<?php

namespace App\Http\Controllers;

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
                'password' => Hash::make($password),
            ]
        );
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
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim((string) $credentials['login']);
        $email = filter_var($loginInput, FILTER_VALIDATE_EMAIL)
            ? $loginInput
            : ($loginInput === $this->adminUsername() ? $this->adminEmail() : $loginInput);

        $attempted = Auth::attempt([
            'email' => $email,
            'password' => $credentials['password'],
        ]);

        if (!$attempted || Auth::user()?->email !== $this->adminEmail()) {
            Auth::logout();

            return back()->withErrors([
                'login' => 'Username/email atau password admin tidak valid.',
            ])->onlyInput('login');
        }

        $request->session()->regenerate();

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
