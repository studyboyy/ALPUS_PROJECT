<?php

namespace Database\Seeders;

use App\Models\AnnualReportSection;
use App\Models\DashboardMonthlyStat;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use App\Models\DocumentItem;
use App\Models\HomePageSetting;
use App\Models\Prodi;
use App\Models\ProfileSection;
use App\Models\User;
use App\Services\ProdiDemoDataSeeder;
use Illuminate\Database\Seeder;

class StatistikSeeder extends Seeder
{
    private const DEFAULT_PRODIS = [
        ['code' => 'SI', 'name' => 'Sistem Informasi', 'kaprodi' => 'Rina Kusumawati'],
        ['code' => 'IF', 'name' => 'Informatika', 'kaprodi' => 'Andi Pratama'],
        ['code' => 'EKONOMI', 'name' => 'Ekonomi', 'kaprodi' => 'Maya Sari'],
        ['code' => 'MJ', 'name' => 'Manajemen', 'kaprodi' => 'Dewi Lestari'],
    ];

    public function run(): void
    {
        DashboardMonthlyStat::query()->delete();
        DashboardYearStat::query()->delete();
        DashboardProgramItem::query()->delete();
        AnnualReportSection::query()->delete();
        DocumentItem::query()->delete();
        ProfileSection::query()->delete();
        HomePageSetting::query()->delete();

        $adminProdi = Prodi::query()->updateOrCreate(
            ['code' => 'ADMIN'],
            ['name' => 'Administrator', 'is_active' => true]
        );
        $activeCodes = collect(self::DEFAULT_PRODIS)->pluck('code')->push('ADMIN')->all();
        Prodi::query()->whereNotIn('code', $activeCodes)->update(['is_active' => false]);

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

        $generator = app(ProdiDemoDataSeeder::class);

        foreach (self::DEFAULT_PRODIS as $item) {
            $prodi = Prodi::query()->updateOrCreate(
                ['code' => $item['code']],
                ['name' => $item['name'], 'is_active' => true]
            );

            $generator->seed($prodi, $item['kaprodi'], replace: false);

            User::query()->updateOrCreate(
                ['email' => 'kaprodi.'.strtolower($prodi->code).'@prodi.local'],
                [
                    'username' => 'kaprodi.'.strtolower($prodi->code),
                    'name' => $item['kaprodi'],
                    'role' => 'kaprodi',
                    'prodi_id' => $prodi->id,
                    'password' => (string) env('KAPRODI_PASSWORD', 'kaprodi123'),
                ]
            );
        }

        $this->command?->info('StatistikSeeder selesai: 4 prodi contoh dibuat dengan data dummy berbeda.');
    }
}
