<?php

use App\Livewire\Pages\AdminProfilePage;
use App\Models\ProfileSection;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin profile tabs isolate sections by selected prodi', function () {
    $adminProdi = Prodi::query()->where('code', 'ADMIN')->firstOrFail();
    $si = Prodi::query()->where('code', 'SI')->firstOrFail();
    $if = Prodi::query()->where('code', 'IF')->firstOrFail();
    $admin = User::factory()->create(['role' => 'admin', 'prodi_id' => $adminProdi->id]);

    ProfileSection::withoutGlobalScopes()->create([
        'prodi_id' => $si->id, 'slug' => 'sejarah-visi-misi', 'title' => 'Sejarah SI',
        'summary' => 'Ringkasan khusus SI', 'full_content' => 'Konten SI', 'sort_order' => 1,
    ]);
    ProfileSection::withoutGlobalScopes()->create([
        'prodi_id' => $if->id, 'slug' => 'sejarah-visi-misi', 'title' => 'Sejarah IF',
        'summary' => 'Ringkasan khusus IF', 'full_content' => 'Konten IF', 'sort_order' => 1,
    ]);

    Livewire::actingAs($admin)
        ->test(AdminProfilePage::class)
        ->call('pilihProdi', $si->id)
        ->assertSee('Ringkasan khusus SI')
        ->assertDontSee('Ringkasan khusus IF');
});
