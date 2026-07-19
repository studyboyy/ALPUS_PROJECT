<div class="space-y-8">

    {{-- ═══════════════════════════════════════════════════════
         HERO CAROUSEL — modern split layout
    ═══════════════════════════════════════════════════════ --}}
    @php
        $heroItems   = collect($homeContent['hero_items'] ?? [])->values();
        $heroCount   = $heroItems->count();
        $tahunLaporanAktif  = data_get($daftarTahun, 0, $tahunDipilih);
        $namaProdiSuffix    = trim((string) data_get($homeContent, 'kaprodi_name', ''));
        $tahunCepatAwal     = collect($daftarTahun)->take(5)->all();
        $tahunCepatLanjutan = collect($daftarTahun)->slice(5)->all();
    @endphp

    <section
        class="js-hero-carousel relative overflow-hidden rounded-3xl shadow-2xl"
        style="min-height: 480px;">

        {{-- ── Slide backgrounds ── --}}
        @foreach ($heroItems as $index => $hero)
            @php
                $img = data_get($hero, 'image_url', $homeContent['hero_background_url'] ?? '');
                $hasImg = !empty($img);
            @endphp
            <div class="js-hero-slide absolute inset-0 transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}">
                @if ($hasImg)
                    {{-- Image slide --}}
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image:url('{{ $img }}');"></div>
                    <div class="absolute inset-0" style="background: linear-gradient(110deg, rgba(3,17,40,0.88) 0%, rgba(7,42,90,0.72) 45%, rgba(3,17,40,0.45) 100%);"></div>
                @else
                    {{-- Pure gradient fallback — looks great even without an image --}}
                    <div class="absolute inset-0" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 40%, #0c2a4a 70%, #0f172a 100%);"></div>
                    {{-- Decorative blobs --}}
                    <div class="absolute -top-20 -right-20 h-96 w-96 rounded-full opacity-20" style="background: radial-gradient(circle, #3b82f6, transparent 70%);"></div>
                    <div class="absolute -bottom-24 -left-16 h-80 w-80 rounded-full opacity-15" style="background: radial-gradient(circle, #0ea5e9, transparent 70%);"></div>
                    <div class="absolute top-1/2 right-1/4 h-60 w-60 rounded-full opacity-10" style="background: radial-gradient(circle, #8b5cf6, transparent 70%);"></div>
                @endif
                {{-- Subtle grid overlay for depth --}}
                <div class="absolute inset-0 opacity-[0.04]" style="background-image: linear-gradient(rgba(255,255,255,0.15) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.15) 1px, transparent 1px); background-size: 40px 40px;"></div>
            </div>
        @endforeach

        {{-- ── Content layer ── --}}
        <div class="relative z-10 flex min-h-[480px] flex-col justify-between p-6 md:p-10 lg:p-14">

            {{-- Top: badge --}}
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-[0.2em] text-sky-100 backdrop-blur-sm">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                    Laporan Tahunan {{ $tahunLaporanAktif }}
                </span>
            </div>

            {{-- Middle: main content --}}
            <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_auto]">

                {{-- Left: text --}}
                <div class="max-w-2xl text-white">
                    <h2 class="display-font text-4xl font-bold leading-[1.15] tracking-tight md:text-5xl lg:text-6xl">
                        {{ data_get($homeContent, 'header_title_text', 'Laporan Tahunan Kepala Program Studi') }}
                    </h2>
                    <p class="mt-3 text-lg font-semibold text-sky-200">
                        {{ data_get($homeContent, 'kaprodi_title', 'Kepala Program Studi') }}
                    </p>
                    <p class="mt-4 max-w-xl text-sm leading-relaxed text-slate-300 md:text-base">
                        {{ data_get($homeContent, 'kaprodi_quote', 'Portal resmi untuk ringkasan kinerja, capaian akademik, statistik, dokumen pendukung, dan dokumentasi kegiatan Program Studi.') }}{{ $namaProdiSuffix !== '' ? ' — ' . $namaProdiSuffix : '' }}
                    </p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <a wire:navigate href="{{ route('laporan') }}"
                            class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-bold text-sky-900 shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Lihat Laporan
                        </a>
                        <a href="#statistik-beranda"
                            class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/20">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Data Statistik
                        </a>
                    </div>

                    {{-- Quick year links --}}
                    <div class="mt-6">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/50">Tahun:</span>
                            @foreach ($tahunCepatAwal as $tahun)
                                <a wire:navigate href="{{ route('laporan', ['year' => $tahun]) }}"
                                    class="rounded-full border border-white/20 bg-white/8 px-2.5 py-1 text-[11px] font-semibold text-white/80 transition hover:border-white/50 hover:bg-white/15 hover:text-white">{{ $tahun }}</a>
                            @endforeach
                            @if (count($tahunCepatLanjutan) > 0)
                                @foreach ($tahunCepatLanjutan as $tahun)
                                    <a wire:navigate href="{{ route('laporan', ['year' => $tahun]) }}"
                                        class="rounded-full border border-white/20 bg-white/8 px-2.5 py-1 text-[11px] font-semibold text-white/80 transition hover:border-white/50 hover:bg-white/15 hover:text-white">{{ $tahun }}</a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right: decorative stat cards — removed --}}
            </div>

            {{-- Bottom: dots di kanan --}}
            @if ($heroCount > 1)
            <div class="mt-8 flex items-center justify-end">
                <div class="flex items-center gap-2">
                    @foreach ($heroItems as $index => $hero)
                        <button type="button" data-hero-dot="{{ $index }}"
                            class="js-hero-dot transition-all duration-300 rounded-full
                                   {{ $index === 0
                                       ? 'h-2.5 w-8 bg-white shadow-lg shadow-white/30'
                                       : 'h-2 w-2 bg-white/35 hover:bg-white/60' }}">
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- /content layer --}}
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
        <article class="section-box relative rounded-2xl p-6  lg:col-span-2" x-data="{ openMore: false }">
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
                        <p class="mt-1 text-2xl font-bold {{ $kpi['valueClass'] }}">{{ $kpi['value'] }}</p>
                    </div>
                @endforeach
            </div>
            {{-- dots carousel ada di dalam hero section --}}
        </article>

        <article class="section-box rounded-2xl p-6"
                 wire:ignore.self>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="text-sm font-semibold">Tren Indikator Utama</p>
                    <p class="mt-0.5 text-[11px] text-(--muted)">{{ data_get($chartJsData, 'rangeLabel', '') }}</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" wire:click="pilihTrendMode('year')"
                        class="rounded-full px-3 py-1.5 text-[11px] font-semibold transition {{ data_get($chartJsData, 'trendMode', 'year') === 'year' ? 'bg-(--accent) text-white shadow-sm' : 'border border-(--line) bg-white text-(--muted) hover:bg-slate-50' }}">Per Tahun</button>
                    <button type="button" wire:click="pilihTrendMode('all')"
                        class="rounded-full px-3 py-1.5 text-[11px] font-semibold transition {{ data_get($chartJsData, 'trendMode', 'year') === 'all' ? 'bg-(--accent) text-white shadow-sm' : 'border border-(--line) bg-white text-(--muted) hover:bg-slate-50' }}">Semua Tahun</button>
                </div>
            </div>

            @php
                $bjAll  = data_get($chartJsData, 'chartAll', ['labels'=>[],'datasets'=>[]]);
                $bjMode = data_get($chartJsData, 'trendMode', 'year');
            @endphp

            <div class="mt-4">
                <div
                    wire:key="beranda-chart--{{ $bjMode }}--{{ $tahunDipilih }}"
                    x-data="prodiChartInit('beranda-chart--{{ $bjMode }}--{{ $tahunDipilih }}', {{ json_encode($bjAll['labels'], JSON_UNESCAPED_UNICODE) }}, {{ json_encode($bjAll['datasets'], JSON_UNESCAPED_UNICODE) }}, true)"
                    x-init="boot()"
                    style="position:relative; height:220px; width:100%;">
                    <canvas id="beranda-chart--{{ $bjMode }}--{{ $tahunDipilih }}" style="display:block; width:100%; height:100%;"></canvas>
                </div>
            </div>
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
                    <a wire:navigate href="{{ $item['detail_url'] }}"
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
                            <a wire:navigate href="{{ $item['detail_url'] }}"
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
            <p class="text-[11px] font-bold uppercase tracking-widest text-(--olive)">Kutipan Kepala Prodi</p>
            <blockquote class="mt-4 border-l-3 border-blue-200 pl-4 text-sm italic leading-relaxed text-(--muted)">
                "{{ $homeContent['kaprodi_quote'] }}"
            </blockquote>
            <div class="mt-5 flex items-center gap-3">
                <img src="{{ $homeContent['kaprodi_photo_url'] }}" alt="{{ $homeContent['kaprodi_name'] }}"
                    class="h-12 w-12 rounded-full object-cover ring-2 ring-white shadow-sm">
                <div>
                    <p class="text-sm font-bold text-slate-800">{{ $homeContent['kaprodi_name'] }}</p>
                    <p class="text-xs text-(--muted)">{{ $homeContent['kaprodi_title'] }}</p>
                </div>
            </div>
            <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="rounded-xl border border-(--line) bg-slate-50 p-3 text-center">
                    <p class="text-lg font-extrabold text-(--accent)">{{ $mitraDanKegiatanStats['mitraAktif'] ?? 0 }}</p>
                    <p class="mt-0.5 text-[11px] font-semibold text-slate-500">Mitra Aktif</p>
                </div>
                <div class="rounded-xl border border-(--line) bg-slate-50 p-3 text-center">
                    <p class="text-lg font-extrabold text-(--olive)">{{ $mitraDanKegiatanStats['kegiatanEksternal'] ?? 0 }}</p>
                    <p class="mt-0.5 text-[11px] font-semibold text-slate-500">Kegiatan {{ $mitraDanKegiatanStats['tahun'] ?? $tahunDipilih }}</p>
                </div>
            </div>
        </article>
    </section>

    <section class="section-box rounded-2xl p-6 md:p-8">
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-widest text-(--olive)">Dokumentasi Terbaru</p>
                <h3 class="display-font mt-1 text-3xl">Galeri Kegiatan</h3>
            </div>
            <a wire:navigate href="{{ route('galeri') }}"
                class="btn-outline !py-2 !px-4 !text-xs">
                Lihat Semua
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (collect($homeContent['gallery_items'])->reverse()->take(6) as $item)
                <a wire:navigate href="{{ route('galeri') }}"
                    class="group overflow-hidden rounded-2xl bg-slate-100 ring-1 ring-slate-200 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:ring-blue-200">
                    <div class="relative overflow-hidden">
                        <img src="{{ data_get($item, 'image_url') }}" alt="{{ data_get($item, 'title') }}"
                            class="h-48 w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/50 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                        <span class="absolute left-3 top-3 rounded-full bg-slate-900/55 px-2.5 py-0.5 text-[10px] font-semibold text-white backdrop-blur-sm">
                            {{ data_get($item, 'category', '') }}
                        </span>
                    </div>
                    <div class="px-4 py-3">
                        <p class="text-sm font-semibold leading-snug text-slate-800">{{ data_get($item, 'title') }}</p>
                        @if (data_get($item, 'description'))
                            <p class="mt-0.5 line-clamp-1 text-xs text-(--muted)">{{ data_get($item, 'description') }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6 text-center">
            <a wire:navigate href="{{ route('galeri') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Lihat Galeri Lengkap
            </a>
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
                    class="space-y-4 rounded-2xl border border-(--line) bg-white p-6 shadow-sm">
                    @if (session()->has('contact_status'))
                        <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                            <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm font-medium text-emerald-800">{{ session('contact_status') }}</p>
                        </div>
                    @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Nama</label>
                            <input wire:model.defer="feedbackName" type="text" placeholder="Nama lengkap Anda"
                                class="form-input">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-600">Email</label>
                            <input wire:model.defer="feedbackEmail" type="email" placeholder="email@contoh.com"
                                class="form-input">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Subjek</label>
                        <input wire:model.defer="feedbackSubject" type="text" placeholder="Topik umpan balik"
                            class="form-input">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">Pesan / Saran</label>
                        <textarea wire:model.defer="feedbackMessage" rows="5" placeholder="Tuliskan pesan atau saran Anda…"
                            class="form-input resize-none"></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Kirim Umpan Balik
                    </button>
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

            const initHeroCarousel = () => {
                const container = document.querySelector('.js-hero-carousel');
                if (!container) return;

                const slides = Array.from(container.querySelectorAll('.js-hero-slide'));
                const dots   = Array.from(container.querySelectorAll('.js-hero-dot'));
                if (slides.length <= 1) return;

                let activeIndex = 0;

                const applyActive = (index) => {
                    activeIndex = (index + slides.length) % slides.length;

                    slides.forEach((slide, i) => {
                        slide.classList.toggle('opacity-100', i === activeIndex);
                        slide.classList.toggle('opacity-0',  i !== activeIndex);
                    });

                    dots.forEach((dot, i) => {
                        if (i === activeIndex) {
                            dot.classList.add('w-8', 'bg-white', 'shadow-lg');
                            dot.classList.remove('w-2', 'bg-white/35', 'hover:bg-white/60', 'h-2');
                            dot.classList.add('h-2.5');
                        } else {
                            dot.classList.remove('w-8', 'bg-white', 'shadow-lg', 'h-2.5');
                            dot.classList.add('w-2', 'h-2', 'bg-white/35');
                        }
                    });
                };

                // Dot clicks
                dots.forEach((dot, i) => dot.addEventListener('click', () => {
                    applyActive(i);
                    resetTimer();
                }));

                // Auto-advance
                const resetTimer = () => {
                    if (window.__heroCarouselTimer) clearInterval(window.__heroCarouselTimer);
                    window.__heroCarouselTimer = setInterval(() => applyActive(activeIndex + 1), 5000);
                };
                resetTimer();

                // Pause on hover
                container.addEventListener('mouseenter', () => {
                    if (window.__heroCarouselTimer) clearInterval(window.__heroCarouselTimer);
                });
                container.addEventListener('mouseleave', resetTimer);
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

            document.addEventListener('livewire:navigated', animateStatVisuals);
            document.addEventListener('livewire:navigated', initHeroCarousel);
            window.addEventListener('statistik-updated', animateStatVisuals);

            animateStatVisuals();
            initHeroCarousel();
        })();
    </script>
</div>
