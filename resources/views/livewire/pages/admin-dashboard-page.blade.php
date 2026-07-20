<div class="space-y-6">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-zinc-400">Selamat datang</p>
            <h2 class="mt-0.5 text-xl font-extrabold text-zinc-800">Dashboard</h2>
        </div>
        <div class="flex items-center gap-2">
            @if ($tahunTerbaru)
                <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                    Data terbaru: {{ $tahunTerbaru }}
                </span>
            @endif
            <span class="rounded-full border border-zinc-200 bg-white px-3 py-1 text-xs font-semibold text-zinc-500">
                {{ now()->locale('id')->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    {{-- ── Count cards row ── --}}
    @php
        $isCentralAdmin = auth()->user()?->role === 'admin';
        $countCards = $isCentralAdmin ? [
            [
                'label'  => 'Program Studi',
                'value'  => $totalProdi,
                'icon'   => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'color'  => 'text-indigo-700',
                'bg'     => 'bg-indigo-50',
                'ring'   => 'ring-indigo-100',
                'border' => 'border-t-indigo-500',
                'link'   => route('admin.users'),
                'hint'   => 'Prodi aktif',
            ],
            [
                'label'  => 'Total Pengguna',
                'value'  => $totalUsers,
                'icon'   => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'color'  => 'text-teal-700',
                'bg'     => 'bg-teal-50',
                'ring'   => 'ring-teal-100',
                'border' => 'border-t-teal-500',
                'link'   => route('admin.users'),
                'hint'   => 'Semua role',
            ],
            [
                'label'  => 'Data Statistik',
                'value'  => $totalTahun,
                'icon'   => 'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7-10a4 4 0 100-8 4 4 0 000 8zm11 10v-2a4 4 0 00-3-3.87m-1-11.26a4 4 0 010 7.75',
                'color'  => 'text-violet-700',
                'bg'     => 'bg-violet-50',
                'ring'   => 'ring-violet-100',
                'border' => 'border-t-violet-500',
                'link'   => route('admin.dashboard'),
                'hint'   => 'Total baris tahunan',
            ],
            [
                'label'  => 'Dokumen Publik',
                'value'  => $totalDokumen,
                'icon'   => 'M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z',
                'color'  => 'text-emerald-700',
                'bg'     => 'bg-emerald-50',
                'ring'   => 'ring-emerald-100',
                'border' => 'border-t-emerald-500',
                'link'   => route('admin.dashboard'),
                'hint'   => 'Total semua prodi',
            ],
            [
                'label'  => 'Program & Agenda',
                'value'  => $totalProgram,
                'icon'   => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'color'  => 'text-sky-700',
                'bg'     => 'bg-sky-50',
                'ring'   => 'ring-sky-100',
                'border' => 'border-t-sky-500',
                'link'   => route('admin.dashboard'),
                'hint'   => 'Tahun '.$tahunTerbaru,
            ],
            [
                'label'  => 'Laporan Tahunan',
                'value'  => $totalLaporan,
                'icon'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'color'  => 'text-amber-700',
                'bg'     => 'bg-amber-50',
                'ring'   => 'ring-amber-100',
                'border' => 'border-t-amber-500',
                'link'   => route('admin.dashboard'),
                'hint'   => 'Total seksi laporan',
            ],
            [
                'label'  => 'Feedback Masuk',
                'value'  => $totalFeedback,
                'icon'   => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                'color'  => 'text-rose-700',
                'bg'     => 'bg-rose-50',
                'ring'   => 'ring-rose-100',
                'border' => 'border-t-rose-500',
                'link'   => route('admin.dashboard'),
                'hint'   => $unreadFeedback > 0 ? $unreadFeedback.' belum dibaca' : 'Semua terbaca',
            ],
            [
                'label'  => 'Tahun Terbaru',
                'value'  => $tahunTerbaru ?: 0,
                'icon'   => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'color'  => 'text-zinc-700',
                'bg'     => 'bg-zinc-50',
                'ring'   => 'ring-zinc-100',
                'border' => 'border-t-zinc-400',
                'link'   => route('admin.dashboard'),
                'hint'   => 'Periode monitoring',
            ],
        ] : [
            [
                'label'  => 'Data Tahun',
                'value'  => $totalTahun,
                'icon'   => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'color'  => 'text-indigo-700',
                'bg'     => 'bg-indigo-50',
                'ring'   => 'ring-indigo-100',
                'border' => 'border-t-indigo-500',
                'link'   => route('admin.dashboard-data'),
                'hint'   => 'Tahun terdaftar',
            ],
            [
                'label'  => 'Program & Agenda',
                'value'  => $totalProgram,
                'icon'   => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'color'  => 'text-violet-700',
                'bg'     => 'bg-violet-50',
                'ring'   => 'ring-violet-100',
                'border' => 'border-t-violet-500',
                'link'   => route('admin.program-agenda'),
                'hint'   => 'Tahun '.$tahunTerbaru,
            ],
            [
                'label'  => 'Dokumen',
                'value'  => $totalDokumen,
                'icon'   => 'M7 3h8l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z',
                'color'  => 'text-emerald-700',
                'bg'     => 'bg-emerald-50',
                'ring'   => 'ring-emerald-100',
                'border' => 'border-t-emerald-500',
                'link'   => route('admin.documents'),
                'hint'   => 'Semua kategori',
            ],
            [
                'label'  => 'Laporan Tahunan',
                'value'  => $totalLaporan,
                'icon'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'color'  => 'text-sky-700',
                'bg'     => 'bg-sky-50',
                'ring'   => 'ring-sky-100',
                'border' => 'border-t-sky-500',
                'link'   => route('admin.annual-report'),
                'hint'   => 'Seksi laporan',
            ],
            [
                'label'  => 'Umpan Balik',
                'value'  => $totalFeedback,
                'icon'   => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                'color'  => 'text-rose-700',
                'bg'     => 'bg-rose-50',
                'ring'   => 'ring-rose-100',
                'border' => 'border-t-rose-500',
                'link'   => route('admin.feedback'),
                'hint'   => $unreadFeedback > 0 ? $unreadFeedback.' belum dibaca' : 'Semua terbaca',
            ],
        ];
    @endphp
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($countCards as $card)
            <a href="{{ $card['link'] }}"
                class="section-box group rounded-2xl border-t-4 {{ $card['border'] }} p-4 transition-all hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $card['bg'] }} ring-1 {{ $card['ring'] }}">
                        <svg class="h-4.5 w-4.5 {{ $card['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
                        </svg>
                    </span>
                    <svg class="h-4 w-4 text-zinc-300 transition group-hover:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
                <p class="mt-3 text-2xl font-extrabold {{ $card['color'] }}">{{ number_format($card['value'],0,',','.') }}</p>
                <p class="mt-0.5 text-xs font-semibold text-zinc-600">{{ $card['label'] }}</p>
                <p class="mt-0.5 text-[10px] text-zinc-400">{{ $card['hint'] }}</p>
            </a>
        @endforeach
    </div>

    {{-- ── KPI cards tahun terbaru ── --}}
    @if ($tahunTerbaru)
        <div>
            <p class="mb-3 text-xs font-bold uppercase tracking-widest text-zinc-400">KPI Tahun {{ $tahunTerbaru }}</p>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @php
                    $kpiCards = [
                        ['label'=>'Mahasiswa Aktif','key'=>'mahasiswa','fmt'=>0, 'color'=>'#4f46e5','bg'=>'bg-indigo-50','text'=>'text-indigo-700','chart'=>'mahasiswa'],
                        ['label'=>'IPK Rata-rata',  'key'=>'ipk',      'fmt'=>2, 'color'=>'#0f766e','bg'=>'bg-teal-50',   'text'=>'text-teal-700',  'chart'=>'ipk'],
                        ['label'=>'Dosen Tetap',    'key'=>'dosen',    'fmt'=>0, 'color'=>'#7c3aed','bg'=>'bg-violet-50', 'text'=>'text-violet-700','chart'=>'dosen'],
                        ['label'=>'Publikasi (Realisasi)',      'key'=>'publikasi','fmt'=>0, 'color'=>'#d97706','bg'=>'bg-amber-50',  'text'=>'text-amber-700', 'chart'=>'publikasi'],
                    ];
                @endphp
                @foreach ($kpiCards as $k)
                    @php
                        $chart = $charts[$k['chart']];
                        $pts   = $chart['points'];
                        $lastY = $chart['lastY'];
                        // first point X for polygon area
                        $firstX = $pts ? (float) explode(',', explode(' ', trim($pts))[0])[0] : 34;
                    @endphp
                    <div class="section-box overflow-hidden rounded-2xl p-4">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-zinc-400">{{ $k['label'] }}</p>
                        <p class="mt-2 text-3xl font-extrabold {{ $k['text'] }}">
                            {{ number_format((float)$kpiLatest[$k['key']], $k['fmt'], ',', '.') }}
                        </p>
                        @if($k['key'] === 'publikasi')
                            <p class="mt-1 text-[10px] text-zinc-400">Target tahunan: {{ number_format((float)($kpiLatest['publikasi_target'] ?? 0), 0, ',', '.') }}</p>
                        @endif
                        {{-- Mini line chart --}}
                        <div class="mt-3 -mx-4 -mb-4">
                            <svg viewBox="0 0 344 100" class="h-16 w-full overflow-visible">
                                <polygon points="{{ $pts }} 310,88 {{ $firstX }},88"
                                    fill="{{ $k['color'] }}" fill-opacity="0.08"/>
                                <polyline points="{{ $pts }}" fill="none" stroke="{{ $k['color'] }}"
                                    stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>
                                <circle cx="310" cy="{{ $lastY }}" r="4"
                                    fill="{{ $k['color'] }}" stroke="white" stroke-width="2"/>
                                @foreach (array_values($trendData) as $ti => $td)
                                    @php
                                        $total = count($trendData);
                                        $tx = round(34 + ($ti / max(1, $total-1)) * 276, 1);
                                    @endphp
                                    <text x="{{ $tx }}" y="99" text-anchor="middle" font-size="8" fill="#a1a1aa">{{ $td['year'] }}</text>
                                @endforeach
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @if ($isCentralAdmin)
        <section class="section-box overflow-hidden rounded-2xl">
            <div class="border-b border-zinc-100 px-5 py-4">
                <p class="text-sm font-bold text-zinc-800">Monitoring Semua Program Studi</p>
                <p class="mt-0.5 text-xs text-zinc-500">Ringkasan baca-saja untuk melihat kelengkapan data tiap prodi.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 bg-zinc-50/80 text-[10px] font-extrabold uppercase tracking-wider text-zinc-500">
                            <th class="px-5 py-3">Program Studi</th>
                            <th class="px-4 py-3 text-right">User</th>
                            <th class="px-4 py-3 text-right">Tahun Data</th>
                            <th class="px-4 py-3 text-right">Dokumen</th>
                            <th class="px-4 py-3 text-right">Program</th>
                            <th class="px-4 py-3 text-right">Mahasiswa</th>
                            <th class="px-4 py-3 text-right">IPK</th>
                            <th class="px-4 py-3 text-right">Publikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($prodiSummaries as $summary)
                            <tr class="border-b border-zinc-100 last:border-0 hover:bg-zinc-50/70">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-zinc-900">{{ $summary['name'] }}</div>
                                    <div class="mt-0.5 text-xs font-semibold text-zinc-400">{{ $summary['code'] }}{{ $summary['latest_year'] ? ' - Data terbaru '.$summary['latest_year'] : '' }}</div>
                                </td>
                                <td class="px-4 py-4 text-right font-semibold text-zinc-700">{{ number_format($summary['users'], 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-zinc-700">{{ number_format($summary['years'], 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-zinc-700">{{ number_format($summary['documents'], 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-zinc-700">{{ number_format($summary['programs'], 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-indigo-700">{{ number_format($summary['mahasiswa'], 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-teal-700">{{ number_format($summary['ipk'], 2, ',', '.') }}</td>
                                <td class="px-4 py-4 text-right font-semibold text-amber-700">{{ number_format($summary['publikasi'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-5 py-10 text-center text-sm text-zinc-400">Belum ada program studi aktif.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif
{{-- ── Bottom row: capaian + quick links ── --}}
    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Capaian persentase --}}
        <div class="section-box rounded-2xl p-5 lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <p class="text-sm font-bold text-zinc-800">Capaian Indikator — {{ $tahunTerbaru }}</p>
                @if (! $isCentralAdmin)
                    <a href="{{ route('admin.dashboard-data') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Edit</a>
                @endif
            </div>
            @if (!empty($capaian))
                <div class="space-y-3">
                    @php
                        $barColors = ['#6366f1','#0f766e','#d97706','#7c3aed'];
                    @endphp
                    @foreach ($capaian as $ci => $item)
                        @php $pct = max(0, min(100, $item['percent'])); @endphp
                        <div>
                            <div class="mb-1 flex justify-between text-xs font-semibold">
                                <span class="text-zinc-600">{{ $item['label'] }}</span>
                                <span class="text-zinc-800">{{ $pct }}%</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-zinc-100">
                                <div class="h-full rounded-full transition-all duration-500"
                                    style="width:{{ $pct }}%; background:{{ $barColors[$ci] ?? '#6366f1' }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-zinc-400">Belum ada data capaian.</p>
            @endif
        </div>

        {{-- Quick actions + recent feedback --}}
        <div class="space-y-4">
            {{-- Quick links --}}
            <div class="section-box rounded-2xl p-4">
                <p class="mb-3 text-xs font-bold uppercase tracking-widest text-zinc-400">Akses Cepat</p>
                <div class="space-y-1.5">
                    @php
                        $quickLinks = $isCentralAdmin ? [
                            ['label'=>'User & Program Studi','route'=>'admin.users',          'icon'=>'M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m7-10a4 4 0 100-8 4 4 0 000 8zm11 10v-2a4 4 0 00-3-3.87m-1-11.26a4 4 0 010 7.75'],
                        ] : [
                            ['label'=>'Kelola Statistik',   'route'=>'admin.dashboard-data',  'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                            ['label'=>'Statistik Bulanan',  'route'=>'admin.monthly-stats',   'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ['label'=>'Konten Beranda',     'route'=>'admin.beranda-content', 'icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                            ['label'=>'Laporan Tahunan',    'route'=>'admin.annual-report',   'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['label'=>'Umpan Balik',        'route'=>'admin.feedback',        'icon'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                        ];
                    @endphp
                    @foreach ($quickLinks as $ql)
                        <a wire:navigate href="{{ route($ql['route']) }}"
                            class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-xs font-semibold text-zinc-600 transition hover:bg-indigo-50 hover:text-indigo-700">
                            <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $ql['icon'] }}"/>
                            </svg>
                            {{ $ql['label'] }}
                            @if ($ql['route'] === 'admin.feedback' && $unreadFeedback > 0)
                                <span class="ml-auto rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] font-bold text-white">{{ $unreadFeedback }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Recent feedback --}}
            @if (! $isCentralAdmin && $recentFeedback->isNotEmpty())
                <div class="section-box rounded-2xl p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-widest text-zinc-400">Feedback Terbaru</p>
                        <a href="{{ route('admin.feedback') }}" class="text-[10px] font-semibold text-indigo-600 hover:underline">Lihat semua</a>
                    </div>
                    <div class="space-y-2">
                        @foreach ($recentFeedback as $fb)
                            <div class="rounded-lg border border-zinc-100 bg-zinc-50 px-3 py-2">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-xs font-semibold text-zinc-700">{{ $fb->name }}</p>
                                    <span class="flex-shrink-0 text-[10px] text-zinc-400">{{ $fb->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-0.5 truncate text-[11px] text-zinc-500">{{ $fb->subject }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>

</div>
