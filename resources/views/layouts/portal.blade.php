<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $homeContent = \App\Models\HomePageSetting::current();
        $routeName = request()->route()?->getName();
        $kaprodiNameForDisplay = trim((string) ($homeContent['kaprodi_name'] ?? ''));
        $resolvedProgramName = $kaprodiNameForDisplay !== '' ? $kaprodiNameForDisplay : 'Program Studi';
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
            background:
                radial-gradient(ellipse at 5% 0%, #dbeafe 0%, transparent 40%),
                radial-gradient(ellipse at 95% 5%, #ccfbf1 0%, transparent 35%),
                var(--bg-main);
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
    <div id="lw-progress" aria-hidden="true"></div>

    <header class="sticky top-0 z-50 border-b border-(--line)/80 bg-(--bg-main)/90 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 md:px-8">
            <a wire:navigate.hover href="{{ route('home') }}" class="flex items-center gap-3">
                @if (!empty($homeContent['header_logo_url']))
                    <img src="{{ $homeContent['header_logo_url'] }}" alt="Logo Program Studi"
                        class="h-11 w-11 rounded-xl border border-white/50 bg-white object-cover shadow-sm" />
                @else
                    <div class="logo-badge">PS</div>
                @endif
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-(--olive)">
                        {{ $homeContent['header_logo_label'] }}</p>
                    <h1 class="display-font text-base font-bold leading-tight md:text-xl">
                        {{ $resolvedHeaderTitle }}</h1>
                </div>
            </a>

            <nav class="hidden flex-wrap items-center gap-2 text-sm font-semibold lg:flex">
                <a wire:navigate.hover href="{{ route('home') }}"
                    class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
                <a wire:navigate.hover href="{{ route('profil') }}"
                    class="nav-link {{ request()->routeIs('profil*') ? 'active' : '' }}">Profil</a>
                <a wire:navigate.hover href="{{ route('laporan') }}"
                    class="nav-link {{ request()->routeIs('laporan*') ? 'active' : '' }}">Laporan</a>
                <a wire:navigate.hover href="{{ route('statistik') }}"
                    class="nav-link {{ request()->routeIs('statistik*') ? 'active' : '' }}">Statistik</a>
                <a wire:navigate.hover href="{{ route('dokumen') }}"
                    class="nav-link {{ request()->routeIs('dokumen*') ? 'active' : '' }}">Dokumen</a>
                <a wire:navigate.hover href="{{ route('galeri') }}"
                    class="nav-link {{ request()->routeIs('galeri*') ? 'active' : '' }}">Galeri</a>
                <a wire:navigate.hover href="{{ route('kontak') }}"
                    class="nav-link {{ request()->routeIs('kontak*') ? 'active' : '' }}">Kontak</a>
            </nav>
        </div>
        <div class="mx-auto flex max-w-7xl flex-wrap gap-2 px-4 pb-4 lg:hidden md:px-8">
            <a wire:navigate.hover href="{{ route('home') }}"
                class="nav-link {{ request()->routeIs('home') ? 'active' : '' }} text-xs">Beranda</a>
            <a wire:navigate.hover href="{{ route('profil') }}"
                class="nav-link {{ request()->routeIs('profil*') ? 'active' : '' }} text-xs">Profil</a>
            <a wire:navigate.hover href="{{ route('laporan') }}"
                class="nav-link {{ request()->routeIs('laporan*') ? 'active' : '' }} text-xs">Laporan</a>
            <a wire:navigate.hover href="{{ route('statistik') }}"
                class="nav-link {{ request()->routeIs('statistik*') ? 'active' : '' }} text-xs">Statistik</a>
            <a wire:navigate.hover href="{{ route('dokumen') }}"
                class="nav-link {{ request()->routeIs('dokumen*') ? 'active' : '' }} text-xs">Dokumen</a>
            <a wire:navigate.hover href="{{ route('galeri') }}"
                class="nav-link {{ request()->routeIs('galeri*') ? 'active' : '' }} text-xs">Galeri</a>
            <a wire:navigate.hover href="{{ route('kontak') }}"
                class="nav-link {{ request()->routeIs('kontak*') ? 'active' : '' }} text-xs">Kontak</a>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 md:px-8 md:py-12">
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <footer class="mt-16 border-t border-(--line) bg-white/80 backdrop-blur-sm">
        <div class="mx-auto max-w-7xl px-4 py-10 md:px-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-800">{{ $resolvedHeaderTitle }}</p>
                    <p class="mt-1 text-xs text-(--muted)">Portal Laporan Tahunan Program Studi</p>
                    <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-(--muted)">
                        <span>✉ {{ $homeContent['contact_email'] }}</span>
                        <span>📞 {{ $homeContent['contact_phone'] }}</span>
                    </div>
                </div>
                <nav class="flex flex-wrap gap-x-5 gap-y-2 text-xs font-semibold text-slate-500">
                    <a wire:navigate.hover href="{{ route('home') }}" class="hover:text-(--accent) transition-colors">Beranda</a>
                    <a wire:navigate.hover href="{{ route('profil') }}" class="hover:text-(--accent) transition-colors">Profil</a>
                    <a wire:navigate.hover href="{{ route('laporan') }}" class="hover:text-(--accent) transition-colors">Laporan</a>
                    <a wire:navigate.hover href="{{ route('statistik') }}" class="hover:text-(--accent) transition-colors">Statistik</a>
                    <a wire:navigate.hover href="{{ route('dokumen') }}" class="hover:text-(--accent) transition-colors">Dokumen</a>
                    <a wire:navigate.hover href="{{ route('galeri') }}" class="hover:text-(--accent) transition-colors">Galeri</a>
                    <a wire:navigate.hover href="{{ route('kontak') }}" class="hover:text-(--accent) transition-colors">Kontak</a>
                </nav>
            </div>
            <div class="mt-8 border-t border-(--line) pt-5 text-center text-xs text-(--muted)">
                © {{ now()->year }} {{ $resolvedProgramName }} — Hak Cipta Dilindungi
            </div>
        </div>
    </footer>

    @livewireScriptConfig
    <script>
        const lwProgress = document.getElementById('lw-progress');

        document.addEventListener('livewire:navigate', () => {
            lwProgress.classList.remove('is-done');
            lwProgress.classList.add('is-loading');
        });

        document.addEventListener('livewire:navigated', () => {
            lwProgress.classList.remove('is-loading');
            lwProgress.classList.add('is-done');

            setTimeout(() => {
                lwProgress.classList.remove('is-done');
                lwProgress.style.width = '';
            }, 180);
        });
    </script>
</body>

</html>
