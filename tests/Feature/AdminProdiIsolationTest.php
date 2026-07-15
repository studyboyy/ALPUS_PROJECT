<?php

use App\Models\DashboardYearStat;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('kaprodi admin panel stays on own prodi after browsing another public prodi', function () {
    $si = Prodi::query()->where('code', 'SI')->firstOrFail();
    $if = Prodi::query()->where('code', 'IF')->firstOrFail();
    $user = User::factory()->create(['role' => 'kaprodi', 'prodi_id' => $si->id]);

    $payload = fn (int $value): array => [
        'kpi' => [
            ['label' => 'Mahasiswa Aktif', 'value' => $value, 'decimals' => 0],
            ['label' => 'IPK Rata-rata', 'value' => 3.5, 'decimals' => 2],
            ['label' => 'Dosen Tetap', 'value' => 10, 'decimals' => 0],
            ['label' => 'Publikasi', 'value' => 20, 'decimals' => 0],
        ],
        'trend' => [],
        'capaian' => [],
    ];
    DashboardYearStat::withoutGlobalScopes()->create(['prodi_id' => $si->id, 'year' => 2026, ...$payload(1234567)]);
    DashboardYearStat::withoutGlobalScopes()->create(['prodi_id' => $if->id, 'year' => 2026, ...$payload(987654321)]);

    $this->actingAs($user)
        ->withSession(['public_prodi_id' => $if->id])
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('1.234.567')
        ->assertDontSee('987.654.321');
});
