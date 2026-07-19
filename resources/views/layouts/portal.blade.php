<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $publicProdis = \Illuminate\Support\Facades\Schema::hasTable('prodis')
            ? \App\Models\Prodi::query()
                ->where('code', '!=', 'ADMIN')
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
            : collect();
        $authUser = auth()->user();
        $candidatePublicProdiId = (int) (session('public_prodi_id')
            ?: (($authUser && $authUser->role !== 'admin') ? $authUser->prodi_id : null)
            ?: $publicProdis->first()?->id);
        $selectedPublicProdiId = $publicProdis->contains('id', $candidatePublicProdiId)
            ? $candidatePublicProdiId
            : (int) $publicProdis->first()?->id;
        $selectedPublicProdi = $publicProdis->firstWhere('id', $selectedPublicProdiId) ?: $publicProdis->first();
        if ($selectedPublicProdi) {
            session(['public_prodi_id' => $selectedPublicProdi->id]);
        }
        $homeContent = \App\Models\HomePageSetting::current();
        $publicProdiOptions = $publicProdis->map(fn ($prodi) => [
            'id' => (int) $prodi->id,
            'code' => $prodi->code,
            'name' => $prodi->name,
        ])->values();
        $routeName = request()->route()?->getName();
        $resolvedProgramName = trim((string) ($selectedPublicProdi?->name ?: ($homeContent['header_logo_label'] ?? 'Program Studi')));
        $resolvedHeaderTitle = str_replace(
            '[Nama Prodi]',
            $resolvedProgramName,
            (string) ($homeContent['header_title_text'] ?? ''),
        );
        $metaTitle = (isset($title) ? $title . ' - ' : '') . ($resolvedHeaderTitle ?: 'Laporan Tahunan Program Studi');
        $metaDescription = match (true) {
            request()->routeIs('home')
                => 'Dashboard interaktif berisi statistik tahunan, capaian kinerja, program unggulan, dan kanal kontak Program Studi.',
            request()->routeIs('profil*')
                => 'Profil Program Studi, capaian institusi, dan informasi strategis untuk laporan tahunan.',
            request()->routeIs('laporan*')
                => 'Ringkasan laporan tahunan Program Studi lengkap dengan indikator capaian dan unduhan PDF.',
            request()->routeIs('statistik*')
                => 'Halaman data dan statistik Program Studi yang menampilkan indikator tahunan secara dinamis.',
            request()->routeIs('dokumen*')
                => 'Dokumen resmi Program Studi untuk kebutuhan akreditasi, evaluasi, dan tata kelola.',
            request()->routeIs('galeri*')
                => 'Galeri kegiatan akademik dan non-akademik sebagai dokumentasi Program Studi.',
            request()->routeIs('kontak*')
                => 'Kontak sekretariat Program Studi dan formulir umpan balik untuk pengguna portal.',
            request()->routeIs('admin.dashboard-data')
                => 'Panel admin untuk mengelola data statistik tahunan dan program unggulan dashboard.',
            default => 'Portal laporan tahunan Program Studi dengan data kinerja, statistik, dokumen, dan galeri.',
        };
        $metaImage = $homeContent['hero_background_url'] ?: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80';
        $metaUrl = url()->current();
    @endphp

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $metaUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $metaUrl }}">
    <meta property="og:image" content="{{ $metaImage }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Lora:ital,wght@0,600;0,700;1,600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --bg-main: #f0f4fa;
            --bg-card: #ffffff;
            --ink: #0d1b2e;
            --muted: #64748b;
            --accent: #2563eb;
            --accent-dark: #1d4ed8;
            --accent-soft: #eff6ff;
            --line: #e2e8f0;
            --olive: #0f766e;
            --radius-card: 1.25rem;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif;
            background: #ffffff;
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
        }

        .display-font { font-family: 'Lora', Georgia, serif; }

        .section-box {
            background: var(--bg-card);
            border: 1px solid var(--line);
            box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 24px rgba(15,31,55,.06);
        }

        .logo-badge {
            width: 2.5rem; height: 2.5rem;
            border-radius: 0.8rem;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: grid; place-items: center;
            color: #fff; font-weight: 800; font-size: 0.8rem;
            letter-spacing: 0.04em;
            box-shadow: 0 4px 12px rgba(37,99,235,.35);
        }

        .nav-link {
            padding: 0.4rem 0.75rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            transition: color .18s ease, background-color .18s ease;
        }
        .nav-link:hover { color: var(--accent); background: var(--accent-soft); }
        .nav-link.active {
            color: var(--accent);
            background: var(--accent-soft);
        }

        /* Inputs & textareas */
        .form-input {
            width: 100%;
            border-radius: 0.75rem;
            border: 1.5px solid var(--line);
            background: #fff;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            font-family: inherit;
            color: var(--ink);
            transition: border-color .18s ease, box-shadow .18s ease;
            outline: none;
        }
        .form-input::placeholder { color: #94a3b8; }
        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }

        /* Buttons */
        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #fff;
            padding: 0.75rem 1.75rem;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 14px rgba(37,99,235,.28);
            transition: transform .18s ease, box-shadow .18s ease, opacity .18s ease;
            cursor: pointer; border: none;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(37,99,235,.36); }
        .btn-primary:active { transform: translateY(0); opacity: .92; }

        .btn-outline {
            display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
            border-radius: 999px;
            border: 1.5px solid var(--line);
            background: #fff;
            color: #374151;
            padding: 0.7rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            transition: border-color .18s ease, background .18s ease, color .18s ease;
            cursor: pointer;
        }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-soft); }

        /* Pill filters */
        .pill-active   { background: var(--accent); color: #fff; box-shadow: 0 2px 8px rgba(37,99,235,.25); }
        .pill-inactive { background: #fff; color: #475569; border: 1.5px solid var(--line); }
        .pill-inactive:hover { border-color: var(--accent); color: var(--accent); }

        /* â”€â”€ Aurora orbs â”€â”€ */
        .aurora-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.45;
            pointer-events: none;
            z-index: 0;
            will-change: transform;
        }
        .aurora-orb-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, #bfdbfe, #93c5fd, transparent 70%);
            top: -120px; left: -160px;
            animation: orb-drift-1 18s ease-in-out infinite alternate;
        }
        .aurora-orb-2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #bfdbfe, #60a5fa, transparent 70%);
            top: 10vh; right: -140px;
            animation: orb-drift-2 22s ease-in-out infinite alternate;
        }
        .aurora-orb-3 {
            width: 420px; height: 420px;
            background: radial-gradient(circle, #dbeafe, #93c5fd, transparent 70%);
            bottom: 15vh; left: 20%;
            animation: orb-drift-3 26s ease-in-out infinite alternate;
        }
        .aurora-orb-4 {
            width: 360px; height: 360px;
            background: radial-gradient(circle, #eff6ff, #bfdbfe, transparent 70%);
            bottom: -80px; right: 10%;
            animation: orb-drift-4 20s ease-in-out infinite alternate;
        }
        @keyframes orb-drift-1 {
            0%   { transform: translate(0, 0) scale(1); }
            33%  { transform: translate(60px, 80px) scale(1.08); }
            66%  { transform: translate(-40px, 120px) scale(0.95); }
            100% { transform: translate(80px, 40px) scale(1.05); }
        }
        @keyframes orb-drift-2 {
            0%   { transform: translate(0, 0) scale(1); }
            33%  { transform: translate(-80px, 60px) scale(1.1); }
            66%  { transform: translate(-40px, -80px) scale(0.92); }
            100% { transform: translate(-100px, 40px) scale(1.06); }
        }
        @keyframes orb-drift-3 {
            0%   { transform: translate(0, 0) scale(1); }
            50%  { transform: translate(100px, -60px) scale(1.12); }
            100% { transform: translate(-60px, -100px) scale(0.94); }
        }
        @keyframes orb-drift-4 {
            0%   { transform: translate(0, 0) scale(1); }
            40%  { transform: translate(-70px, -50px) scale(1.08); }
            100% { transform: translate(60px, -90px) scale(0.97); }
        }

        /* â”€â”€ Page transition â”€â”€ */
        #page-content {
            animation: page-enter 0.32s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes page-enter {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .page-leaving {
            opacity: 0 !important;
            transform: translateY(-8px) !important;
            transition: opacity 0.18s ease, transform 0.18s ease !important;
        }

        /* Progress bar */
        #lw-progress {
            position: fixed; top: 0; left: 0;
            width: 0; height: 3px;
            background: linear-gradient(90deg, #60a5fa, #2563eb);
            box-shadow: 0 0 10px rgba(37,99,235,.55);
            z-index: 200; opacity: 0;
            transition: width .25s ease, opacity .2s ease;
        }
        #lw-progress.is-loading { opacity: 1; width: 78%; }
        #lw-progress.is-done    { opacity: 1; width: 100%; }
    </style>
</head>

<body>
    {{-- Aurora orbs background --}}
    <div class="aurora-orb aurora-orb-1" aria-hidden="true"></div>
    <div class="aurora-orb aurora-orb-2" aria-hidden="true"></div>
    <div class="aurora-orb aurora-orb-3" aria-hidden="true"></div>
    <div class="aurora-orb aurora-orb-4" aria-hidden="true"></div>

    <div id="lw-progress" aria-hidden="true"></div>

    <header class="sticky top-0 z-50 border-b border-(--line)/80 bg-white/80 backdrop-blur-md">
        <div class="mx-auto grid max-w-[92rem] grid-cols-[minmax(0,1fr)_auto] items-center gap-3 px-4 py-3.5 md:px-6 xl:grid-cols-[minmax(280px,1fr)_auto_auto] xl:gap-5 xl:px-8">
            <a wire:navigate href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                @if (!empty($homeContent['header_logo_url']))
                    <img src="{{ $homeContent['header_logo_url'] }}" alt="Logo Program Studi"
                        class="h-11 w-11 rounded-xl border border-white/50 bg-white object-cover shadow-sm" />
                @else
                    <div class="logo-badge">PS</div>
                @endif
                <div class="min-w-0">
                    <p class="truncate text-[10px] font-bold uppercase tracking-[0.2em] text-(--olive) md:text-xs">
                        {{ $selectedPublicProdi?->name ?: $homeContent['header_logo_label'] }}</p>
                    <h1 class="display-font line-clamp-2 max-w-[30rem] text-sm font-bold leading-tight text-slate-900 md:text-lg xl:text-xl">
                        {{ $resolvedHeaderTitle }}</h1>
                </div>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden items-center gap-0.5 whitespace-nowrap text-[13px] font-semibold xl:flex 2xl:gap-1">
                <a wire:navigate href="{{ route('home') }}"
                    class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
                <a wire:navigate href="{{ route('profil') }}"
                    class="nav-link {{ request()->routeIs('profil*') ? 'active' : '' }}">Profil</a>
                <a wire:navigate href="{{ route('laporan') }}"
                    class="nav-link {{ request()->routeIs('laporan*') ? 'active' : '' }}">Laporan</a>
                <a wire:navigate href="{{ route('statistik') }}"
                    class="nav-link {{ request()->routeIs('statistik*') ? 'active' : '' }}">Statistik</a>
                <a wire:navigate href="{{ route('dokumen') }}"
                    class="nav-link {{ request()->routeIs('dokumen*') ? 'active' : '' }}">Dokumen</a>
                <a wire:navigate href="{{ route('galeri') }}"
                    class="nav-link {{ request()->routeIs('galeri*') ? 'active' : '' }}">Galeri</a>
                <a wire:navigate href="{{ route('kontak') }}"
                    class="nav-link {{ request()->routeIs('kontak*') ? 'active' : '' }}">Kontak</a>
            </nav>

            @if ($publicProdis->isNotEmpty())
                <div class="relative hidden shrink-0 xl:block"
                    x-data="publicProdiDropdown({
                        action: @js(route('public.prodi.select')),
                        selectedId: @js($selectedPublicProdiId),
                        selectedLabel: @js($selectedPublicProdi?->name ?: 'Program Studi'),
                        options: @js($publicProdiOptions),
                    })"
                    x-cloak>
                    <button type="button"
                        @click="toggle()"
                        class="flex w-[16.5rem] items-center justify-between gap-3 rounded-xl border border-blue-200 bg-white px-3 py-2.5 text-left text-xs font-bold text-blue-800 shadow-sm transition hover:border-blue-400 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-200"
                        :aria-expanded="open.toString()"
                        aria-haspopup="listbox">
                        <span class="min-w-0 truncate" x-text="selectedLabel"></span>
                        <svg class="h-4 w-4 shrink-0 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-transition.origin.top.right
                        class="absolute right-0 z-50 mt-2 w-[16rem] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl ring-1 ring-slate-900/5"
                        role="listbox"
                        @click.outside="close()"
                        @keydown.escape.window="close()">
                        <div class="max-h-72 overflow-auto p-1">
                            <template x-for="option in options" :key="option.id">
                                <button type="button"
                                    @click="choose(option)"
                                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left transition"
                                    :class="option.id === selectedId ? 'bg-blue-50 text-blue-800' : 'text-slate-700 hover:bg-slate-50'">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg"
                                        :class="option.id === selectedId ? 'bg-blue-600' : 'bg-slate-100'">
                                        <span class="h-2.5 w-2.5 rounded-full"
                                            :class="option.id === selectedId ? 'bg-white' : 'bg-slate-400'"></span>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-semibold leading-tight" x-text="option.name"></span>
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </header>

    <main id="page-content" class="relative z-10 mx-auto max-w-7xl px-4 py-8 md:px-8 md:py-12">
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <footer class="relative z-10 mt-16 border-t border-(--line) bg-white/80 backdrop-blur-sm">
        <div class="mx-auto max-w-7xl px-4 py-10 md:px-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-800">{{ $resolvedHeaderTitle }}</p>
                    <p class="mt-1 text-xs text-(--muted)">Portal Laporan Tahunan Program Studi</p>
                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-(--muted)">
                        <span>âœ‰ {{ $homeContent['contact_email'] }}</span>
                        <span>ðŸ“ž {{ $homeContent['contact_phone'] }}</span>
                    </div>
                </div>
                <nav class="flex flex-wrap gap-x-5 gap-y-2 text-xs font-semibold text-slate-500">
                    <a wire:navigate href="{{ route('home') }}" class="hover:text-(--accent) transition-colors">Beranda</a>
                    <a wire:navigate href="{{ route('profil') }}" class="hover:text-(--accent) transition-colors">Profil</a>
                    <a wire:navigate href="{{ route('laporan') }}" class="hover:text-(--accent) transition-colors">Laporan</a>
                    <a wire:navigate href="{{ route('statistik') }}" class="hover:text-(--accent) transition-colors">Statistik</a>
                    <a wire:navigate href="{{ route('dokumen') }}" class="hover:text-(--accent) transition-colors">Dokumen</a>
                    <a wire:navigate href="{{ route('galeri') }}" class="hover:text-(--accent) transition-colors">Galeri</a>
                    <a wire:navigate href="{{ route('kontak') }}" class="hover:text-(--accent) transition-colors">Kontak</a>
                </nav>
            </div>
            <div class="mt-8 border-t border-(--line) pt-5 text-center text-xs text-(--muted)">
                Â© {{ now()->year }} {{ $resolvedProgramName }} â€” Hak Cipta Dilindungi
            </div>
        </div>
    </footer>

    {{-- â”€â”€ Global Gallery Lightbox (direct child of body â€” no stacking context issues) â”€â”€ --}}
    <div id="gallery-lightbox"
        x-data="{
            open: false,
            image: '',
            title: '',
            category: '',
            description: '',
            init() {
                window.addEventListener('gallery-modal-open', (e) => {
                    this.image       = e.detail.image;
                    this.title       = e.detail.title;
                    this.category    = e.detail.category;
                    this.description = e.detail.description || '';
                    this.open        = true;
                    document.body.style.overflow = 'hidden';
                });
                window.addEventListener('gallery-modal-close', () => this.close());
            },
            close() {
                this.open = false;
                document.body.style.overflow = '';
            }
        }"
        x-init="init()"
        @keydown.escape.window="close()">

        {{-- Backdrop --}}
        <div
            x-show="open"
            x-transition:enter="transition duration-250 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="close()"
            style="display:none; position:fixed; inset:0; background:rgba(3,7,18,0.82); z-index:9998;">
        </div>

        {{-- Modal card --}}
        <div
            x-show="open"
            x-transition:enter="transition duration-250 ease-out"
            x-transition:enter-start="opacity-0 scale-95 translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition duration-180 ease-in"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            style="display:none; position:fixed; inset:0; z-index:9999;">

            {{-- Centering layer â€” always flex, not toggled by Alpine --}}
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; padding:1.25rem;">

                <div style="
                    pointer-events:auto;
                    position:relative;
                    width:100%;
                    max-width:720px;
                    max-height:calc(100vh - 2.5rem);
                    display:flex;
                    flex-direction:column;
                    overflow:hidden;
                    border-radius:1.25rem;
                    background:#ffffff;
                    box-shadow:
                        0 0 0 1px rgba(255,255,255,0.08),
                        0 32px 80px rgba(0,0,0,0.55),
                        0 8px 24px rgba(0,0,0,0.3);
                ">
                    {{-- Header --}}
                    <div style="
                        display:flex; align-items:center; gap:1rem;
                        padding:1rem 1.25rem;
                        border-bottom:1px solid #f1f5f9;
                        background:#fff;
                        flex-shrink:0;
                    ">
                        {{-- Photo icon --}}
                        <div style="
                            width:2.25rem; height:2.25rem; flex-shrink:0;
                            border-radius:.625rem;
                            background:linear-gradient(135deg,#dbeafe,#eff6ff);
                            display:flex; align-items:center; justify-content:center;
                        ">
                            <svg style="width:1.1rem;height:1.1rem;color:#2563eb;" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        {{-- Title + category --}}
                        <div style="min-width:0; flex:1;">
                            <p style="font-size:.875rem; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; line-height:1.3;" x-text="title"></p>
                            <p style="margin-top:.15rem; font-size:.6875rem; font-weight:600; text-transform:uppercase; letter-spacing:.12em; color:#94a3b8;" x-text="category"></p>
                        </div>
                        {{-- Close button --}}
                        <button type="button" @click="close()"
                            style="
                                flex-shrink:0;
                                width:2rem; height:2rem;
                                border-radius:.5rem;
                                border:1px solid #e2e8f0;
                                background:#f8fafc;
                                cursor:pointer;
                                display:flex; align-items:center; justify-content:center;
                                color:#64748b;
                                transition:all .15s;
                            "
                            onmouseover="this.style.background='#fee2e2';this.style.borderColor='#fca5a5';this.style.color='#ef4444'"
                            onmouseout="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0';this.style.color='#64748b'"
                            aria-label="Tutup">
                            <svg style="width:.9rem;height:.9rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Image area --}}
                    <div style="flex:1; overflow:hidden; background:#f8fafc; padding:.875rem; min-height:0;">
                        <div style="overflow:hidden; border-radius:.875rem; background:#020617; box-shadow:0 2px 12px rgba(0,0,0,.25);">
                            <img :src="image" :alt="title"
                                style="display:block; width:100%; max-height:58vh; object-fit:contain;">
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div style="
                        display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:.75rem;
                        padding:.875rem 1.25rem;
                        border-top:1px solid #f1f5f9;
                        background:#fafafa;
                        flex-shrink:0;
                    ">
                        <div x-show="description !== ''" style="min-width:0; flex:1; display:flex; align-items:flex-start; gap:.5rem;">
                            <svg style="width:.875rem;height:.875rem;flex-shrink:0;margin-top:.1rem;color:#94a3b8;" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p x-text="description" style="font-size:.75rem; color:#64748b; line-height:1.5;"></p>
                        </div>
                        <a :href="image" target="_blank" rel="noreferrer"
                            style="
                                margin-left:auto; flex-shrink:0;
                                display:inline-flex; align-items:center; gap:.4rem;
                                padding:.5rem 1.125rem;
                                border-radius:9999px;
                                background:linear-gradient(135deg,#3b82f6,#1d4ed8);
                                color:#fff;
                                font-size:.75rem; font-weight:600;
                                text-decoration:none;
                                box-shadow:0 3px 12px rgba(37,99,235,.35);
                                transition:transform .15s, box-shadow .15s;
                            "
                            onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 18px rgba(37,99,235,.45)'"
                            onmouseout="this.style.transform='';this.style.boxShadow='0 3px 12px rgba(37,99,235,.35)'">
                            <svg style="width:.8rem;height:.8rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Buka Penuh
                        </a>
                    </div>

                </div>
            </div>{{-- /centering --}}
        </div>{{-- /modal x-show --}}
    </div>{{-- /gallery-lightbox --}}

    @livewireScriptConfig
    <script data-navigate-once>
        (() => {
            if (window.__portalLayoutBound) return;
            window.__portalLayoutBound = true;

            function closeMobileMenu() {
                const mobileMenu = document.getElementById('mobile-menu');
                const hamburgerIcon = document.getElementById('hamburger-icon');
                const closeIcon = document.getElementById('close-icon');
                const mobileMenuBtn = document.getElementById('mobile-menu-btn');

                if (!mobileMenu) return;
                mobileMenu.classList.add('hidden');
                hamburgerIcon && hamburgerIcon.classList.remove('hidden');
                closeIcon && closeIcon.classList.add('hidden');
                mobileMenuBtn && mobileMenuBtn.setAttribute('aria-expanded', 'false');
            }

            window.publicProdiDropdown = (config) => ({
                open: false,
                action: config.action,
                selectedId: Number(config.selectedId || 0),
                selectedLabel: config.selectedLabel || '',
                options: config.options || [],

                toggle() {
                    this.open = !this.open;
                },

                close() {
                    this.open = false;
                },

                async choose(option) {
                    if (!option) return;
                    this.selectedId = Number(option.id || 0);
                    this.selectedLabel = option.name || '';
                    this.close();
                    await this.save(option.id);
                },

                async save(prodiId) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content || window.livewireScriptConfig?.csrf;

                    await fetch(this.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-Requested-With': 'XMLHttpRequest',
                            ...(token ? { 'X-CSRF-TOKEN': token } : {}),
                        },
                        body: new URLSearchParams({ prodi_id: String(prodiId) }).toString(),
                    });

                    if (window.Livewire && typeof window.Livewire.navigate === 'function') {
                        window.Livewire.navigate(window.location.pathname + window.location.search);
                        return;
                    }

                    window.location.reload();
                },
            });

            document.addEventListener('livewire:navigate', () => {
                const lwProgress = document.getElementById('lw-progress');
                const content = document.getElementById('page-content');

                lwProgress && lwProgress.classList.remove('is-done');
                lwProgress && lwProgress.classList.add('is-loading');
                content && content.classList.add('page-leaving');
            });

            document.addEventListener('livewire:navigated', () => {
                const lwProgress = document.getElementById('lw-progress');
                const content = document.getElementById('page-content');

                if (lwProgress) {
                    lwProgress.classList.remove('is-loading');
                    lwProgress.classList.add('is-done');
                    setTimeout(() => {
                        lwProgress.classList.remove('is-done');
                        lwProgress.style.width = '';
                    }, 180);
                }

                if (content) {
                    content.classList.remove('page-leaving');
                    content.style.animation = 'none';
                    content.offsetHeight;
                    content.style.animation = '';
                }

                closeMobileMenu();
            });

            document.addEventListener('click', (event) => {
                const mobileMenuBtn = event.target.closest('#mobile-menu-btn');
                if (!mobileMenuBtn) return;

                const mobileMenu = document.getElementById('mobile-menu');
                const hamburgerIcon = document.getElementById('hamburger-icon');
                const closeIcon = document.getElementById('close-icon');

                if (!mobileMenu) return;
                const isClosed = mobileMenu.classList.contains('hidden');
                mobileMenu.classList.toggle('hidden', !isClosed);
                hamburgerIcon && hamburgerIcon.classList.toggle('hidden', isClosed);
                closeIcon && closeIcon.classList.toggle('hidden', !isClosed);
                mobileMenuBtn.setAttribute('aria-expanded', isClosed ? 'true' : 'false');
            });

        })();
    </script>

    {{-- â”€â”€ Shared Chart.js Alpine component (used by beranda + statistik) â”€â”€ --}}
    <script data-navigate-once>
    window.__prodiChartRegistry = window.__prodiChartRegistry || {};

    function prodiChartInit(chartId, initLabels, initDatasets, showLegend) {
        return {
            _chart: null,

            buildDatasets(raw) {
                return (raw || []).map(ds => ({
                    label:                ds.label || '',
                    data:                 ds.data  || [],
                    borderColor:          ds.color || '#6366f1',
                    backgroundColor:      ds.fill  ? (ds.color || '#6366f1') + '18' : 'transparent',
                    fill:                 !!ds.fill,
                    tension:              0.38,
                    borderWidth:          ds.borderWidth || 2.5,
                    borderDash:           ds.dash  || [],
                    pointRadius:          3,
                    pointHoverRadius:     6,
                    pointBackgroundColor: ds.color || '#6366f1',
                    pointBorderColor:     '#fff',
                    pointBorderWidth:     1.5,
                    yAxisID:              ds.yAxis || 'y',
                }));
            },

            makeScales(datasets) {
                const hasY2 = (datasets || []).some(ds => ds.yAxis === 'y2');
                const s = {
                    x: {
                        grid:   { display: false },
                        ticks:  { color:'#94a3b8', font:{size:11, family:"'Plus Jakarta Sans',sans-serif"}, maxRotation:0 },
                        border: { display: false },
                    },
                    y: {
                        position: 'left', grid: { color:'#f1f5f9' },
                        ticks: { color:'#94a3b8', font:{size:11}, padding:6,
                                 callback: v => Number.isInteger(v) ? v.toLocaleString('id-ID') : v },
                        border: { display: false },
                    },
                };
                if (hasY2) {
                    s.y2 = {
                        position:'right', grid:{ display:false },
                        ticks:{ color:'#7c3aed', font:{size:11}, padding:6,
                                callback: v => Number.isInteger(v) ? v : parseFloat(v).toFixed(2) },
                        border:{ display:false },
                    };
                }
                return s;
            },

            create(labels, datasets) {
                const el = document.getElementById(chartId);
                if (!el) return;

                // Wait for Chart.js to be available (loaded via Vite bundle)
                if (typeof window.Chart === 'undefined') {
                    setTimeout(() => this.create(labels, datasets), 50);
                    return;
                }

                // Destroy stale
                const stale = window.__prodiChartRegistry[chartId] || window.Chart.getChart(el);
                if (stale) { try { stale.destroy(); } catch(e){} }
                delete window.__prodiChartRegistry[chartId];

                const instance = new window.Chart(el, {
                    type: 'line',
                    data: { labels: labels || [], datasets: this.buildDatasets(datasets) },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        animation:  { duration: 350, easing: 'easeInOutQuart' },
                        interaction: { mode:'index', intersect:false },
                        plugins: {
                            legend: {
                                display: !!showLegend, position:'top', align:'end',
                                labels: { boxWidth:10, boxHeight:10, borderRadius:5, useBorderRadius:true,
                                          color:'#475569', padding:14,
                                          font:{size:11, family:"'Plus Jakarta Sans',sans-serif", weight:'600'} },
                            },
                            tooltip: {
                                backgroundColor:'#1e293b', titleColor:'#f8fafc', bodyColor:'#cbd5e1',
                                borderColor:'#334155', borderWidth:1, cornerRadius:10, padding:12,
                                titleFont:{size:12, weight:'700', family:"'Plus Jakarta Sans',sans-serif"},
                                bodyFont: {size:11, family:"'Plus Jakarta Sans',sans-serif"},
                                callbacks: { label: ctx => {
                                    const v = ctx.parsed.y;
                                    const f = Number.isInteger(v) ? v.toLocaleString('id-ID') : parseFloat(v).toFixed(2).replace('.',',');
                                    return '  ' + ctx.dataset.label + ': ' + f;
                                }},
                            },
                        },
                        scales: this.makeScales(datasets),
                    },
                });

                this._chart = instance;
                window.__prodiChartRegistry[chartId] = instance;
            },

            // Called on initial Alpine mount
            boot() {
                this.create(initLabels, initDatasets);
            },

            // Called when Livewire dispatches 'prodi-chart-update' after re-render
            onUpdate(detail) {
                if (!detail || detail.chartId !== chartId) return;
                const newDatasets = this.buildDatasets(detail.datasets || []);
                if (this._chart) {
                    // Rebuild scales in case y2 axis presence changed
                    this._chart.data.labels   = detail.labels || [];
                    this._chart.data.datasets = newDatasets;
                    this._chart.options.scales = this.makeScales(detail.datasets || []);
                    this._chart.update('active');
                } else {
                    this.create(detail.labels, detail.datasets);
                }
            },
        };
    }

    // Purge all charts on Livewire SPA navigation
    document.addEventListener('livewire:navigating', () => {
        Object.values(window.__prodiChartRegistry).forEach(c => { try { c.destroy(); } catch(e){} });
        window.__prodiChartRegistry = {};
    });
    </script>
</body>

</html>
