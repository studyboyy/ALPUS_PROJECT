<?php

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest can select an active public prodi', function () {
    $prodi = Prodi::query()->create(['code' => 'TI', 'name' => 'Teknik Informatika', 'is_active' => true]);

    $this->post(route('public.prodi.select'), ['prodi_id' => $prodi->id])
        ->assertRedirect()
        ->assertSessionHas('public_prodi_id', $prodi->id);
});

test('kaprodi can switch public portal to another prodi', function () {
    $si = Prodi::query()->create(['code' => 'SI2', 'name' => 'Sistem Informasi', 'is_active' => true]);
    $if = Prodi::query()->create(['code' => 'IF2', 'name' => 'Informatika', 'is_active' => true]);
    $user = User::factory()->create(['role' => 'kaprodi', 'prodi_id' => $si->id]);

    $this->actingAs($user)
        ->post(route('public.prodi.select'), ['prodi_id' => $if->id])
        ->assertRedirect()
        ->assertSessionHas('public_prodi_id', $if->id);
});
