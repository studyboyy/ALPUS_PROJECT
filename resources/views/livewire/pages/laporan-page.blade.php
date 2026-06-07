@php
    $barColors = ['from-blue-500 to-indigo-600','from-violet-500 to-purple-600','from-emerald-500 to-teal-600','from-amber-500 to-orange-500'];
    $kpiColors = [
        ['border'=>'border-blue-100','bg'=>'bg-blue-50','val'=>'text-blue-700'],
        ['border'=>'border-violet-100','bg'=>'bg-violet-50','val'=>'text-violet-700'],
        ['border'=>'border-emerald-100','bg'=>'bg-emerald-50','val'=>'text-emerald-700'],
        ['border'=>'border-amber-100','bg'=>'bg-amber-50','val'=>'text-amber-700'],
    ];
@endphp

<div class="grid gap-6 lg:grid-cols-[240px,1fr]">

    {{-- ── Sidebar ── --}}
    <aside class="space-y-4">
        <div class="section-box rounded-2xl p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-(--olive)">Pilih Tahun</p>
            <div class="mt-3 space-y-1.5">
                @foreach (collect($daftarTahun)->take(6)->all() as $tahun)
                    <button type="button" wire:click="pilihTahun({{ $tahun }})"
                        class="w-full rounded-xl px-4 py-2.5 text-left text-sm font-semibold transition-all
                               {{ $laporanAktif && $laporanAktif->year === $tahun
                                   ? 'bg-(--accent) text-white shadow-sm'
                                   : 'border border-(--line) bg-white text-slate-600 hover:border-blue-300 hover:text-(--accent)' }}">
                        {{ $tahun }}
                    </button>
                @endforeach
                @if (count(collect($daftarTahun)->slice(6)->all()) > 0)
                    <details class="mt-1">
                        <summary class="cursor-pointer rounded-xl border border-(--line) bg-white px-4 py-2.5 text-xs font-semibold text-slate-500 hover:bg-slate-50">
                            Tahun lainnya…
                        </summary>
                        <div class="mt-1.5 space-y-1.5">
                            @foreach (collect($daftarTahun)->slice(6)->all() as $tahun)
                                <button type="button" wire:click="pilihTahun({{ $tahun }})"
                                    class="w-full rounded-xl px-4 py-2.5 text-left text-sm font-semibold transition-all
                                           {{ $laporanAktif && $laporanAktif->year === $tahun
                                               ? 'bg-(--accent) text-white shadow-sm'
                                               : 'border border-(--line) bg-white text-slate-600 hover:border-blue-300' }}">
                                    {{ $tahun }}
                                </button>
                            @endforeach
                        </div>
                    </details>
                @endif
            </div>
        </div>

        <a href="{{ route('laporan.pdf', ['year' => $laporanAktif?->year]) }}"
            class="flex w-full items-center justify-center gap-2 rounded-2xl border border-(--line) bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-blue-300 hover:text-(--accent)">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Unduh PDF {{ $laporanAktif?->year }}
        </a>
        <a href="{{ route('laporan.pdf.semua') }}"
            class="flex w-full items-center justify-center gap-2 rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            PDF Semua Tahun
        </a>
    </aside>

    {{-- ── Main content ── --}}
    <div class="space-y-6">

        {{-- Header --}}
        <div class="section-box rounded-2xl p-6 md:p-8">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-(--olive)">Laporan Tahunan</p>
                    <h2 class="display-font mt-1.5 text-4xl leading-tight">{{ $laporanAktif?->year }}</h2>
                </div>
                <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700">
                    Diperbarui {{ now()->locale('id')->translatedFormat('F Y') }}
                </span>
            </div>

            {{-- KPI Cards --}}
            <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($laporanAktif?->kpi ?? [] as $ki => $item)
                    @php $kc = $kpiColors[$ki] ?? $kpiColors[0]; @endphp
                    <div class="rounded-xl border {{ $kc['border'] }} {{ $kc['bg'] }} p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ data_get($item, 'label') }}</p>
                        <p class="mt-2 text-2xl font-extrabold {{ $kc['val'] }}">
                            {{ number_format((float) data_get($item,'value',0),(int) data_get($item,'decimals',0),',','.') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Section summaries --}}
        @if ($sections->isNotEmpty())
            <div class="section-box rounded-2xl p-6">
                <h3 class="text-sm font-bold text-slate-800">Ringkasan Laporan</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach ($sections as $section)
                        <div class="rounded-xl border border-(--line) bg-slate-50 p-4">
                            <h4 class="font-semibold text-slate-800">{{ $section->title }}</h4>
                            <p class="mt-1.5 text-sm leading-relaxed text-(--muted)">{{ $section->summary }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Capaian + Tabel --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Progress bars --}}
            <div class="section-box rounded-2xl p-6">
                <h3 class="text-sm font-bold text-slate-800">Grafik Capaian Indikator</h3>
                <div class="mt-5 space-y-4">
                    @foreach ($laporanAktif?->capaian ?? [] as $ci => $item)
                        @php
                            $pct = max(0, min(100, (float) data_get($item, 'percent', 0)));
                            $bc  = $barColors[$ci] ?? $barColors[0];
                        @endphp
                        <div>
                            <div class="mb-1.5 flex justify-between text-xs font-semibold">
                                <span class="text-slate-700">{{ data_get($item,'label') }}</span>
                                <span class="text-slate-500">{{ number_format($pct,0,',','.') }}%</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r {{ $bc }}" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tabel KPI --}}
            <div class="section-box rounded-2xl p-6">
                <h3 class="text-sm font-bold text-slate-800">Tabel Indikator Utama</h3>
                <div class="mt-4 overflow-hidden rounded-xl border border-(--line)">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-slate-500">Indikator</th>
                                <th class="px-4 py-3 font-semibold text-slate-500 text-right">Realisasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-(--line)">
                            @foreach ($laporanAktif?->kpi ?? [] as $item)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 text-slate-700">{{ data_get($item,'label') }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800">
                                        {{ number_format((float) data_get($item,'value',0),(int) data_get($item,'decimals',0),',','.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Program & agenda --}}
        @if (!empty($programItems))
            <div class="section-box rounded-2xl p-6">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-sm font-bold text-slate-800">Program &amp; Agenda Terkait</h3>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ count($programItems) }} item</span>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach ($programItems as $item)
                        <div class="rounded-xl border border-(--line) bg-white p-4 shadow-sm">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ data_get($item,'type') }}</p>
                            <h4 class="mt-1.5 font-bold text-slate-800 leading-snug">{{ data_get($item,'title') }}</h4>
                            <p class="mt-1 text-sm leading-relaxed text-(--muted)">{{ data_get($item,'description') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Full section content --}}
        @foreach ($sections as $section)
            <section class="section-box rounded-2xl p-6 md:p-8">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <h3 class="display-font text-2xl leading-tight text-slate-800">{{ $section->title }}</h3>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">{{ $laporanAktif?->year }}</span>
                </div>
                @if ($section->summary)
                    <p class="mt-2 text-sm font-medium text-slate-600">{{ $section->summary }}</p>
                @endif
                <div class="mt-5 border-t border-(--line) pt-5 text-sm leading-relaxed text-slate-700 whitespace-pre-line">
                    {{ $section->content }}
                </div>
            </section>
        @endforeach

    </div>
</div>
