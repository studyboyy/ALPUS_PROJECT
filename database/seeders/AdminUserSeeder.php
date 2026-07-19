<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminProdi = Prodi::query()->updateOrCreate(
            ['code' => 'ADMIN'],
            ['name' => 'Administrator', 'is_active' => true]
        );

        User::query()->updateOrCreate(
            ['email' => (string) env('ADMIN_EMAIL', 'admin@prodi.local')],
            [
                'name' => (string) env('ADMIN_NAME', 'Admin Pusat'),
                'username' => (string) env('ADMIN_USERNAME', 'admin'),
                'role' => 'admin',
                'prodi_id' => $adminProdi->id,
                'password' => (string) env('ADMIN_PASSWORD', 'admin123'),
            ]
        );
    }
}
