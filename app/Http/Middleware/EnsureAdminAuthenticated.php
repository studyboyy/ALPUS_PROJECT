<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminEmail = (string) env('ADMIN_EMAIL', 'admin@prodi.local');

        if (Auth::check() && Auth::user()?->email === $adminEmail) {
            return $next($request);
        }

        return redirect()->route('admin.login');
    }
}
