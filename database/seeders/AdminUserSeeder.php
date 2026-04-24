<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => (string) env('ADMIN_EMAIL', 'admin@prodi.local')],
            [
                'name' => (string) env('ADMIN_NAME', 'Admin Prodi'),
                'password' => (string) env('ADMIN_PASSWORD', 'admin123'),
            ]
        );
    }
}
