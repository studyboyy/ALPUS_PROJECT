@php
    $cjA        = data_get($chartJsData, 'chartA', ['labels'=>[],'datasets'=>[]]);
    $cjB        = data_get($chartJsData, 'chartB', ['labels'=>[],'datasets'=>[]]);
    $rangeLabel = data_get($chartJsData, 'rangeLabel', '');
    $trendMode  = data_get($chartJsData, 'trendMode', 'year');
    $kpiColors  = [
        ['border'=>'border-blue-100','bg'=>'bg-gradient-to-br from-blue-50 to-indigo-50','val'=>'text-blue-700'],
        ['border'=>'border-violet-100','bg'=>'bg-gradient-to-br from-violet-50 to-purple-50','val'=>'text-violet-700'],
        ['border'=>'border-emerald-100','bg'=>'bg-gradient-to-br from-emerald-50 to-teal-50','val'=>'text-emerald-700'],
        ['border'=>'border-amber-100','bg'=>'bg-gradient-to-br from-amber-50 to-orange-50','val'=>'text-amber-700'],
    ];
    $barGradients = ['from-blue-500 to-indigo-600','from-violet-500 to-purple-600','from-emerald-500 to-teal-600','from-amber-500 to-orange-500'];
@endphp

<div class="space-y-5">

    {{-- ── Header ── --}}
    <div class="section-box rounded-2xl p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-(--olive)">Data Akademik</p>
                <h2 class="display-font mt-1 text-4xl leading-tight">Statistik</h2>
            </div>
            {{-- Year selector --}}
            <div class="flex flex-wrap gap-2">
                @foreach (collect($daftarTahun)->take(8)->all() as $tahun)
                    <button type="button" wire:click="pilihTahun({{ $tahun }})"
                        class="rounded-full px-4 py-2 text-xs font-semibold transition
                               {{ $statAktif && $statAktif->year === $tahun
                                   ? 'bg-(--accent) text-white shadow-sm'
                                   : 'border border-(--line) bg-white hover:border-blue-300 hover:text-(--accent)' }}">
                        {{ $tahun }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- KPI cards --}}
        <div class="mt-5 grid gap-3 sm:grid-cols-2 md:grid-cols-4">
            @foreach ($statAktif?->kpi ?? [] as $ki => $item)
                @php $kc = $kpiColors[$ki] ?? $kpiColors[0]; @endphp
                <div class="rounded-xl border {{ $kc['border'] }} {{ $kc['bg'] }} p-4">
                        <p class="text-xs font-medium text-slate-500">{{ $ki === 3 ? 'Target Publikasi' : data_get($item,'label') }}</p>
                    <p class="mt-1.5 text-2xl font-extrabold {{ $kc['val'] }}">
                        {{ number_format((float) data_get($item,'value',0), (int) data_get($item,'decimals',0), ',', '.') }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Trend Charts ── --}}
    <div class="section-box rounded-2xl p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-bold text-slate-800">Tren Indikator Utama</p>
                <p class="mt-0.5 text-xs text-(--muted)">{{ $rangeLabel }}</p>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" wire:click="pilihTrendMode('year')"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold transition
                           {{ $trendMode === 'year' ? 'bg-(--accent) text-white shadow-sm' : 'border border-(--line) bg-white text-(--muted) hover:bg-slate-50' }}">
                    Per Tahun
                </button>
                <button type="button" wire:click="pilihTrendMode('all')"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold transition
                           {{ $trendMode === 'all' ? 'bg-(--accent) text-white shadow-sm' : 'border border-(--line) bg-white text-(--muted) hover:bg-slate-50' }}">
                    Semua Tahun
                </button>
                {{-- Spinner saat loading --}}
                <span wire:loading wire:target="pilihTrendMode,pilihTahun"
                    class="ml-1 inline-flex h-4 w-4 items-center justify-center">
                    <svg class="h-4 w-4 animate-spin text-(--accent)" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </span>
            </div>
        </div>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            {{-- Chart A: Mahasiswa & Dosen --}}
            <div class="rounded-xl border border-(--line) bg-white p-4 shadow-sm">
                <p class="mb-1 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                    Mahasiswa Aktif &amp; Dosen Tetap
                </p>
                <div
                    wire:key="stat-chart-a--{{ $trendMode }}--{{ $tahunDipilih }}"
                    x-data="prodiChartInit('stat-chart-a--{{ $trendMode }}--{{ $tahunDipilih }}', {{ json_encode($cjA['labels'], JSON_UNESCAPED_UNICODE) }}, {{ json_encode($cjA['datasets'], JSON_UNESCAPED_UNICODE) }}, true)"
                    x-init="boot()"
                    style="position:relative; height:220px; width:100%;">
                    <canvas id="stat-chart-a--{{ $trendMode }}--{{ $tahunDipilih }}" style="display:block; width:100%; height:100%;"></canvas>
                </div>
            </div>

            {{-- Chart B: IPK & Publikasi --}}
            <div class="rounded-xl border border-(--line) bg-white p-4 shadow-sm">
                <p class="mb-1 text-[11px] font-bold uppercase tracking-widest text-slate-400">
                    IPK Rata-rata &amp; Publikasi (kumulatif)
                </p>
                <div
                    wire:key="stat-chart-b--{{ $trendMode }}--{{ $tahunDipilih }}"
                    x-data="prodiChartInit('stat-chart-b--{{ $trendMode }}--{{ $tahunDipilih }}', {{ json_encode($cjB['labels'], JSON_UNESCAPED_UNICODE) }}, {{ json_encode($cjB['datasets'], JSON_UNESCAPED_UNICODE) }}, true)"
                    x-init="boot()"
                    style="position:relative; height:220px; width:100%;">
                    <canvas id="stat-chart-b--{{ $trendMode }}--{{ $tahunDipilih }}" style="display:block; width:100%; height:100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Bottom row: Capaian + YTD ── --}}
    <div class="grid gap-5 lg:grid-cols-2">

        {{-- Capaian persentase --}}
        <div class="section-box rounded-2xl p-6">
            <p class="text-sm font-bold text-slate-800">Capaian Indikator</p>
            <div class="mt-5 space-y-4">
                @foreach ($statAktif?->capaian ?? [] as $ci => $item)
                    @php $pct = max(0, min(100, (float) data_get($item,'percent',0))); @endphp
                    <div>
                        <div class="mb-1.5 flex justify-between text-xs font-semibold">
                            <span class="text-slate-700">{{ data_get($item,'label') }}</span>
                            <span class="text-slate-800">{{ $pct }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r {{ $barGradients[$ci] ?? $barGradients[0] }}"
                                style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Statistik Kinerja YTD --}}
        <div class="section-box rounded-2xl p-6">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-bold text-slate-800">Kinerja YTD</p>
                <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-[11px] font-semibold text-sky-700">
                    s.d. {{ data_get($kinerjaTahunanBerjalan,'monthLabel','-') }}
                    {{ data_get($kinerjaTahunanBerjalan,'year',$tahunDipilih) }}
                </span>
            </div>
            <div class="mt-4 space-y-4">
                @foreach (data_get($kinerjaTahunanBerjalan,'items',[]) as $item)
                    @php
                        $status   = data_get($item,'status','danger');
                        $progress = (float) data_get($item,'progress',0);
                        $decimals = (int) data_get($item,'decimals',0);
                        $isIpk    = (bool) data_get($item,'is_ipk', false);
                        $sc = match($status) {
                            'success' => ['grad'=>'from-emerald-500 to-teal-600','bg'=>'bg-emerald-50','text'=>'text-emerald-700'],
                            'warning' => ['grad'=>'from-amber-500 to-orange-500','bg'=>'bg-amber-50','text'=>'text-amber-700'],
                            default   => ['grad'=>'from-rose-500 to-pink-600','bg'=>'bg-rose-50','text'=>'text-rose-700'],
                        };
                    @endphp
                    <div class="rounded-xl border border-(--line) bg-white p-4">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ data_get($item,'label') }}</p>
                            <span class="rounded-full {{ $sc['bg'] }} {{ $sc['text'] }} px-2 py-0.5 text-[10px] font-bold flex-shrink-0">
                                @if ($isIpk)
                                    @if ($status === 'success') Unggul
                                    @elseif ($status === 'warning') Baik
                                    @else Perlu Perhatian
                                    @endif
                                @else
                                    {{ number_format($progress,1,',','.') }}%
                                @endif
                            </span>
                        </div>
                        <p class="mt-1.5 text-xl font-extrabold text-slate-800">
                            {{ number_format((float) data_get($item,'realisasi',0), $decimals, ',', '.') }}
                            <span class="text-xs font-medium text-slate-400">
                                / {{ number_format((float) data_get($item,'target',0), $decimals, ',', '.') }}
                                @if (!$isIpk)
                                    &nbsp;·&nbsp; Forecast {{ number_format((float) data_get($item,'forecast',0), $decimals, ',', '.') }}
                                @endif
                            </span>
                        </p>
                        <div class="mt-2.5 h-1.5 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r {{ $sc['grad'] }}"
                                style="width:{{ max(0,min(100,$progress)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Quick summary cards ── --}}
    <div class="section-box rounded-2xl p-6">
        <p class="mb-4 text-sm font-bold text-slate-800">Ringkasan — {{ $statAktif?->year }}</p>
        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5">
            @php
                $quickItems = [
                    ['label'=>'Mahasiswa Aktif',    'value'=>number_format((float)data_get($statAktif?->kpi,'0.value',0),0,',','.'),'color'=>'text-blue-700','bg'=>'bg-blue-50','border'=>'border-blue-100'],
                    ['label'=>'Lulusan & Tracer',   'value'=>number_format((float)data_get($statAktif?->capaian,'1.percent',0),0,',','.').'%','color'=>'text-violet-700','bg'=>'bg-violet-50','border'=>'border-violet-100'],
                    ['label'=>'Publikasi (Realisasi YTD)',   'value'=>number_format((float) data_get(collect($kinerjaTahunanBerjalan)->first(fn($row) => str_contains((string) data_get($row,'label'), 'Publikasi')), 'value', 0),0,',','.'),'color'=>'text-emerald-700','bg'=>'bg-emerald-50','border'=>'border-emerald-100'],
                    ['label'=>'Kegiatan Dosen&Mhs', 'value'=>number_format((float)data_get($statAktif?->capaian,'3.percent',0),0,',','.').'%','color'=>'text-amber-700','bg'=>'bg-amber-50','border'=>'border-amber-100'],
                    ['label'=>'Program Aktif',      'value'=>$programCount,'color'=>'text-rose-700','bg'=>'bg-rose-50','border'=>'border-rose-100'],
                ];
            @endphp
            @foreach ($quickItems as $qi)
                <div class="rounded-xl border {{ $qi['border'] }} {{ $qi['bg'] }} p-4">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">{{ $qi['label'] }}</p>
                    <p class="mt-2 text-xl font-extrabold {{ $qi['color'] }}">{{ $qi['value'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

</div>
