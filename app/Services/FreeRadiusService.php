<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class FreeRadiusService
{
    protected $connection;

    public function __construct()
    {
        $this->connection = DB::connection(config('database.radius_connection'));
    }

    /**
     * Sync customer ke FreeRADIUS
     */
    public function syncCustomer(Customer $customer): void
    {
        $package = $customer->package;

        // Password
        $this->connection->table('radcheck')->updateOrInsert(
            [
                'username' => $customer->username,
                'attribute' => 'Cleartext-Password'
            ],
            [
                'op' => ':=',
                'value' => $customer->password
            ]
        );

        // Bandwidth (Mikrotik)
        $this->connection->table('radreply')->updateOrInsert(
            [
                'username' => $customer->username,
                'attribute' => 'Mikrotik-Rate-Limit'
            ],
            [
                'op' => ':=',
                'value' => $package->rate_limit
            ]
        );

        // Handle expiration
        if ($customer->expired_at) {
            $this->setExpiration($customer);
        } else {
            $this->removeExpiration($customer);
        }

        // Pool group assignment
        if ($customer->poolGroup) {
            $this->connection->table('radreply')->updateOrInsert(
                [
                    'username' => $customer->username,
                    'attribute' => 'Mikrotik-Group',
                ],
                [
                    'op' => ':=',
                    'value' => $customer->poolGroup->name,
                ]
            );
        } else {
            $this->connection->table('radreply')
                ->where('username', $customer->username)
                ->where('attribute', 'Mikrotik-Group')
                ->delete();
        }

        // Pastikan user tidak di-block Auth-Type
        $this->connection->table('radcheck')
            ->where('username', $customer->username)
            ->where('attribute', 'Auth-Type')
            ->delete();
    }

    /**
     * Disable user (langsung expired)
     */
    public function disableCustomer(Customer $customer): void
    {
        $this->connection->table('radreply')->updateOrInsert(
            [
                'username' => $customer->username,
                'attribute' => 'Expiration'
            ],
            [
                'op' => ':=',
                'value' => now()->subDay()->format('d M Y H:i:s')
            ]
        );
    }

    /**
     * Enable user
     */
    public function enableCustomer(Customer $customer): void
    {
        $this->removeExpiration($customer);
    }

    /**
     * Hapus user
     */
    public function deleteCustomer(Customer $customer): void
    {
        $this->connection->table('radcheck')
            ->where('username', $customer->username)
            ->delete();

        $this->connection->table('radreply')
            ->where('username', $customer->username)
            ->delete();
    }

    /**
     * Monitoring trafik user
     */
    public function getTrafficSummary(?string $search = null): array
    {
        // Get all customers
        $customers = \App\Models\Customer::query();
        if ($search) {
            $customers->where('username', 'like', "%{$search}%");
        }
        $customers = $customers->pluck('username')->toArray();

        if (empty($customers)) {
            return [];
        }

        // Get traffic data with left join
        $traffic = $this->connection->table('radacct')
            ->selectRaw('
                radacct.username,
                COALESCE(SUM(radacct.acctinputoctets), 0) as total_download,
                COALESCE(SUM(radacct.acctoutputoctets), 0) as total_upload,
                MAX(CASE WHEN radacct.acctstoptime IS NULL THEN 1 ELSE 0 END) as online
            ')
            ->whereIn('radacct.username', $customers)
            ->groupBy('radacct.username')
            ->get()
            ->keyBy('username');

        // Combine with all customers
        $result = [];
        foreach ($customers as $username) {
            $data = $traffic->get($username);
            $result[] = [
                'username' => $username,
                'download' => $data ? $this->formatBytes($data->total_download) : '0 B',
                'upload' => $data ? $this->formatBytes($data->total_upload) : '0 B',
                'online' => $data ? (bool) $data->online : false,
            ];
        }

        return $result;
    }

    /**
     * Set expired date
     */
    protected function setExpiration(Customer $customer): void
    {
        if (! $customer->expired_at) return;

        $this->connection->table('radreply')->updateOrInsert(
            [
                'username' => $customer->username,
                'attribute' => 'Expiration'
            ],
            [
                'op' => ':=',
                'value' => $customer->expired_at->format('d M Y H:i:s')
            ]
        );
    }

    /**
     * Hapus expiration
     */
    protected function removeExpiration(Customer $customer): void
    {
        $this->connection->table('radreply')
            ->where('username', $customer->username)
            ->where('attribute', 'Expiration')
            ->delete();
    }

    /**
     * Get detailed traffic for a specific user
     */
    public function getUserTrafficDetail(string $username): array
    {
        $sessions = $this->connection->table('radacct')
            ->where('username', $username)
            ->orderBy('acctstarttime', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'session_id' => $session->radacctid,
                    'start_time' => $session->acctstarttime,
                    'stop_time' => $session->acctstoptime,
                    'session_time' => $session->acctsessiontime,
                    'download' => $session->acctinputoctets,
                    'upload' => $session->acctoutputoctets,
                    'total' => $session->acctinputoctets + $session->acctoutputoctets,
                    'download_formatted' => $this->formatBytes($session->acctinputoctets),
                    'upload_formatted' => $this->formatBytes($session->acctoutputoctets),
                    'total_formatted' => $this->formatBytes($session->acctinputoctets + $session->acctoutputoctets),
                    'nas_ip' => $session->nasipaddress,
                    'framed_ip' => $session->framedipaddress,
                ];
            });

        $totalDownload = $sessions->sum('download');
        $totalUpload = $sessions->sum('upload');
        $totalTraffic = $sessions->sum('total');

        return [
            'username' => $username,
            'total_sessions' => $sessions->count(),
            'total_download' => $this->formatBytes($totalDownload),
            'total_upload' => $this->formatBytes($totalUpload),
            'total_traffic' => $this->formatBytes($totalTraffic),
            'sessions' => $sessions,
        ];
    }

    /**
     * Format bytes
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        if ($bytes == 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = log($bytes, 1024);
        $index = floor($base);

        return round(pow(1024, $base - $index), $precision) . ' ' . $units[$index];
    }
}