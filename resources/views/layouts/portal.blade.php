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
        $metaTitle = (isset($title) ? $title . ' - ' : '') . 'Laporan Tahunan Kepala Program Studi';
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
        $metaImage = 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80';
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
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --bg-main: #f2f6fb;
            --bg-card: #ffffff;
            --ink: #0f1f37;
            --muted: #5b6b81;
            --accent: #1166d8;
            --accent-soft: #dbeafe;
            --line: #dbe5f2;
            --olive: #0f766e;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Sora', sans-serif;
            background:
                radial-gradient(circle at 8% 10%, #eaf2ff 0%, transparent 26%),
                radial-gradient(circle at 88% 14%, #d5f3f1 0%, transparent 24%),
                var(--bg-main);
            color: var(--ink);
        }

        .display-font {
            font-family: 'Fraunces', serif;
        }

        .section-box {
            background: var(--bg-card);
            border: 1px solid var(--line);
            box-shadow: 0 14px 32px rgba(15, 31, 55, 0.08);
        }

        .logo-badge {
            width: 2.4rem;
            height: 2.4rem;
            border-radius: 0.75rem;
            background: linear-gradient(145deg, #0ea5e9, #2563eb);
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .nav-link {
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--accent);
            background: var(--accent-soft);
        }

        #lw-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #0ea5e9, #2563eb);
            box-shadow: 0 0 12px rgba(37, 99, 235, 0.5);
            z-index: 100;
            opacity: 0;
            transition: width 0.25s ease, opacity 0.2s ease;
        }

        #lw-progress.is-loading {
            opacity: 1;
            width: 78%;
        }

        #lw-progress.is-done {
            opacity: 1;
            width: 100%;
        }
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

    <footer class="mt-12 border-t border-(--line) bg-white/70 py-8 backdrop-blur">
        <div
            class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 text-xs text-(--muted) md:flex-row md:px-8">
            <p>© {{ now()->year }} Program Studi {{ $resolvedProgramName }} - Portal Laporan Tahunan</p>
            <p>Email: {{ $homeContent['contact_email'] }} | Telp: {{ $homeContent['contact_phone'] }} | WhatsApp:
                {{ $homeContent['contact_whatsapp'] }}</p>
            <p class="flex flex-wrap items-center justify-center gap-2">
                <a wire:navigate.hover href="{{ route('home') }}" class="hover:text-(--accent)">Beranda</a>
                <span>|</span>
                <a wire:navigate.hover href="{{ route('profil') }}" class="hover:text-(--accent)">Profil</a>
                <span>|</span>
                <a wire:navigate.hover href="{{ route('laporan') }}" class="hover:text-(--accent)">Laporan Tahunan</a>
                <span>|</span>
                <a wire:navigate.hover href="{{ route('statistik') }}" class="hover:text-(--accent)">Data &amp;
                    Statistik</a>
                <span>|</span>
                <a wire:navigate.hover href="{{ route('dokumen') }}" class="hover:text-(--accent)">Dokumen</a>
                <span>|</span>
                <a wire:navigate.hover href="{{ route('galeri') }}" class="hover:text-(--accent)">Galeri</a>
                <span>|</span>
                <a wire:navigate.hover href="{{ route('kontak') }}" class="hover:text-(--accent)">Kontak</a>
            </p>
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
