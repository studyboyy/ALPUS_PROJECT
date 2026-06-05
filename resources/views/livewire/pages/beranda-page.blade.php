<div class="space-y-8">
    <section class="js-hero-carousel relative overflow-hidden rounded-3xl border border-white/20 p-6 shadow-xl md:p-10">
        <div class="absolute inset-0">
            @foreach (collect($homeContent['hero_items'] ?? [])->values() as $index => $hero)
                <div class="js-hero-slide absolute inset-0 bg-cover bg-center transition-opacity duration-700 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                    style="background-image: linear-gradient(120deg, rgba(4, 20, 42, 0.82), rgba(12, 58, 96, 0.45)), url('{{ data_get($hero, 'image_url', $homeContent['hero_background_url']) }}');">
                </div>
            @endforeach
        </div>
        <div class="relative z-10 max-w-3xl text-white">
            @php $tahunLaporanAktif = data_get($daftarTahun, 0, $tahunDipilih); @endphp
            @php $namaProdiSuffix = trim((string) data_get($homeContent, 'kaprodi_name', '')); @endphp
            <p class="inline-block rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em]">
                Laporan Tahunan {{ $tahunLaporanAktif }}</p>
            <h2 class="mt-4 display-font text-4xl leading-tight md:text-6xl">Laporan Tahunan Kepala Program Studi</h2>
            <p class="mt-3 text-xl font-semibold text-sky-100">Menuju Prodi Unggul, Inovatif, dan Berdaya Saing</p>
            <p class="mt-4 text-sm leading-relaxed text-slate-100 md:text-base">Portal resmi untuk ringkasan kinerja,
                capaian akademik, statistik, dokumen pendukung, dan dokumentasi kegiatan Program
                Studi{{ $namaProdiSuffix !== '' ? ' ' . $namaProdiSuffix : '' }}.</p>
            <div class="mt-7 flex flex-wrap gap-3">
                <a wire:navigate.hover href="{{ route('laporan') }}"
                    class="rounded-full bg-white px-5 py-3 text-sm font-bold text-sky-800 transition hover:-translate-y-0.5">Lihat
                    Laporan Tahunan</a>
                <a href="#statistik-beranda"
                    class="rounded-full border border-white/45 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20">Lihat
                    Data Statistik</a>
            </div>
            @php
                $tahunCepatAwal = collect($daftarTahun)->take(5)->all();
                $tahunCepatLanjutan = collect($daftarTahun)->slice(5)->all();
            @endphp
            <div class="mt-5 space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-100">Tautan Cepat
                        Tahun:</span>
                    @foreach ($tahunCepatAwal as $tahun)
                        <a wire:navigate.hover href="{{ route('laporan', ['year' => $tahun]) }}"
                            class="rounded-full border border-white/35 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/20">{{ $tahun }}</a>
                    @endforeach
                </div>
                @if (count($tahunCepatLanjutan) > 0)
                    <details class="text-white">
                        <summary class="cursor-pointer text-xs font-semibold text-sky-100">Lihat semua tahun</summary>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($tahunCepatLanjutan as $tahun)
                                <a wire:navigate.hover href="{{ route('laporan', ['year' => $tahun]) }}"
                                    class="rounded-full border border-white/35 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/20">{{ $tahun }}</a>
                            @endforeach
                        </div>
                    </details>
                @endif
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @php
            $quickColorMap = [
                'blue' => [
                    'border' => 'border-blue-500',
                    'iconBg' => 'from-blue-500 to-indigo-600',
                    'shadow' => 'shadow-blue-200',
                    'linkBg' => 'bg-blue-50',
                    'linkText' => 'text-blue-700',
                    'linkHover' => 'group-hover:bg-blue-100',
                ],
                'violet' => [
                    'border' => 'border-violet-500',
                    'iconBg' => 'from-violet-500 to-purple-600',
                    'shadow' => 'shadow-violet-200',
                    'linkBg' => 'bg-violet-50',
                    'linkText' => 'text-violet-700',
                    'linkHover' => 'group-hover:bg-violet-100',
                ],
                'emerald' => [
                    'border' => 'border-emerald-500',
                    'iconBg' => 'from-emerald-500 to-teal-600',
                    'shadow' => 'shadow-emerald-200',
                    'linkBg' => 'bg-emerald-50',
                    'linkText' => 'text-emerald-700',
                    'linkHover' => 'group-hover:bg-emerald-100',
                ],
                'amber' => [
                    'border' => 'border-amber-500',
                    'iconBg' => 'from-amber-500 to-orange-600',
                    'shadow' => 'shadow-amber-200',
                    'linkBg' => 'bg-amber-50',
                    'linkText' => 'text-amber-700',
                    'linkHover' => 'group-hover:bg-amber-100',
                ],
            ];
            $quickIconMap = [
                'chart' =>
                    'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'document' =>
                    'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'users' =>
                    'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                'award' =>
                    'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
            ];
            $quickHighlights = collect($homeContent['quick_highlights'] ?? []);
        @endphp

        @foreach ($quickHighlights as $highlight)
            @php
                $colors = $quickColorMap[data_get($highlight, 'color_key', 'blue')] ?? $quickColorMap['blue'];
                $iconPath = $quickIconMap[data_get($highlight, 'icon_key', 'chart')] ?? $quickIconMap['chart'];
                $link = data_get($highlight, 'link', '#');

                // Direct link to gallery for Prestasi Mahasiswa
                if (data_get($highlight, 'title') === 'Prestasi Mahasiswa') {
                    $link = route('galeri.category', ['kategori' => 'prestasi-mahasiswa']);
                }
            @endphp
            <article
                class="section-box group rounded-2xl border-t-4 {{ $colors['border'] }} p-5 transition-all duration-200 hover:-translate-y-1 hover:shadow-xl">
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-linear-to-br {{ $colors['iconBg'] }} shadow-md {{ $colors['shadow'] }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                    </svg>
                </div>
                <h3 class="mt-3 text-lg font-bold">{{ data_get($highlight, 'title', '-') }}</h3>
                <p class="mt-2 text-sm text-(--muted)">{{ data_get($highlight, 'description', '-') }}</p>
                <a href="{{ $link }}"
                    class="mt-4 inline-block rounded-full {{ $colors['linkBg'] }} px-3 py-1 text-xs font-semibold {{ $colors['linkText'] }} transition {{ $colors['linkHover'] }}">{{ data_get($highlight, 'link_label', 'Lihat Detail') }}</a>
            </article>
        @endforeach
    </section>

    <section id="statistik-beranda" class="grid gap-4 lg:grid-cols-2 scroll-mt-28">
        <article class="section-box relative rounded-2xl p-6 pb-20 lg:col-span-2" x-data="{ openMore: false }">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="display-font text-4xl leading-tight">Data dan Statistik Prodi</h3>
                @php
                    $tahunStatAwal = collect($daftarTahun)->take(6)->all();
                    $tahunStatLanjutan = collect($daftarTahun)->slice(6)->all();
                @endphp
                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tahunStatAwal as $tahun)
                            <button type="button" wire:click="pilihTahun({{ $tahun }})"
                                class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $tahunDipilih === $tahun ? 'bg-(--accent) text-white' : 'border border-(--line) bg-white hover:bg-slate-50' }}">{{ $tahun }}</button>
                        @endforeach
                    </div>
                    @if (count($tahunStatLanjutan) > 0)
                        <details>
                            <summary class="cursor-pointer text-xs font-semibold text-(--muted)">Lihat semua tahun
                            </summary>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($tahunStatLanjutan as $tahun)
                                    <button type="button" wire:click="pilihTahun({{ $tahun }})"
                                        class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $tahunDipilih === $tahun ? 'bg-(--accent) text-white' : 'border border-(--line) bg-white hover:bg-slate-50' }}">{{ $tahun }}</button>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </div>
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-4">
                @foreach ($statistikAktif['kpi'] as $kpi)
                    <div class="rounded-xl border p-4 {{ $kpi['boxClass'] }}">
                        <p class="text-xs text-(--muted)">{{ $kpi['label'] }}</p>
                        <p class="mt-1 text-2xl font-bold {{ $kpi['valueClass'] }}">
                            <span class="js-countup" data-countup="{{ $kpi['countTarget'] }}"
                                data-decimals="{{ $kpi['decimals'] }}">{{ $kpi['value'] }}</span>
                        </p>
                    </div>
                @endforeach
            </div>
            {{-- Hero carousel navigation dots (satu set, di luar loop KPI) --}}
            <div class="mt-4 flex items-center gap-2">
                @foreach (collect($homeContent['hero_items'] ?? [])->values() as $index => $hero)
                    <button type="button" data-hero-dot="{{ $index }}"
                        class="js-hero-dot h-2.5 rounded-full bg-slate-300 transition-all {{ $index === 0 ? 'w-7 bg-(--accent)' : 'w-2.5' }}"></button>
                @endforeach
            </div>
        </article>

        <article class="section-box rounded-2xl p-6"
                 x-data="berandaTrendChart({{ json_encode(data_get($statistikAktif, 'trend.tooltipData', []), JSON_UNESCAPED_UNICODE) }})"
                 x-init="init()">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="text-sm font-semibold">Tren Indikator Utama</p>
                    <p class="mt-0.5 text-[11px] text-(--muted)">{{ data_get($statistikAktif, 'trend.rangeLabel', '') }}</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" wire:click="pilihTrendMode('year')"
                        class="rounded-full px-3 py-1.5 text-[11px] font-semibold transition {{ data_get($statistikAktif, 'trend.trendMode', 'year') === 'year' ? 'bg-(--accent) text-white shadow-sm' : 'border border-(--line) bg-white text-(--muted) hover:bg-slate-50' }}">Per Tahun</button>
                    <button type="button" wire:click="pilihTrendMode('all')"
                        class="rounded-full px-3 py-1.5 text-[11px] font-semibold transition {{ data_get($statistikAktif, 'trend.trendMode', 'year') === 'all' ? 'bg-(--accent) text-white shadow-sm' : 'border border-(--line) bg-white text-(--muted) hover:bg-slate-50' }}">Semua Tahun</button>
                </div>
            </div>

            <div class="relative mt-4 rounded-xl border border-(--line) bg-white p-4 shadow-sm">
                <svg id="beranda-trend-svg" viewBox="0 0 340 148" class="h-44 w-full overflow-visible"
                     @mousemove="onMouseMove($event)" @mouseleave="onMouseLeave()">

                    @foreach (data_get($statistikAktif, 'trend.yTicks', []) as $tick)
                        <line x1="34" y1="{{ data_get($tick, 'y') }}" x2="310"
                            y2="{{ data_get($tick, 'y') }}" stroke="#e2e8f0" stroke-width="1" />
                        <text x="30" y="{{ data_get($tick, 'y') + 3.5 }}" text-anchor="end"
                            font-size="8.5" fill="#94a3b8">{{ data_get($tick, 'label') }}</text>
                    @endforeach

                    {{-- Area fills --}}
                    @php
                        $mPts = $statistikAktif['trend']['mahasiswa'] ?? '';
                        $firstMX = $mPts ? (float) explode(',', explode(' ', trim($mPts))[0])[0] : 34;
                        $lastX   = data_get($statistikAktif,'trend.lastX', 310);
                    @endphp
                    <polygon points="{{ $mPts }} {{ $lastX }},128 {{ $firstMX }},128"
                        fill="#1d4ed8" fill-opacity="0.05" />

                    <polyline points="{{ $statistikAktif['trend']['mahasiswa'] }}" fill="none" stroke="#1d4ed8" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />
                    <polyline points="{{ $statistikAktif['trend']['ipk'] }}" fill="none" stroke="#0f766e" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />
                    <polyline points="{{ $statistikAktif['trend']['publikasi'] }}" fill="none" stroke="#ea580c" stroke-width="1.5" stroke-dasharray="5 3" stroke-linejoin="round" stroke-linecap="round" />
                    <polyline points="{{ $statistikAktif['trend']['dosen'] }}" fill="none" stroke="#fb923c" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round" />
                    <polyline points="{{ data_get($statistikAktif,'trend.progressYtd','') }}" fill="none" stroke="#7c3aed" stroke-width="1.5" stroke-dasharray="3 2" stroke-linejoin="round" stroke-linecap="round" />

                    {{-- End dots --}}
                    <circle cx="{{ $lastX }}" cy="{{ $statistikAktif['trend']['mahasiswaLastY'] }}" r="4" fill="#1d4ed8" stroke="white" stroke-width="1.5" />
                    <circle cx="{{ $lastX }}" cy="{{ $statistikAktif['trend']['ipkLastY'] }}" r="4" fill="#0f766e" stroke="white" stroke-width="1.5" />
                    <circle cx="{{ $lastX }}" cy="{{ $statistikAktif['trend']['publikasiLastY'] }}" r="3.5" fill="#ea580c" stroke="white" stroke-width="1.5" />
                    <circle cx="{{ $lastX }}" cy="{{ $statistikAktif['trend']['dosenLastY'] }}" r="3.5" fill="#fb923c" stroke="white" stroke-width="1.5" />
                    <circle cx="{{ $lastX }}" cy="{{ data_get($statistikAktif,'trend.progressLastY',96) }}" r="3.5" fill="#7c3aed" stroke="white" stroke-width="1.5" />

                    {{-- Hover crosshair --}}
                    <line x-show="hoverIdx !== null" :x1="hoverX" y1="20" :x2="hoverX" y2="128"
                        stroke="#94a3b8" stroke-width="1" stroke-dasharray="3 2" />

                    @foreach (data_get($statistikAktif, 'trend.yearTicks', []) as $tick)
                        <text x="{{ data_get($tick, 'x') }}" y="143" text-anchor="middle"
                            font-size="8.5" fill="#94a3b8">{{ data_get($tick, 'label', data_get($tick, 'year')) }}</text>
                    @endforeach
                </svg>

                {{-- Tooltip --}}
                <div x-show="hoverIdx !== null" x-cloak
                     :style="'left:' + tooltipLeft + 'px; top:' + tooltipTop + 'px'"
                     class="pointer-events-none absolute z-20 min-w-[150px] rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-lg text-[11px] leading-5">
                    <p class="mb-1 font-semibold text-slate-700" x-text="hoverLabel"></p>
                    <p class="text-blue-700">Mahasiswa: <span class="font-semibold" x-text="hoverMahasiswa"></span></p>
                    <p class="text-teal-700">IPK: <span class="font-semibold" x-text="hoverIpk"></span></p>
                    <p class="text-orange-600">Publikasi: <span class="font-semibold" x-text="hoverPublikasi"></span></p>
                    <p class="text-orange-400">Dosen: <span class="font-semibold" x-text="hoverDosen"></span></p>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap gap-3 text-[10px] font-medium text-slate-500">
                <span class="flex items-center gap-1"><span class="inline-block h-1.5 w-4 rounded bg-blue-700"></span>Mahasiswa</span>
                <span class="flex items-center gap-1"><span class="inline-block h-1.5 w-4 rounded bg-teal-700"></span>IPK</span>
                <span class="flex items-center gap-1"><span class="inline-block h-1.5 w-4 rounded bg-orange-600" style="border-top:2px dashed #ea580c;height:0"></span>Publikasi</span>
                <span class="flex items-center gap-1"><span class="inline-block h-1.5 w-4 rounded bg-orange-400"></span>Dosen</span>
                <span class="flex items-center gap-1"><span class="inline-block h-1.5 w-4 rounded bg-violet-600"></span>Progres</span>
            </div>

            <script>
            function berandaTrendChart(tooltipData) {
                return {
                    hoverIdx: null, hoverX: 0,
                    hoverLabel:'', hoverMahasiswa:'', hoverDosen:'', hoverIpk:'', hoverPublikasi:'',
                    tooltipLeft: 0, tooltipTop: 0, points: [],
                    init() {
                        const svg = document.getElementById('beranda-trend-svg');
                        if (!svg) return;
                        const poly = svg.querySelector('polyline');
                        if (!poly || !tooltipData || !tooltipData.length) return;
                        const raw = poly.getAttribute('points').trim().split(/\s+/);
                        this.points = raw.map(p => parseFloat(p.split(',')[0]));
                    },
                    onMouseMove(event) {
                        if (!tooltipData || !tooltipData.length || !this.points.length) return;
                        const svg = document.getElementById('beranda-trend-svg');
                        if (!svg) return;
                        const rect = svg.getBoundingClientRect();
                        const scaleX = rect.width / 340;
                        const mouseX = (event.clientX - rect.left) / scaleX;
                        let closest = 0, minDist = Infinity;
                        this.points.forEach((px, i) => { const d = Math.abs(px - mouseX); if (d < minDist) { minDist = d; closest = i; } });
                        if (minDist > 18) { this.hoverIdx = null; return; }
                        this.hoverIdx = closest;
                        this.hoverX = this.points[closest];
                        const d = tooltipData[closest] || {};
                        this.hoverLabel = d.label || '';
                        this.hoverMahasiswa = typeof d.mahasiswa === 'number' ? d.mahasiswa.toLocaleString('id-ID') : d.mahasiswa;
                        this.hoverDosen = typeof d.dosen === 'number' ? d.dosen.toLocaleString('id-ID') : d.dosen;
                        this.hoverIpk = typeof d.ipk === 'number' ? d.ipk.toFixed(2).replace('.', ',') : d.ipk;
                        this.hoverPublikasi = typeof d.publikasi === 'number' ? d.publikasi.toLocaleString('id-ID') : d.publikasi;
                        const container = svg.parentElement;
                        const cRect = container.getBoundingClientRect();
                        const svgLeft = this.hoverX * scaleX;
                        this.tooltipLeft = Math.max(4, Math.min(svgLeft - 75, cRect.width - 160));
                        this.tooltipTop = 4;
                    },
                    onMouseLeave() { this.hoverIdx = null; }
                };
            }
            </script>
        </article>

        <article class="section-box rounded-2xl p-6">
            <p class="text-sm font-semibold">Capaian Persentase</p>
            <div class="mt-6 space-y-4 text-sm">
                @foreach ($statistikAktif['capaian'] as $item)
                    <div>
                        <div class="mb-1 flex justify-between font-medium"><span>{{ $item['label'] }}</span><span
                                class="{{ $item['textClass'] }}">{{ $item['percent'] }}%</span></div>
                        <div class="h-2.5 overflow-hidden rounded-full {{ $item['trackClass'] }}"><span
                                class="js-progress-bar block h-full rounded-full bg-linear-to-r {{ $item['barClass'] }}"
                                data-target-width="{{ $item['percent'] }}%"
                                style="width: {{ $item['percent'] }}%"></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <article class="section-box relative rounded-2xl p-6 pb-20 lg:col-span-2" x-data="{ openMore: false }">
            @php
                $programPreviewItems = collect($programAgendaItems)->take(4);
                $programSisaItems = collect($programAgendaItems)->slice(4)->values();
            @endphp
            <div class="mb-4 flex items-center justify-between">
                <h3 class="display-font text-3xl">Program Unggulan & Agenda {{ $tahunDipilih }}</h3>
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Update
                    Baru</span>
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                @foreach ($programPreviewItems as $item)
                    <a wire:navigate.hover href="{{ $item['detail_url'] }}"
                        class="block rounded-xl border p-4 transition hover:opacity-95 {{ $item['boxClass'] }}">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold uppercase tracking-[0.12em] {{ $item['badgeClass'] }}">{{ $item['tipe'] }}</span>
                            <span
                                class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold {{ $item['statusBadgeClass'] }}">{{ $item['status_label'] }}</span>
                        </div>
                        <h4 class="mt-2 font-semibold">{{ $item['title'] }}</h4>
                        <p class="mt-1 text-sm text-(--muted)">
                            {{ \Illuminate\Support\Str::limit($item['description'], 95, '...') }}</p>
                        <p class="mt-2 text-xs font-semibold text-indigo-700">Lihat detail lengkap</p>
                    </a>
                @endforeach
            </div>
            @if ($programSisaItems->isNotEmpty())
                <div class="mt-4 max-h-0 overflow-hidden opacity-0 transition-all duration-500 ease-in-out"
                    x-bind:style="openMore ? `max-height:${$el.scrollHeight}px;opacity:1;transform:translateY(0);` :
                        'max-height:0px;opacity:0;transform:translateY(-8px);'">
                    <div class="grid gap-3 pb-2 md:grid-cols-2">
                        @foreach ($programSisaItems as $item)
                            <a wire:navigate.hover href="{{ $item['detail_url'] }}"
                                class="block rounded-xl border p-4 transition hover:opacity-95 {{ $item['boxClass'] }}">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold uppercase tracking-[0.12em] {{ $item['badgeClass'] }}">{{ $item['tipe'] }}</span>
                                    <span
                                        class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold {{ $item['statusBadgeClass'] }}">{{ $item['status_label'] }}</span>
                                </div>
                                <h4 class="mt-2 font-semibold">{{ $item['title'] }}</h4>
                                <p class="mt-1 text-sm text-(--muted)">
                                    {{ \Illuminate\Support\Str::limit($item['description'], 95, '...') }}</p>
                                <p class="mt-2 text-xs font-semibold text-indigo-700">Lihat detail lengkap</p>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="absolute inset-x-0 bottom-5 flex justify-center">
                    <button type="button" @click="openMore = !openMore"
                        class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-linear-to-r from-white to-indigo-50 px-5 py-2 text-xs font-semibold text-indigo-700 shadow-sm transition hover:from-indigo-50 hover:to-indigo-100"
                        x-bind:aria-expanded="openMore ? 'true' : 'false'">
                        <span x-text="openMore ? 'Tutup' : 'Lihat Selengkapnya'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-300"
                            viewBox="0 0 20 20" fill="currentColor" :class="openMore ? 'rotate-180' : ''">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            @endif

        </article>

        <article class="section-box rounded-2xl p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-(--olive)">Kutipan Kepala Prodi</p>
            <p class="mt-4 text-sm leading-relaxed text-(--muted)">"{{ $homeContent['kaprodi_quote'] }}"</p>
            <div class="mt-5 flex items-center gap-3">
                <img src="{{ $homeContent['kaprodi_photo_url'] }}" alt="Kepala Prodi"
                    class="h-12 w-12 rounded-full object-cover">
                <div>
                    <p class="text-sm font-semibold">{{ $homeContent['kaprodi_name'] }}</p>
                    <p class="text-xs text-(--muted)">{{ $homeContent['kaprodi_title'] }}</p>
                </div>
            </div>
            <div class="mt-5 rounded-xl border border-(--line) bg-slate-50 p-3 text-xs text-(--muted)">
                Total mitra aktif: <strong>{{ $mitraDanKegiatanStats['mitraAktif'] ?? 0 }}</strong> | Kegiatan
                eksternal {{ $mitraDanKegiatanStats['tahun'] ?? $tahunDipilih }}:
                <strong>{{ $mitraDanKegiatanStats['kegiatanEksternal'] ?? 0 }}</strong>
            </div>
        </article>
    </section>

    <section class="section-box rounded-2xl p-6 md:p-8">
        <div class="mb-5 flex items-center justify-between gap-3">
            <h3 class="display-font text-3xl">Galeri Pilihan Beranda</h3>
            <a wire:navigate.hover href="{{ route('galeri') }}"
                class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Lihat Semua</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (collect($homeContent['gallery_items'])->reverse()->take(6) as $item)
                <article class="overflow-hidden rounded-xl border border-(--line)">
                    <img src="{{ data_get($item, 'image_url') }}" alt="{{ data_get($item, 'title') }}"
                        class="h-52 w-full object-cover">
                    <p class="px-4 py-3 text-sm font-semibold">{{ data_get($item, 'title') }}</p>
                </article>
            @endforeach
        </div>
        <div class="mt-5 text-center">
            <a wire:navigate.hover href="{{ route('galeri') }}"
                class="inline-flex items-center gap-2 rounded-full bg-linear-to-r from-indigo-600 to-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-200">Lihat
                Selengkapnya</a>
        </div>
    </section>

    <section id="kontak-beranda" class="scroll-mt-28">
        <div class="mb-5 flex items-center gap-4">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-linear-to-br from-rose-500 to-pink-600 shadow-md shadow-rose-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h3 class="display-font text-3xl leading-tight">Kontak dan Umpan Balik</h3>
                <p class="text-sm text-(--muted)">Kirim pertanyaan atau saran untuk peningkatan layanan Program Studi.
                </p>
            </div>
        </div>
        <div class="section-box rounded-2xl p-6 md:p-8">
            <div class="grid gap-6 lg:grid-cols-2">
                <div>
                    <div class="space-y-3 text-sm">
                        <div
                            class="flex items-center gap-3 rounded-xl border border-blue-100 bg-linear-to-r from-blue-50 to-indigo-50 p-3">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-(--ink)">Email</p>
                                <p class="text-(--muted)">{{ $homeContent['contact_email'] }}</p>
                            </div>
                        </div>
                        <div
                            class="flex items-center gap-3 rounded-xl border border-sky-100 bg-linear-to-r from-sky-50 to-cyan-50 p-3">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-white">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 5a2 2 0 012-2h3.28a2 2 0 011.897 1.368l1.068 3.206a2 2 0 01-.455 2.11l-1.27 1.27a16.042 16.042 0 006.586 6.586l1.27-1.27a2 2 0 012.11-.455l3.206 1.068A2 2 0 0121 15.72V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-(--ink)">Telepon & WhatsApp</p>
                                <div class="text-(--muted)">
                                    @if (!empty($homeContent['contact_phone']))
                                        <p>{{ $homeContent['contact_phone'] }}</p>
                                    @endif
                                    @if (!empty($homeContent['contact_whatsapp']))
                                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $homeContent['contact_whatsapp']) }}"
                                            target="_blank" rel="noreferrer"
                                            class="text-sky-700 transition hover:text-sky-800 hover:underline">WhatsApp:
                                            {{ $homeContent['contact_whatsapp'] }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex items-center gap-3 rounded-xl border border-emerald-100 bg-linear-to-r from-emerald-50 to-teal-50 p-3">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-(--ink)">Alamat</p>
                                <p class="text-(--muted)">{{ $homeContent['contact_address'] }}</p>
                            </div>
                        </div>
                        <div
                            class="flex items-center gap-3 rounded-xl border border-violet-100 bg-linear-to-r from-violet-50 to-purple-50 p-3">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-600 text-white">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-(--ink)">Media Sosial</p>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach ($homeContent['contact_social_links'] as $social)
                                        @if (!empty($social['url']))
                                            <a href="{{ $social['url'] }}" target="_blank" rel="noreferrer"
                                                class="rounded-full bg-white/80 px-3 py-1 text-xs font-semibold text-violet-700 ring-1 ring-violet-200 transition hover:bg-white">{{ $social['label'] }}</a>
                                        @else
                                            <span
                                                class="rounded-full bg-white/80 px-3 py-1 text-xs font-semibold text-violet-700 ring-1 ring-violet-200">{{ $social['label'] }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 overflow-hidden rounded-xl border border-(--line)">
                        <iframe title="Lokasi Prodi" class="h-52 w-full"
                            src="{{ $homeContent['contact_map_embed_url'] }}" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <form wire:submit="kirimUmpanBalik"
                    class="grid gap-3 rounded-2xl border border-(--line) bg-slate-50 p-5">
                    @if (session()->has('contact_status'))
                        <div
                            class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('contact_status') }}
                        </div>
                    @endif
                    <input wire:model.defer="feedbackName" type="text" placeholder="Nama"
                        class="rounded-xl border border-(--line) bg-white px-4 py-3 text-sm outline-none focus:border-(--accent)">
                    <input wire:model.defer="feedbackEmail" type="email" placeholder="Email"
                        class="rounded-xl border border-(--line) bg-white px-4 py-3 text-sm outline-none focus:border-(--accent)">
                    <input wire:model.defer="feedbackSubject" type="text" placeholder="Subjek"
                        class="rounded-xl border border-(--line) bg-white px-4 py-3 text-sm outline-none focus:border-(--accent)">
                    <textarea wire:model.defer="feedbackMessage" rows="5" placeholder="Pesan / Saran"
                        class="rounded-xl border border-(--line) bg-white px-4 py-3 text-sm outline-none focus:border-(--accent)"></textarea>
                    <button type="submit"
                        class="rounded-full bg-linear-to-r from-blue-600 to-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-md shadow-blue-200 transition hover:-translate-y-0.5 hover:shadow-lg">Kirim
                        Umpan Balik</button>
                </form>
            </div>
        </div>
    </section>

    <script>
        (function() {
            if (window.__berandaCountupBooted) {
                return;
            }
            window.__berandaCountupBooted = true;

            const animateCount = (el) => {
                const target = Number(el.dataset.countup || 0);
                const decimals = Number(el.dataset.decimals || 0);

                if (!Number.isFinite(target)) {
                    return;
                }

                const duration = 900;
                const startTime = performance.now();

                const step = (now) => {
                    const progress = Math.min((now - startTime) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const current = target * eased;

                    el.textContent = decimals > 0 ?
                        current.toLocaleString('id-ID', {
                            minimumFractionDigits: decimals,
                            maximumFractionDigits: decimals,
                        }) :
                        Math.round(current).toLocaleString('id-ID');

                    if (progress < 1) {
                        requestAnimationFrame(step);
                    }
                };

                requestAnimationFrame(step);
            };

            const initCountups = () => {
                document.querySelectorAll('.js-countup').forEach((el) => animateCount(el));
            };

            const initHeroCarousel = () => {
                const container = document.querySelector('.js-hero-carousel');
                if (!container) {
                    return;
                }

                const slides = Array.from(container.querySelectorAll('.js-hero-slide'));
                const dots = Array.from(container.querySelectorAll('.js-hero-dot'));
                if (slides.length <= 1) {
                    return;
                }

                let activeIndex = 0;
                const applyActive = (index) => {
                    activeIndex = index;

                    slides.forEach((slide, i) => {
                        slide.classList.toggle('opacity-100', i === activeIndex);
                        slide.classList.toggle('opacity-0', i !== activeIndex);
                    });

                    dots.forEach((dot, i) => {
                        dot.classList.toggle('w-7', i === activeIndex);
                        dot.classList.toggle('bg-white', i === activeIndex);
                        dot.classList.toggle('w-2.5', i !== activeIndex);
                        dot.classList.toggle('bg-white/55', i !== activeIndex);
                    });
                };

                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => applyActive(index));
                });

                if (window.__heroCarouselTimer) {
                    clearInterval(window.__heroCarouselTimer);
                }

                window.__heroCarouselTimer = setInterval(() => {
                    const nextIndex = (activeIndex + 1) % slides.length;
                    applyActive(nextIndex);
                }, 4500);
            };

            const animateTrendCharts = () => {
                document.querySelectorAll('.js-trend-chart').forEach((chart) => {
                    chart.querySelectorAll('.js-trend-line').forEach((line) => {
                        const length = line.getTotalLength();

                        line.style.transition = 'none';
                        line.style.strokeDasharray = String(length);
                        line.style.strokeDashoffset = String(length);

                        line.getBoundingClientRect();

                        line.style.transition =
                            'stroke-dashoffset 850ms cubic-bezier(0.22, 1, 0.36, 1)';
                        line.style.strokeDashoffset = '0';
                    });

                    chart.querySelectorAll('.js-trend-dot').forEach((dot) => {
                        if (typeof dot.animate === 'function') {
                            dot.animate([{
                                    transform: 'scale(0.4)',
                                    opacity: 0.25,
                                },
                                {
                                    transform: 'scale(1.25)',
                                    opacity: 1,
                                },
                                {
                                    transform: 'scale(1)',
                                    opacity: 1,
                                },
                            ], {
                                duration: 700,
                                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                            });
                        }
                    });
                });
            };

            const animateProgressBars = () => {
                document.querySelectorAll('.js-progress-bar').forEach((bar) => {
                    const targetWidth = bar.dataset.targetWidth || '0%';

                    bar.style.transition = 'none';
                    bar.style.width = '0%';

                    requestAnimationFrame(() => {
                        bar.style.transition = 'width 820ms cubic-bezier(0.22, 1, 0.36, 1)';
                        bar.style.width = targetWidth;
                    });
                });
            };

            const animateStatVisuals = () => {
                animateTrendCharts();
                animateProgressBars();
            };

            document.addEventListener('livewire:navigated', initCountups);
            document.addEventListener('livewire:navigated', animateStatVisuals);
            document.addEventListener('livewire:navigated', initHeroCarousel);
            window.addEventListener('statistik-updated', initCountups);
            window.addEventListener('statistik-updated', animateStatVisuals);

            initCountups();
            animateStatVisuals();
            initHeroCarousel();
        })();
    </script>
</div>
