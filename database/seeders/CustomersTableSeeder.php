<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Package;
use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    public function run(): void
    {
        $package = Package::first();

        if (! $package) {
            return;
        }

        Customer::updateOrCreate([
            'username' => 'pelanggan1',
        ], [
            'name' => 'Pelanggan 1',
            'password' => 'secret123',
            'package_id' => $package->id,
            'expired_at' => now()->addMonth(),
            'active' => true,
            'status' => 'active',
            'email' => 'pelanggan1@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 1',
        ]);

        Customer::updateOrCreate([
            'username' => 'pelanggan2',
        ], [
            'name' => 'Pelanggan 2 (Expired)',
            'password' => 'secret123',
            'package_id' => $package->id,
            'expired_at' => now()->subDay(), // Sudah expired
            'active' => true,
            'status' => 'active',
            'email' => 'pelanggan2@example.com',
            'phone' => '081234567891',
            'address' => 'Jl. Contoh No. 2',
        ]);
    }
}
