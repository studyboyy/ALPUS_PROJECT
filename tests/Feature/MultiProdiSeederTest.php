<?php

use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use App\Models\HomePageSetting;
use App\Models\Prodi;
use App\Models\User;
use App\Services\ProdiProvisioner;
use Database\Seeders\StatistikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('statistik seeder creates complete isolated data for every active prodi', function () {
    $this->seed(StatistikSeeder::class);

    $prodis = Prodi::query()->where('code', '!=', 'ADMIN')->get();
    expect($prodis)->not->toBeEmpty();

    foreach ($prodis as $prodi) {
        expect(DashboardYearStat::withoutGlobalScopes()->where('prodi_id', $prodi->id)->count())->toBe(5)
            ->and(User::query()->where('prodi_id', $prodi->id)->where('role', 'kaprodi')->exists())->toBeTrue();
    }
});

test('new prodi can receive starter data cloned from an existing prodi', function () {
    $this->seed(StatistikSeeder::class);
    $ekonomi = Prodi::query()->create(['code' => 'EKO', 'name' => 'Ekonomi', 'is_active' => true]);

    app(ProdiProvisioner::class)->cloneStarterData($ekonomi);

    expect(DashboardYearStat::withoutGlobalScopes()->where('prodi_id', $ekonomi->id)->count())->toBe(5);
});

test('every seeded prodi has different statistics programs and public content', function () {
    $this->seed(StatistikSeeder::class);
    $prodis = Prodi::query()->where('code', '!=', 'ADMIN')->orderBy('id')->get();

    $signatures = $prodis->map(function (Prodi $prodi): string {
        $stat = DashboardYearStat::withoutGlobalScopes()->where('prodi_id', $prodi->id)->where('year', 2026)->firstOrFail();
        $program = DashboardProgramItem::withoutGlobalScopes()->where('prodi_id', $prodi->id)->orderBy('sort_order')->value('title');
        $home = HomePageSetting::withoutGlobalScopes()->where('prodi_id', $prodi->id)->firstOrFail();

        return md5(json_encode([$stat->kpi, $stat->capaian, $program, $home->contact_email, $home->gallery_items]));
    });

    expect($signatures->unique()->count())->toBe($prodis->count());
});
