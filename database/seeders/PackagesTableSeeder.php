<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackagesTableSeeder extends Seeder
{
    public function run(): void
    {
        Package::updateOrCreate(['name' => 'Paket Basic'], [
            'download_speed' => 2048,
            'upload_speed' => 512,
            'price' => 100000,
            'rate_limit' => '2048k/512k',
            'description' => 'Paket internet dasar untuk pelanggan kecil.',
        ]);

        Package::updateOrCreate(['name' => 'Paket Premium'], [
            'download_speed' => 8192,
            'upload_speed' => 1024,
            'price' => 250000,
            'rate_limit' => '8192k/1024k',
            'description' => 'Paket internet cepat untuk kebutuhan rumahan dan usaha kecil.',
        ]);
    }
}
