<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@isp.local',
        ], [
            'name' => 'Admin ISP',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
        ]);

        User::updateOrCreate([
            'email' => 'tech@isp.local',
        ], [
            'name' => 'Teknisi ISP',
            'password' => 'password',
            'role' => User::ROLE_TECHNICIAN,
        ]);
    }
}
