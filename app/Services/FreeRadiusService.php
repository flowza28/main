<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Package;
use Illuminate\Support\Facades\DB;

class FreeRadiusService
{
    protected $connection;

    public function __construct()
    {
        $this->connection = DB::connection(config('database.radius_connection'));
    }

    public function syncCustomer(Customer $customer): void
    {
        $package = $customer->package;

        $this->connection->table('radcheck')->updateOrInsert(
            ['username' => $customer->username, 'attribute' => 'Cleartext-Password'],
            ['op' => ':=', 'value' => $customer->password, 'updated_at' => now(), 'created_at' => now()] 
        );

        $this->connection->table('radreply')->updateOrInsert(
            ['username' => $customer->username, 'attribute' => 'Mikrotik-Rate-Limit'],
            ['op' => ':=', 'value' => $package->rate_limit, 'updated_at' => now(), 'created_at' => now()]
        );

        if (! $customer->active) {
            $this->disableCustomer($customer);
        }
    }

    public function disableCustomer(Customer $customer): void
    {
        $this->connection->table('radcheck')->updateOrInsert(
            ['username' => $customer->username, 'attribute' => 'Auth-Type'],
            ['op' => ':=', 'value' => 'Reject', 'updated_at' => now(), 'created_at' => now()]
        );
    }

    public function enableCustomer(Customer $customer): void
    {
        $this->connection->table('radcheck')
            ->where('username', $customer->username)
            ->where('attribute', 'Auth-Type')
            ->delete();
    }

    public function deleteCustomer(Customer $customer): void
    {
        $this->connection->table('radcheck')->where('username', $customer->username)->delete();
        $this->connection->table('radreply')->where('username', $customer->username)->delete();
    }

    public function getTrafficSummary(?string $search = null): array
    {
        $query = $this->connection->table('radacct')
            ->selectRaw('username, SUM(acctinputoctets) as total_download, SUM(acctoutputoctets) as total_upload, MAX(acctstoptime IS NULL) as online')
            ->groupBy('username');

        if ($search) {
            $query->where('username', 'like', "%{$search}%");
        }

        return $query->get()->map(function ($row) {
            return [
                'username' => $row->username,
                'download' => $row->total_download,
                'upload' => $row->total_upload,
                'online' => (bool) $row->online,
            ];
        })->toArray();
    }
}
