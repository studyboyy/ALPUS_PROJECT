<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    private function adminUsername(): string
    {
        return (string) env('ADMIN_USERNAME', 'admin');
    }

    private function adminEmail(): string
    {
        return (string) env('ADMIN_EMAIL', 'admin@prodi.local');
    }

    private function ensureAdminAccountExists(): void
    {
        $email = $this->adminEmail();
        $password = (string) env('ADMIN_PASSWORD', 'admin123');
        $name = (string) env('ADMIN_NAME', 'Admin Prodi');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'username' => $this->adminUsername(),
                'role' => 'admin',
                'prodi_id' => Prodi::query()->where('code', 'ADMIN')->value('id'),
                'password' => Hash::make($password),
            ]
        );

        foreach (Prodi::query()->where('code', '!=', 'ADMIN')->where('is_active', true)->get() as $prodi) {
            if (! User::query()->where('prodi_id', $prodi->id)->where('role', 'kaprodi')->exists()) {
                User::query()->create([
                    'username' => 'kaprodi.'.strtolower($prodi->code),
                    'email' => 'kaprodi.'.strtolower($prodi->code).'@prodi.local',
                    'name' => 'Kaprodi '.$prodi->name,
                    'role' => 'kaprodi',
                    'prodi_id' => $prodi->id,
                    'password' => Hash::make((string) env('KAPRODI_PASSWORD', 'kaprodi123')),
                ]);
            }
        }
    }

    public function showLogin(): View|RedirectResponse
    {
        $this->ensureAdminAccountExists();

        if (Auth::check() && Auth::user()?->email === $this->adminEmail()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $this->ensureAdminAccountExists();

        $credentials = $request->validate([
            'prodi_id' => ['required', 'integer', 'exists:prodis,id'],
            'login' => ['required', 'string', 'max:160'],
            'password' => ['required', 'string'],
        ]);

        $identity = strtolower(trim((string) $credentials['login']));
        $user = User::query()
            ->where('prodi_id', (int) $credentials['prodi_id'])
            ->where(function ($query) use ($identity): void {
                $query->whereRaw('LOWER(username) = ?', [$identity])
                    ->orWhereRaw('LOWER(email) = ?', [$identity]);
            })
            ->first();
        $user = $user && Hash::check($credentials['password'], (string) $user->password) ? $user : null;
        $attempted = (bool) $user;

        if (! $attempted) {
            Auth::logout();

            return back()->withErrors([
                'login' => 'Program Studi, username/email, atau password tidak valid.',
            ])->onlyInput('prodi_id', 'login');
        }

        Auth::login($user);
        $request->session()->regenerate();
        if ($user->role !== 'admin' && $user->prodi_id) {
            $request->session()->put('public_prodi_id', $user->prodi_id);
            $request->session()->put('admin_prodi_id', $user->prodi_id);
        } elseif ($user->role === 'admin') {
            $selectedProdiId = Prodi::query()
                ->where('code', '!=', 'ADMIN')
                ->where('is_active', true)
                ->orderBy('name')
                ->value('id');
            $request->session()->put('admin_prodi_id', $selectedProdiId);
            $request->session()->put('public_prodi_id', $selectedProdiId);
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
