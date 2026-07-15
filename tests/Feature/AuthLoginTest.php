<?php

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can login with prodi username and password', function () {
    $prodi = Prodi::query()->where('code', 'SI')->firstOrFail();
    $user = User::factory()->create([
        'username' => 'kaprodi.si.test',
        'email' => 'kaprodi.si.test@unwari.ac.id',
        'role' => 'kaprodi',
        'prodi_id' => $prodi->id,
        'password' => 'rahasia123',
    ]);

    $this->post(route('admin.login.submit'), [
        'prodi_id' => $prodi->id,
        'login' => 'kaprodi.si.test',
        'password' => 'rahasia123',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('user can login with email and cannot login under another prodi', function () {
    $si = Prodi::query()->where('code', 'SI')->firstOrFail();
    $if = Prodi::query()->where('code', 'IF')->firstOrFail();
    $user = User::factory()->create([
        'username' => 'sekprodi.si.test',
        'email' => 'sekprodi.si.test@unwari.ac.id',
        'role' => 'sekprodi',
        'prodi_id' => $si->id,
        'password' => 'rahasia456',
    ]);

    $this->post(route('admin.login.submit'), [
        'prodi_id' => $if->id,
        'login' => $user->email,
        'password' => 'rahasia456',
    ])->assertSessionHasErrors('login');

    $this->post(route('admin.login.submit'), [
        'prodi_id' => $si->id,
        'login' => strtoupper($user->email),
        'password' => 'rahasia456',
    ])->assertRedirect(route('admin.dashboard'));
});
