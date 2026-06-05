<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Jalankan: php artisan db:seed
     * Atau fresh: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call(StatistikSeeder::class);
    }
}
