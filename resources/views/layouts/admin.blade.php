<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title . ' - ' : '' }}Admin Panel Prodi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&family=Fraunces:opsz,wght@9..144,600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --bg-main: #f5f5fb;
            --bg-card: #ffffff;
            --ink: #2f2b3d;
            --muted: #8d8a9d;
            --line: #ebeaf2;
            --accent: #7367f0;
            --accent-soft: #f1efff;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background: var(--bg-main);
            color: var(--ink);
        }

        .display-font {
            font-family: 'Fraunces', serif;
        }

        .section-box,
        .panel-card,
        .topbar-card {
            background: var(--bg-card);
            border: 1px solid var(--line);
            box-shadow: 0 10px 30px rgba(47, 43, 61, 0.06);
        }

        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 40;
            width: 260px;
            height: 100vh;
            overflow-y: auto;
            background: #fff;
            border-right: 1px solid var(--line);
            padding: 1rem 0.85rem;
        }

        .admin-main {
            margin-left: 260px;
            min-height: 100vh;
            padding: 1.2rem;
        }

        .sidebar-section-title {
            padding: 0 0.85rem;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #b3b1c2;
        }

        .sidebar-link {
            display: flex;
            position: relative;
            align-items: center;
            gap: 0.75rem;
            border-radius: 0.8rem;
            border: 1px solid transparent;
            padding: 0.72rem 0.85rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: #5d596c;
            transition: all 0.25s ease;
        }

        .sidebar-link:hover {
            background: #f8f7ff;
            border-color: #ede9fe;
            color: #2f2b3d;
            transform: translateX(2px);
        }

        .sidebar-link.active {
            background: linear-gradient(90deg, #f3f1ff 0%, #f8f7ff 100%);
            border-color: #ddd8ff;
            color: #5d50de;
            box-shadow: 0 10px 22px rgba(115, 103, 240, 0.16);
            transform: translateX(2px);
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0.45rem;
            top: 0.45rem;
            bottom: 0.45rem;
            width: 4px;
            border-radius: 999px;
            background: linear-gradient(180deg, #7367f0 0%, #5b4fe0 100%);
        }

        .sidebar-icon {
            display: inline-flex;
            height: 1.15rem;
            width: 1.15rem;
            align-items: center;
            justify-content: center;
            color: #6f6b7d;
            flex-shrink: 0;
            transition: color 0.25s ease;
        }

        .sidebar-link.active .sidebar-icon {
            color: #5d50de;
        }

        .sidebar-link.active .sidebar-label {
            font-weight: 800;
        }

        .sidebar-label {
            flex: 1;
        }

        .sidebar-arrow {
            color: #8d8a9d;
            flex-shrink: 0;
        }

        .sidebar-badge {
            min-width: 1.55rem;
            border-radius: 999px;
            background: #ff4d5e;
            padding: 0.12rem 0.45rem;
            text-align: center;
            font-size: 0.72rem;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        .brand-mark {
            height: 1.1rem;
            width: 1.1rem;
            border-radius: 0.35rem;
            background: linear-gradient(135deg, #7367f0, #8c7dff);
            transform: rotate(45deg);
            flex-shrink: 0;
        }

        .topbar-card {
            display: flex;
            position: sticky;
            top: 0;
            z-index: 30;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
        }

        .search-shell {
            display: flex;
            min-width: 320px;
            flex: 1;
            align-items: center;
            gap: 0.85rem;
            border-radius: 0.95rem;
            border: 1px solid var(--line);
            background: #fff;
            padding: 0.85rem 1rem;
        }

        .topbar-action {
            display: grid;
            height: 2.6rem;
            width: 2.6rem;
            place-items: center;
            border: 1px solid var(--line);
            border-radius: 0.95rem;
            background: #fff;
            color: #6f6b7d;
        }

        .admin-toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 120;
            max-width: 360px;
            border-radius: 0.9rem;
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
            padding: 0.85rem 1rem;
            box-shadow: 0 16px 34px rgba(22, 101, 52, 0.2);
            opacity: 0;
            transform: translateY(-10px);
            pointer-events: none;
            transition: opacity 0.35s ease, transform 0.35s ease;
        }

        .admin-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .admin-toast.hide {
            opacity: 0;
            transform: translateY(-10px);
        }

        @media (max-width: 1023px) {
            .admin-sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .admin-main {
                margin-left: 0;
            }

            .topbar-card {
                flex-direction: column;
                align-items: stretch;
            }

            .search-shell {
                min-width: 0;
            }
        }
    </style>
</head>

<body>
    @if (session()->has('status'))
        <div id="admin-toast" class="admin-toast" role="status" aria-live="polite"
            data-toast-message="{{ session('status') }}">
            <p class="text-sm font-semibold">Data berhasil disimpan</p>
            <p class="mt-1 text-xs text-emerald-700/90">{{ session('status') }}</p>
        </div>
    @endif

    <aside class="admin-sidebar">
        <div class="mb-6 flex items-center gap-3 px-3">
            <div class="brand-mark"></div>
            <div class="text-3xl font-extrabold tracking-tight text-slate-800">Prodi</div>
        </div>

        <div class="">
            <div>
                <p class="sidebar-section-title">Dashboard</p>
                <nav class="mt-3 space-y-1.5">
                    <a wire:navigate.hover href="{{ route('admin.dashboard-data') }}"
                        class="sidebar-link {{ request()->routeIs('admin.dashboard-data') ? 'active' : '' }}">
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 11l9-8 9 8v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z" />
                            </svg>
                        </span>
                        <span class="sidebar-label">Dashboards</span>

                    </a>
                </nav>
            </div>

            <div>
                <p class="sidebar-section-title">Konten Utama</p>
                <nav class="space-y-1.5">
                    <a wire:navigate.hover href="{{ route('admin.program-agenda') }}"
                        class="sidebar-link {{ request()->routeIs('admin.program-agenda') ? 'active' : '' }}">
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" />
                            </svg>
                        </span>
                        <span class="sidebar-label">Program & Agenda</span>

                    </a>
                    <a wire:navigate.hover href="{{ route('admin.annual-report') }}"
                        class="sidebar-link {{ request()->routeIs('admin.annual-report') ? 'active' : '' }}">
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                        <span class="sidebar-label">Laporan Tahunan</span>
                    </a>
                    <a wire:navigate.hover href="{{ route('admin.beranda-content') }}"
                        class="sidebar-link {{ request()->routeIs('admin.beranda-content') ? 'active' : '' }}">
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v12m6-6H6m14-7H4a1 1 0 00-1 1v12a1 1 0 001 1h16a1 1 0 001-1V6a1 1 0 00-1-1z" />
                            </svg>
                        </span>
                        <span class="sidebar-label">Konten Beranda</span>

                    </a>
                    <a wire:navigate.hover href="{{ route('admin.feedback') }}"
                        class="sidebar-link {{ request()->routeIs('admin.feedback') ? 'active' : '' }}">
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 10h8M8 14h5M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H7l-4 3V7a1 1 0 011-1z" />
                            </svg>
                        </span>
                        <span class="sidebar-label">Inbox Umpan Balik</span>
                    </a>
                    <a wire:navigate.hover href="{{ route('admin.documents') }}"
                        class="sidebar-link {{ request()->routeIs('admin.documents') ? 'active' : '' }}">
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 3v6h6" />
                            </svg>
                        </span>
                        <span class="sidebar-label">Dokumen Publik</span>
                    </a>
                    <a wire:navigate.hover href="{{ route('admin.profile') }}"
                        class="sidebar-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <span class="sidebar-label">Profil Program Studi</span>
                    </a>
                </nav>
            </div>

            <div>
                <p class="sidebar-section-title">Layanan Publik</p>
                <nav class="space-y-1.5">
                    <a wire:navigate.hover href="{{ route('home') }}"
                        class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 12l9-9 9 9M4 10v10h16V10" />
                            </svg>
                        </span>
                        <span class="sidebar-label">Front Pages</span>

                    </a>
                    <a href="{{ route('laporan.pdf') }}"
                        class="sidebar-link {{ request()->routeIs('laporan.pdf') ? 'active' : '' }}">
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 3v6h6" />
                            </svg>
                        </span>
                        <span class="sidebar-label">Export PDF</span>

                    </a>
                </nav>
            </div>

            <div>
                <p class="sidebar-section-title">Session</p>
                <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-left">
                        <span class="sidebar-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H9" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 20H6a2 2 0 01-2-2V6a2 2 0 012-2h7" />
                            </svg>
                        </span>
                        <span class="sidebar-label">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="admin-main">
        <div class="w-full">
            <section class="topbar-card mb-5 w-full ">

                <div class="flex  w-full justify-end items-center gap-2">
                    <div class="topbar-action">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M3 12h18" />
                        </svg>
                    </div>
                    <div class="topbar-action">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.17V11a6 6 0 10-12 0v3.17a2 2 0 01-.6 1.43L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2">
                        <div
                            class="grid h-9 w-9 place-items-center rounded-full bg-linear-to-br from-violet-500 to-indigo-600 text-xs font-bold text-white">
                            AP</div>
                        <div class="hidden text-left sm:block">
                            <p class="text-xs font-semibold text-slate-700">Admin Prodi</p>
                            <p class="text-[11px] text-slate-400">Administrator</p>
                        </div>
                    </div>
                </div>
            </section>

            {{ $slot }}
        </div>
    </main>

    @livewireScriptConfig
    <script>
        (function() {
            let hideTimer = null;
            let removeTimer = null;

            const renderToast = (message) => {
                let toast = document.getElementById('admin-toast');

                if (!toast) {
                    toast = document.createElement('div');
                    toast.id = 'admin-toast';
                    toast.className = 'admin-toast';
                    toast.setAttribute('role', 'status');
                    toast.setAttribute('aria-live', 'polite');
                    document.body.appendChild(toast);
                }

                toast.innerHTML = `
                    <p class="text-sm font-semibold">Data berhasil disimpan</p>
                    <p class="mt-1 text-xs text-emerald-700/90"></p>
                `;

                const detail = toast.querySelector('p:last-child');
                if (detail) {
                    detail.textContent = message || 'Perubahan berhasil disimpan.';
                }

                toast.classList.remove('hide');
                requestAnimationFrame(() => {
                    toast.classList.add('show');
                });

                if (hideTimer) {
                    clearTimeout(hideTimer);
                }
                if (removeTimer) {
                    clearTimeout(removeTimer);
                }

                hideTimer = setTimeout(() => {
                    toast.classList.remove('show');
                    toast.classList.add('hide');
                }, 3200);

                removeTimer = setTimeout(() => {
                    toast.remove();
                }, 3800);
            };

            const initialToast = document.getElementById('admin-toast');
            if (initialToast) {
                renderToast(initialToast.dataset.toastMessage || 'Perubahan berhasil disimpan.');
            }

            document.addEventListener('livewire:init', () => {
                Livewire.on('admin-toast', (event) => {
                    const payload = Array.isArray(event) ? event[0] : event;
                    renderToast(payload?.message || 'Perubahan berhasil disimpan.');
                });
            });
        })();
    </script>
</body>

</html>
