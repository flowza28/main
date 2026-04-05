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
        $query = $this->connection->table('radacct')
            ->selectRaw('
                username,
                SUM(acctinputoctets) as total_download,
                SUM(acctoutputoctets) as total_upload,
                MAX(CASE WHEN acctstoptime IS NULL THEN 1 ELSE 0 END) as online
            ')
            ->groupBy('username');

        if ($search) {
            $query->where('username', 'like', "%{$search}%");
        }

        return $query->get()->map(function ($row) {
            return [
                'username' => $row->username,
                'download' => $this->formatBytes($row->total_download),
                'upload' => $this->formatBytes($row->total_upload),
                'online' => (bool) $row->online,
            ];
        })->toArray();
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