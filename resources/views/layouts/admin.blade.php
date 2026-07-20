<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $isCentralAdminForTitle = auth()->user()?->role === 'admin';
        $rolePageTitle = isset($title) ? (string) $title : 'Dashboard';
        if (! $isCentralAdminForTitle) {
            $rolePageTitle = trim((string) preg_replace('/\bAdmin\b\s*/i', '', $rolePageTitle));
        }
    @endphp
    <title>{{ $rolePageTitle }} — {{ $isCentralAdminForTitle ? 'Admin Panel' : 'Kaprodi' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Lora:wght@600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --adm-bg:      #f6f7fb;
            --adm-card:    #ffffff;
            --adm-ink:     #18181b;
            --adm-muted:   #71717a;
            --adm-line:    #e4e4e7;
            --adm-accent:  #6366f1;
            --adm-soft:    #eef2ff;
            --adm-sidebar: #ffffff;
            /* portal variable aliases — needed for bg-(--accent) etc. in admin pages */
            --accent:      #6366f1;
            --accent-soft: #eef2ff;
            --line:        #e4e4e7;
            --muted:       #71717a;
            --ink:         #18181b;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html { height: 100%; }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif;
            background: var(--adm-bg);
            color: var(--adm-ink);
            min-height: 100%;
            -webkit-font-smoothing: antialiased;
        }

        .display-font { font-family: 'Lora', Georgia, serif; }

        /* ── Sidebar ── */
        .adm-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 50;
            width: 224px;
            background: var(--adm-sidebar);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .adm-sidebar::-webkit-scrollbar { width: 4px; }
        .adm-sidebar::-webkit-scrollbar-thumb { background: #e4e4e7; border-radius: 99px; }

        .adm-brand {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 1.25rem 1rem 1rem;
            border-bottom: 1px solid var(--adm-line);
        }

        .adm-brand-icon {
            flex-shrink: 0;
            width: 2rem; height: 2rem;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #6366f1, #818cf8);
            display: grid; place-items: center;
            box-shadow: 0 2px 8px rgba(99,102,241,.25);
        }

        .adm-brand-name {
            font-size: 0.8125rem;
            font-weight: 700;
            color: #18181b;
            line-height: 1.25;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .adm-brand-sub {
            font-size: 0.6875rem;
            color: #a1a1aa;
            font-weight: 500;
        }

        .adm-nav { flex: 1; padding: 0.75rem 0.625rem; }

        .adm-nav-group { margin-bottom: 1.5rem; }

        .adm-nav-label {
            padding: 0 0.5rem 0.375rem;
            font-size: 0.625rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #a1a1aa;
        }

        .adm-link {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            border-radius: 0.5rem;
            padding: 0.5rem 0.625rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #52525b;
            transition: background 0.12s, color 0.12s;
            position: relative;
            text-decoration: none;
        }

        .adm-link:hover {
            background: #f4f4f5;
            color: #18181b;
        }

        .adm-link.active {
            background: #eef2ff;
            color: #4338ca;
            font-weight: 700;
        }

        .adm-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 25%; bottom: 25%;
            width: 3px;
            border-radius: 999px;
            background: #6366f1;
        }

        .adm-link svg { flex-shrink: 0; opacity: .6; }
        .adm-link.active svg { opacity: 1; color: #6366f1; }
        .adm-link:hover svg { opacity: .8; }

        .adm-footer {
            padding: 0.75rem 0.625rem 1rem;
            border-top: 1px solid var(--adm-line);
        }

        /* ── Main ── */
        .adm-main {
            margin-left: 224px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ── */
        .adm-topbar {
            position: sticky;
            top: 0;
            z-index: 30;
            background: rgba(246,247,251,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--adm-line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.75rem 1.5rem;
        }

        .adm-topbar-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--adm-ink);
        }

        .adm-topbar-action {
            display: grid;
            height: 2.25rem; width: 2.25rem;
            place-items: center;
            border-radius: 0.5rem;
            border: 1px solid var(--adm-line);
            background: #fff;
            color: #71717a;
            transition: background 0.12s, border-color 0.12s;
            cursor: pointer;
        }
        .adm-topbar-action:hover {
            background: var(--adm-soft);
            border-color: #a5b4fc;
            color: var(--adm-accent);
        }

        .adm-avatar {
            display: grid;
            height: 2rem; width: 2rem;
            place-items: center;
            border-radius: 999px;
            background: linear-gradient(135deg, #6366f1, #818cf8);
            font-size: 0.6875rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        /* ── Content area ── */
        .adm-content { flex: 1; padding: 1.5rem; }

        /* ── Cards ── */
        .section-box {
            background: var(--adm-card);
            border: 1px solid var(--adm-line);
            box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 4px 16px rgba(0,0,0,.04);
        }

        /* Admin forms */
        .adm-field-label {
            display: block;
            margin-bottom: .45rem;
            font-size: .75rem;
            font-weight: 700;
            color: #3f3f46;
        }

        .adm-input {
            display: block;
            width: 100%;
            min-height: 2.75rem;
            border: 1px solid #d4d4d8;
            border-radius: .75rem;
            background: #fff;
            padding: .7rem .85rem;
            color: #18181b;
            font-size: .8125rem;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }

        .adm-input::placeholder { color: #a1a1aa; }
        .adm-input:hover { border-color: #a1a1aa; }
        .adm-input:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }

        .adm-input.is-invalid {
            border-color: #fda4af;
            background: #fff7f7;
        }

        .adm-btn-primary, .adm-btn-secondary {
            display: inline-flex;
            min-height: 2.625rem;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            border-radius: .75rem;
            padding: .65rem 1rem;
            font-size: .8125rem;
            font-weight: 700;
            transition: transform .15s, background .15s, border-color .15s, box-shadow .15s;
        }

        .adm-btn-primary {
            border: 1px solid #4f46e5;
            background: #4f46e5;
            color: #fff;
            box-shadow: 0 4px 12px rgba(79,70,229,.2);
        }
        .adm-btn-primary:hover { background: #4338ca; box-shadow: 0 6px 16px rgba(79,70,229,.25); }
        .adm-btn-secondary { border: 1px solid #d4d4d8; background: #fff; color: #52525b; }
        .adm-btn-secondary:hover { border-color: #a5b4fc; background: #eef2ff; color: #4338ca; }
        .adm-btn-primary:active, .adm-btn-secondary:active { transform: scale(.98); }
        .adm-btn-primary:disabled, .adm-btn-secondary:disabled { cursor: wait; opacity: .65; }

        /* ── Toast ── */
        .admin-toast {
            position: fixed; top: 1rem; right: 1rem;
            z-index: 200;
            max-width: 340px;
            border-radius: 0.75rem;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            padding: 0.875rem 1.125rem;
            box-shadow: 0 12px 32px rgba(22,101,52,.2);
            opacity: 0; transform: translateY(-8px);
            pointer-events: none;
            transition: opacity .3s ease, transform .3s ease;
        }
        .admin-toast.show { opacity: 1; transform: translateY(0); }
        .admin-toast.hide { opacity: 0; transform: translateY(-8px); }

        @media (max-width: 1023px) {
            .adm-sidebar {
                position: fixed;
                top: 0; left: 0; bottom: 0;
                width: 224px;
                z-index: 100;
                transform: translateX(-100%);
                transition: transform 0.25s cubic-bezier(0.22, 1, 0.36, 1);
                box-shadow: 4px 0 24px rgba(0,0,0,.12);
            }
            .adm-sidebar.open { transform: translateX(0); }
            .adm-main { margin-left: 0; }
            .adm-overlay {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,.35);
                backdrop-filter: blur(2px);
                z-index: 99;
            }
            .adm-overlay.show { display: block; }
        }
    </style>
</head>

<body>
    @php
        $adminHomeContent = \App\Models\HomePageSetting::current();
        $adminProdiName   = trim((string) ($adminHomeContent['header_logo_label'] ?? 'Program Studi'));
        $adminInitials    = collect(explode(' ', $adminProdiName))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->implode('');
        if ($adminInitials === '') { $adminInitials = 'PS'; }
        $adminUser        = auth()->user();
        $isCentralAdmin   = $adminUser?->role === 'admin';
        $isProdiManager   = in_array($adminUser?->role, ['kaprodi', 'sekprodi'], true);
        $adminProdis       = $isCentralAdmin
            ? \App\Models\Prodi::query()->where('code', '!=', 'ADMIN')->where('is_active', true)->orderBy('name')->get()
            : collect();
        $selectedAdminProdi = $adminProdis->firstWhere('id', (int) session('admin_prodi_id'));
        $adminPageTitle   = $rolePageTitle;
    @endphp

    @if (session()->has('status'))
        <div id="admin-toast" class="admin-toast" role="status" aria-live="polite"
            data-toast-message="{{ session('status') }}">
            <p class="text-sm font-semibold">Tersimpan</p>
            <p class="mt-0.5 text-xs text-emerald-700/90">{{ session('status') }}</p>
        </div>
    @endif

    {{-- Mobile overlay --}}
    <div id="adm-overlay" class="adm-overlay" onclick="closeAdminSidebar()"></div>

    {{-- ── Sidebar ── --}}
    <aside class="adm-sidebar" id="adm-sidebar">
        {{-- Brand --}}
        <div class="adm-brand">
            <div class="adm-brand-icon">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="adm-brand-name">{{ $adminProdiName }}</p>
                <p class="adm-brand-sub">{{ $isCentralAdmin ? 'Admin Panel' : 'Kaprodi' }}</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="adm-nav">

        

            <div class="adm-nav-group">
                <p class="adm-nav-label">Utama</p>
                <a wire:navigate href="{{ route('admin.dashboard') }}"
                    class="adm-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
            </div>

            <div class="adm-nav-group">
                <p class="adm-nav-label">{{ $isCentralAdmin ? 'Manajemen Sistem' : 'Data Prodi' }}</p>
                @if ($isProdiManager || $isCentralAdmin)
                    <a wire:navigate href="{{ route('admin.dashboard-data') }}"
                        class="adm-link {{ request()->routeIs('admin.dashboard-data') ? 'active' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Kelola Statistik
                    </a>
                    <a wire:navigate href="{{ route('admin.monthly-stats') }}"
                        class="adm-link {{ request()->routeIs('admin.monthly-stats') ? 'active' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Statistik Bulanan
                    </a>
                @endif
                @if ($isCentralAdmin)
                    <a wire:navigate href="{{ route('admin.users') }}"
                        class="adm-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7-10a4 4 0 100-8 4 4 0 000 8zm11 10v-2a4 4 0 00-3-3.87m-1-11.26a4 4 0 010 7.75"/>
                        </svg>
                        User &amp; Program Studi
                    </a>
                @endif
            </div>

            @if ($isProdiManager || $isCentralAdmin)
            <div class="adm-nav-group">
                <p class="adm-nav-label">Konten Prodi</p>
                <a wire:navigate href="{{ route('admin.program-agenda') }}"
                    class="adm-link {{ request()->routeIs('admin.program-agenda') ? 'active' : '' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Program &amp; Agenda
                </a>
                <a wire:navigate href="{{ route('admin.annual-report') }}"
                    class="adm-link {{ request()->routeIs('admin.annual-report') ? 'active' : '' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Laporan Tahunan
                </a>
                <a wire:navigate href="{{ route('admin.beranda-content') }}"
                    class="adm-link {{ request()->routeIs('admin.beranda-content') ? 'active' : '' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Konten Beranda
                </a>
                <a wire:navigate href="{{ route('admin.documents') }}"
                    class="adm-link {{ request()->routeIs('admin.documents') ? 'active' : '' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 3v6h6"/>
                    </svg>
                    Dokumen Publik
                </a>
                <a wire:navigate href="{{ route('admin.profile') }}"
                    class="adm-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profil Prodi
                </a>
                <a wire:navigate href="{{ route('admin.feedback') }}"
                    class="adm-link {{ request()->routeIs('admin.feedback') ? 'active' : '' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Umpan Balik
                </a>
            </div>
            @endif

            <div class="adm-nav-group">
                <p class="adm-nav-label">Lainnya</p>
                <a wire:navigate href="{{ route('home') }}" target="_blank"
                    class="adm-link">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Lihat Portal
                </a>
                <a href="{{ route('laporan.pdf') }}" class="adm-link" target="_blank" rel="noopener">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export PDF
                </a>
            </div>

        </nav>

        {{-- Footer: logout --}}
        <div class="adm-footer">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="adm-link w-full">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main ── --}}
    <div class="adm-main">

        {{-- Topbar --}}
        <header class="adm-topbar">
            <div class="flex items-center gap-3">
                {{-- Mobile hamburger --}}
                <button type="button" onclick="openAdminSidebar()"
                    class="adm-topbar-action lg:hidden" aria-label="Buka menu">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="adm-topbar-title">{{ $adminPageTitle }}</span>
            </div>
            <div class="flex items-center gap-2">
                @if ($isCentralAdmin && $adminProdis->isNotEmpty())
                    <form method="POST" action="{{ route('admin.prodi.select') }}" class="hidden items-center gap-2 md:flex">
                        @csrf
                        <label for="admin-prodi-selector" class="sr-only">Pilih program studi</label>
                        <select id="admin-prodi-selector" name="prodi_id" onchange="this.form.submit()"
                            class="h-9 max-w-[220px] rounded-xl border border-zinc-200 bg-white px-3 text-xs font-semibold text-zinc-700 shadow-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                            @foreach ($adminProdis as $prodi)
                                <option value="{{ $prodi->id }}" @selected($selectedAdminProdi?->id === $prodi->id)>{{ $prodi->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
                <a wire:navigate href="{{ route('home') }}" target="_blank"
                    class="adm-topbar-action" title="Buka Portal">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
                <div class="flex items-center gap-2.5 rounded-xl border border-zinc-200 bg-white px-3 py-1.5 shadow-sm">
                    <div class="adm-avatar">{{ $adminInitials }}</div>
                    <div class="hidden text-left sm:block">
                        <p class="text-xs font-bold text-zinc-800 leading-tight">{{ $isCentralAdmin ? 'Admin' : 'Kaprodi' }}</p>
                        <p class="text-[10px] text-zinc-400 leading-tight">{{ $isCentralAdmin ? ($selectedAdminProdi?->name ?? $adminProdiName) : $adminProdiName }}</p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <div class="adm-content">
            {{ $slot }}
        </div>
    </div>

    @livewireScriptConfig
    <script data-navigate-once>
    (function () {
        let hideT, removeT;
        const renderToast = (msg) => {
            let el = document.getElementById('admin-toast');
            if (!el) {
                el = document.createElement('div');
                el.id = 'admin-toast';
                el.className = 'admin-toast';
                el.setAttribute('role', 'status');
                el.setAttribute('aria-live', 'polite');
                document.body.appendChild(el);
            }
            el.innerHTML = `<p class="text-sm font-semibold">Tersimpan</p><p class="mt-0.5 text-xs text-emerald-700/90">${msg || 'Perubahan berhasil disimpan.'}</p>`;
            el.classList.remove('hide');
            requestAnimationFrame(() => el.classList.add('show'));
            clearTimeout(hideT); clearTimeout(removeT);
            hideT   = setTimeout(() => { el.classList.remove('show'); el.classList.add('hide'); }, 3200);
            removeT = setTimeout(() => el.remove(), 3800);
        };
        const init = document.getElementById('admin-toast');
        if (init) renderToast(init.dataset.toastMessage);
        document.addEventListener('livewire:init', () => {
            Livewire.on('admin-toast', (e) => {
                const p = Array.isArray(e) ? e[0] : e;
                renderToast(p?.message);
            });
        });
    })();

    // ── Mobile sidebar ──
    function openAdminSidebar() {
        document.getElementById('adm-sidebar').classList.add('open');
        document.getElementById('adm-overlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeAdminSidebar() {
        document.getElementById('adm-sidebar').classList.remove('open');
        document.getElementById('adm-overlay').classList.remove('show');
        document.body.style.overflow = '';
    }
    // Close on navigate
    document.addEventListener('livewire:navigated', closeAdminSidebar);
    </script>
</body>
</html>
