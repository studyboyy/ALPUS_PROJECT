<div class="grid gap-4 lg:grid-cols-2">

    {{-- ── Header + KPI Cards ── --}}
    <article class="section-box rounded-2xl p-6 lg:col-span-2">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="display-font text-4xl leading-tight">Data dan Statistik</h2>
            @php
                $tahunStatistikAwal = collect($daftarTahun)->take(6)->all();
                $tahunStatistikLanjutan = collect($daftarTahun)->slice(6)->all();
            @endphp
            <div class="space-y-2">
                <div class="flex flex-wrap gap-2">
                    @foreach ($tahunStatistikAwal as $tahun)
                        <button type="button" wire:click="pilihTahun({{ $tahun }})"
                            class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $statAktif && $statAktif->year === $tahun ? 'bg-(--accent) text-white shadow-sm' : 'border border-(--line) bg-white hover:bg-slate-50' }}">{{ $tahun }}</button>
                    @endforeach
                </div>
                @if (count($tahunStatistikLanjutan) > 0)
                    <details>
                        <summary class="cursor-pointer text-xs font-semibold text-(--muted)">Lihat semua tahun</summary>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($tahunStatistikLanjutan as $tahun)
                                <button type="button" wire:click="pilihTahun({{ $tahun }})"
                                    class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $statAktif && $statAktif->year === $tahun ? 'bg-(--accent) text-white' : 'border border-(--line) bg-white hover:bg-slate-50' }}">{{ $tahun }}</button>
                            @endforeach
                        </div>
                    </details>
                @endif
            </div>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 md:grid-cols-4">
            @php
                $kpiColors = [
                    ['border'=>'border-blue-100','bg'=>'bg-gradient-to-br from-blue-50 to-indigo-50','val'=>'text-blue-700','icon'=>'text-blue-400'],
                    ['border'=>'border-violet-100','bg'=>'bg-gradient-to-br from-violet-50 to-purple-50','val'=>'text-violet-700','icon'=>'text-violet-400'],
                    ['border'=>'border-emerald-100','bg'=>'bg-gradient-to-br from-emerald-50 to-teal-50','val'=>'text-emerald-700','icon'=>'text-emerald-400'],
                    ['border'=>'border-amber-100','bg'=>'bg-gradient-to-br from-amber-50 to-orange-50','val'=>'text-amber-700','icon'=>'text-amber-400'],
                ];
            @endphp
            @foreach ($statAktif?->kpi ?? [] as $ki => $item)
                @php $kc = $kpiColors[$ki] ?? $kpiColors[0]; @endphp
                <div class="rounded-xl border {{ $kc['border'] }} {{ $kc['bg'] }} p-4">
                    <p class="text-xs font-medium text-slate-500">{{ data_get($item, 'label') }}</p>
                    <p class="mt-1.5 text-2xl font-bold {{ $kc['val'] }}">
                        {{ number_format((float) data_get($item, 'value', 0), (int) data_get($item, 'decimals', 0), ',', '.') }}
                    </p>
                </div>
            @endforeach
        </div>
    </article>

    {{-- ── Tren Indikator Utama (2 chart terpisah + tooltip hover) ── --}}
    @php
        $tooltipJson = json_encode(data_get($trendVisual, 'tooltipData', []), JSON_UNESCAPED_UNICODE);
        $cA = data_get($trendVisual, 'chartA', []);
        $cB = data_get($trendVisual, 'chartB', []);
        $isMonthly = data_get($trendVisual, 'trendMode', 'year') === 'year';
    @endphp

    <article class="section-box rounded-2xl p-6 lg:col-span-2">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <p class="text-sm font-semibold">Tren Indikator Utama</p>
                <p class="mt-0.5 text-[11px] text-(--muted)">{{ data_get($trendVisual, 'rangeLabel', '') }}</p>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" wire:click="pilihTrendMode('year')"
                    class="rounded-full px-3 py-1.5 text-[11px] font-semibold transition {{ data_get($trendVisual, 'trendMode', 'year') === 'year' ? 'bg-(--accent) text-white shadow-sm' : 'border border-(--line) bg-white text-(--muted) hover:bg-slate-50' }}">Per Tahun</button>
                <button type="button" wire:click="pilihTrendMode('all')"
                    class="rounded-full px-3 py-1.5 text-[11px] font-semibold transition {{ data_get($trendVisual, 'trendMode', 'year') === 'all' ? 'bg-(--accent) text-white shadow-sm' : 'border border-(--line) bg-white text-(--muted) hover:bg-slate-50' }}">Semua Tahun</button>
            </div>
        </div>

        <div class="mt-4 grid gap-4 md:grid-cols-2">

            {{-- Chart A: Mahasiswa Aktif & Dosen Tetap --}}
            <div class="rounded-xl border border-(--line) bg-white p-4 shadow-sm"
                 x-data="trendChart('chartA-{{ $statAktif?->year }}', {{ $tooltipJson }})"
                 x-init="init()">
                <div class="mb-2 flex items-center justify-between">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Mahasiswa Aktif &amp; Dosen Tetap</p>
                    <div class="flex items-center gap-3 text-[10px] font-medium text-slate-500">
                        <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-blue-600"></span>Mahasiswa</span>
                        <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-teal-600"></span>Dosen</span>
                    </div>
                </div>
                <div class="relative">
                    <svg id="chartA-{{ $statAktif?->year }}" viewBox="0 0 344 148" class="h-40 w-full overflow-visible"
                         @mousemove="onMouseMove($event, 'A')" @mouseleave="onMouseLeave()">

                        {{-- Grid lines + Y labels --}}
                        @foreach (data_get($cA, 'yTicks', []) as $tick)
                            <line x1="34" y1="{{ data_get($tick, 'y') }}" x2="310"
                                y2="{{ data_get($tick, 'y') }}" stroke="#e2e8f0" stroke-width="1" />
                            <text x="30" y="{{ data_get($tick, 'y') + 3.5 }}" text-anchor="end"
                                font-size="8.5" fill="#94a3b8">{{ data_get($tick, 'label') }}</text>
                        @endforeach

                        {{-- Area fill Mahasiswa --}}
                        @php
                            $mPts = data_get($cA, 'mahasiswa', '');
                            $firstMX = $mPts ? (float) explode(',', explode(' ', trim($mPts))[0])[0] : 34;
                            $lastMX  = data_get($cA, 'lastX', 310);
                            $lastMY  = data_get($cA, 'mahasiswaLastY', 74);
                        @endphp
                        <polygon points="{{ $mPts }} {{ $lastMX }},128 {{ $firstMX }},128"
                            fill="#1d4ed8" fill-opacity="0.06" />

                        {{-- Area fill Dosen --}}
                        @php
                            $dPts = data_get($cA, 'dosen', '');
                            $firstDX = $dPts ? (float) explode(',', explode(' ', trim($dPts))[0])[0] : 34;
                            $lastDX  = data_get($cA, 'lastX', 310);
                            $lastDY  = data_get($cA, 'dosenLastY', 88);
                        @endphp
                        <polygon points="{{ $dPts }} {{ $lastDX }},128 {{ $firstDX }},128"
                            fill="#0f766e" fill-opacity="0.06" />

                        {{-- Mahasiswa line --}}
                        <polyline points="{{ $mPts }}" fill="none" stroke="#1d4ed8"
                            stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />
                        {{-- Dosen line --}}
                        <polyline points="{{ $dPts }}" fill="none" stroke="#0f766e"
                            stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />

                        {{-- Dots at each data point (skip last — covered by end dot) --}}
                        @php
                            $mPtArr = array_values(array_filter(preg_split('/\s+/', trim($mPts))));
                            $dPtArr = array_values(array_filter(preg_split('/\s+/', trim($dPts))));
                            $mPtMid = array_slice($mPtArr, 0, max(0, count($mPtArr) - 1));
                            $dPtMid = array_slice($dPtArr, 0, max(0, count($dPtArr) - 1));
                        @endphp
                        @foreach ($mPtMid as $pt)
                            @php [$px,$py] = explode(',', $pt); @endphp
                            <circle cx="{{ $px }}" cy="{{ $py }}" r="2.5" fill="#1d4ed8" />
                        @endforeach
                        @foreach ($dPtMid as $pt)
                            @php [$px,$py] = explode(',', $pt); @endphp
                            <circle cx="{{ $px }}" cy="{{ $py }}" r="2.5" fill="#0f766e" />
                        @endforeach

                        {{-- End dots (larger) --}}
                        <circle cx="{{ $lastMX }}" cy="{{ $lastMY }}" r="4" fill="#1d4ed8" stroke="white" stroke-width="1.5" />
                        <circle cx="{{ $lastDX }}" cy="{{ $lastDY }}" r="4" fill="#0f766e" stroke="white" stroke-width="1.5" />

                        {{-- Hover crosshair --}}
                        <line x-show="hoverIdx !== null" :x1="hoverX" y1="20" :x2="hoverX" y2="128"
                            stroke="#94a3b8" stroke-width="1" stroke-dasharray="3 2" />

                        {{-- X-axis labels --}}
                        @foreach (data_get($cA, 'xTicks', []) as $tick)
                            <text x="{{ data_get($tick, 'x') }}" y="143" text-anchor="middle"
                                font-size="8.5" fill="#94a3b8">{{ data_get($tick, 'label') }}</text>
                        @endforeach
                    </svg>

                    {{-- Tooltip --}}
                    <div x-show="hoverIdx !== null" x-cloak
                         :style="'left:' + tooltipLeft + 'px; top:' + tooltipTop + 'px'"
                         class="pointer-events-none absolute z-20 min-w-[130px] rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-lg text-[11px] leading-5">
                        <p class="mb-1 font-semibold text-slate-700" x-text="hoverLabel"></p>
                        <p class="text-blue-700">Mahasiswa: <span class="font-semibold" x-text="hoverMahasiswa"></span></p>
                        <p class="text-teal-700">Dosen: <span class="font-semibold" x-text="hoverDosen"></span></p>
                    </div>
                </div>
            </div>

            {{-- Chart B: IPK & Publikasi --}}
            <div class="rounded-xl border border-(--line) bg-white p-4 shadow-sm"
                 x-data="trendChart('chartB-{{ $statAktif?->year }}', {{ $tooltipJson }})"
                 x-init="init()">
                <div class="mb-2 flex items-center justify-between">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">IPK Rata-rata &amp; Publikasi</p>
                    <div class="flex items-center gap-3 text-[10px] font-medium text-slate-500">
                        <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-teal-600"></span>IPK <span class="text-slate-400">(kiri)</span></span>
                        <span class="flex items-center gap-1"><span class="inline-block h-2 w-2 rounded-full bg-orange-500"></span>Pub <span class="text-slate-400">(kanan)</span></span>
                    </div>
                </div>
                <div class="relative">
                    <svg id="chartB-{{ $statAktif?->year }}" viewBox="0 0 360 148" class="h-40 w-full overflow-visible"
                         @mousemove="onMouseMove($event, 'B')" @mouseleave="onMouseLeave()">

                        {{-- IPK grid + left Y labels --}}
                        @foreach (data_get($cB, 'yTicksIpk', []) as $tick)
                            <line x1="44" y1="{{ data_get($tick, 'y') }}" x2="316"
                                y2="{{ data_get($tick, 'y') }}" stroke="#e2e8f0" stroke-width="1" />
                            <text x="40" y="{{ data_get($tick, 'y') + 3.5 }}" text-anchor="end"
                                font-size="8.5" fill="#0f766e">{{ data_get($tick, 'label') }}</text>
                        @endforeach

                        {{-- Publikasi right Y labels --}}
                        @foreach (data_get($cB, 'yTicksPub', []) as $tick)
                            <text x="320" y="{{ data_get($tick, 'y') + 3.5 }}" text-anchor="start"
                                font-size="8.5" fill="#ea580c">{{ data_get($tick, 'label') }}</text>
                        @endforeach

                        {{-- Area fills --}}
                        @php
                            $iPts = data_get($cB, 'ipk', '');
                            $pPts = data_get($cB, 'publikasi', '');
                            $firstIX = $iPts ? (float) explode(',', explode(' ', trim($iPts))[0])[0] : 44;
                            $firstPX = $pPts ? (float) explode(',', explode(' ', trim($pPts))[0])[0] : 44;
                            $lastBX  = data_get($cB, 'lastX', 316);
                        @endphp
                        <polygon points="{{ $iPts }} {{ $lastBX }},128 {{ $firstIX }},128"
                            fill="#0f766e" fill-opacity="0.06" />
                        <polygon points="{{ $pPts }} {{ $lastBX }},128 {{ $firstPX }},128"
                            fill="#ea580c" fill-opacity="0.06" />

                        {{-- IPK line --}}
                        <polyline points="{{ $iPts }}" fill="none" stroke="#0f766e"
                            stroke-width="2" stroke-linejoin="round" stroke-linecap="round" />
                        {{-- Publikasi line dashed --}}
                        <polyline points="{{ $pPts }}" fill="none" stroke="#ea580c"
                            stroke-width="2" stroke-dasharray="5 3" stroke-linejoin="round" stroke-linecap="round" />

                        {{-- Dots at each point (skip last — covered by end dot) --}}
                        @php
                            $iPtArr = array_values(array_filter(preg_split('/\s+/', trim($iPts))));
                            $pPtArr = array_values(array_filter(preg_split('/\s+/', trim($pPts))));
                            $iPtMid = array_slice($iPtArr, 0, max(0, count($iPtArr) - 1));
                            $pPtMid = array_slice($pPtArr, 0, max(0, count($pPtArr) - 1));
                        @endphp
                        @foreach ($iPtMid as $pt)
                            @php [$px,$py] = explode(',', $pt); @endphp
                            <circle cx="{{ $px }}" cy="{{ $py }}" r="2.5" fill="#0f766e" />
                        @endforeach
                        @foreach ($pPtMid as $pt)
                            @php [$px,$py] = explode(',', $pt); @endphp
                            <circle cx="{{ $px }}" cy="{{ $py }}" r="2.5" fill="#ea580c" />
                        @endforeach

                        {{-- End dots (larger) --}}
                        <circle cx="{{ $lastBX }}" cy="{{ data_get($cB,'ipkLastY',74) }}" r="4" fill="#0f766e" stroke="white" stroke-width="1.5" />
                        <circle cx="{{ $lastBX }}" cy="{{ data_get($cB,'publikasiLastY',88) }}" r="4" fill="#ea580c" stroke="white" stroke-width="1.5" />

                        {{-- Hover crosshair --}}
                        <line x-show="hoverIdx !== null" :x1="hoverX" y1="20" :x2="hoverX" y2="128"
                            stroke="#94a3b8" stroke-width="1" stroke-dasharray="3 2" />

                        {{-- X-axis labels --}}
                        @foreach (data_get($cB, 'xTicks', []) as $tick)
                            <text x="{{ data_get($tick, 'x') }}" y="143" text-anchor="middle"
                                font-size="8.5" fill="#94a3b8">{{ data_get($tick, 'label') }}</text>
                        @endforeach
                    </svg>

                    {{-- Tooltip --}}
                    <div x-show="hoverIdx !== null" x-cloak
                         :style="'left:' + tooltipLeft + 'px; top:' + tooltipTop + 'px'"
                         class="pointer-events-none absolute z-20 min-w-[130px] rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-lg text-[11px] leading-5">
                        <p class="mb-1 font-semibold text-slate-700" x-text="hoverLabel"></p>
                        <p class="text-teal-700">IPK: <span class="font-semibold" x-text="hoverIpk"></span></p>
                        <p class="text-orange-600">Publikasi: <span class="font-semibold" x-text="hoverPublikasi"></span></p>
                    </div>
                </div>
            </div>

        </div>
    </article>

    {{-- ── Capaian Persentase ── --}}
    <article class="section-box rounded-2xl p-6">
        <p class="text-sm font-semibold">Capaian Persentase</p>
        <div class="mt-5 space-y-4 text-sm">
            @foreach ($statAktif?->capaian ?? [] as $ci => $item)
                @php
                    $barColors = ['from-blue-500 to-indigo-600','from-violet-500 to-purple-600','from-emerald-500 to-teal-600','from-amber-500 to-orange-600'];
                    $bc = $barColors[$ci] ?? $barColors[0];
                    $pct = max(0, min(100, (float) data_get($item,'percent',0)));
                @endphp
                <div>
                    <div class="mb-1.5 flex justify-between font-medium">
                        <span class="text-slate-700">{{ data_get($item,'label') }}</span>
                        <span class="font-bold text-slate-800">{{ number_format($pct, 0, ',', '.') }}%</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                        <span class="block h-full rounded-full bg-gradient-to-r {{ $bc }}"
                              style="width:{{ $pct }}%"></span>
                    </div>
                </div>
            @endforeach
        </div>
    </article>

    {{-- ── Statistik Kinerja YTD ── --}}
    <article class="section-box rounded-2xl p-6 lg:col-span-2">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm font-semibold">Statistik Kinerja Tahunan Berjalan</p>
            <span class="rounded-full bg-sky-50 px-3 py-1 text-[11px] font-semibold text-sky-700">
                YTD s.d. {{ data_get($kinerjaTahunanBerjalan,'monthLabel','-') }} {{ data_get($kinerjaTahunanBerjalan,'year',$tahunDipilih) }}
            </span>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach (data_get($kinerjaTahunanBerjalan,'items',[]) as $item)
                @php
                    $status = data_get($item,'status','danger');
                    $sc = match($status) {
                        'success' => ['grad'=>'from-emerald-500 to-teal-600','bg'=>'bg-emerald-50','text'=>'text-emerald-700'],
                        'warning' => ['grad'=>'from-amber-500 to-orange-600','bg'=>'bg-amber-50','text'=>'text-amber-700'],
                        default   => ['grad'=>'from-rose-500 to-pink-600','bg'=>'bg-rose-50','text'=>'text-rose-700'],
                    };
                    $progress = (float) data_get($item,'progress',0);
                    $decimals = (int) data_get($item,'decimals',0);
                @endphp
                <div class="rounded-xl border border-(--line) bg-white p-4 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400">{{ data_get($item,'label') }}</p>
                    <p class="mt-2 text-xl font-bold text-slate-800">
                        {{ number_format((float) data_get($item,'realisasi',0), $decimals, ',', '.') }}
                        <span class="text-xs font-medium text-slate-400">/ {{ number_format((float) data_get($item,'target',0), $decimals, ',', '.') }}</span>
                    </p>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-100">
                        <span class="block h-full rounded-full bg-gradient-to-r {{ $sc['grad'] }}"
                              style="width:{{ max(0,min(100,$progress)) }}%"></span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="inline-flex items-center rounded-full {{ $sc['bg'] }} {{ $sc['text'] }} px-2 py-0.5 text-[10px] font-semibold">
                            {{ number_format($progress,1,',','.') }}%
                        </span>
                        <span class="text-[10px] text-slate-400">
                            Forecast {{ number_format((float) data_get($item,'forecast',0), $decimals, ',', '.') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
        @if (!empty(data_get($kinerjaTahunanBerjalan,'updatedAt')))
            <p class="mt-3 text-[11px] text-(--muted)">Pembaruan terakhir: {{ data_get($kinerjaTahunanBerjalan,'updatedAt') }}</p>
        @endif
    </article>

    {{-- ── Ringkasan Statistik Cepat ── --}}
    <article class="section-box rounded-2xl p-6 lg:col-span-2">
        <p class="mb-4 text-sm font-semibold">Ringkasan Statistik Utama {{ $statAktif?->year }}</p>
        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5">
            @php
                $quickItems = [
                    ['label'=>'Mahasiswa Aktif','value'=> number_format((float) data_get($statAktif?->kpi,'0.value',0),0,',','.'),'color'=>'text-blue-700','bg'=>'bg-blue-50','border'=>'border-blue-100'],
                    ['label'=>'Lulusan & Tracer Study','value'=> number_format((float) data_get($statAktif?->capaian,'1.percent',0),0,',','.').'%','color'=>'text-violet-700','bg'=>'bg-violet-50','border'=>'border-violet-100'],
                    ['label'=>'Publikasi Ilmiah','value'=> number_format((float) data_get($statAktif?->kpi,'3.value',0),0,',','.'),'color'=>'text-emerald-700','bg'=>'bg-emerald-50','border'=>'border-emerald-100'],
                    ['label'=>'Kegiatan Dosen & Mhs','value'=> number_format((float) data_get($statAktif?->capaian,'3.percent',0),0,',','.').'%','color'=>'text-amber-700','bg'=>'bg-amber-50','border'=>'border-amber-100'],
                    ['label'=>'Program Aktif','value'=> $programCount,'color'=>'text-rose-700','bg'=>'bg-rose-50','border'=>'border-rose-100'],
                ];
            @endphp
            @foreach ($quickItems as $qi)
                <div class="rounded-xl border {{ $qi['border'] }} {{ $qi['bg'] }} p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">{{ $qi['label'] }}</p>
                    <p class="mt-2 text-xl font-bold {{ $qi['color'] }}">{{ $qi['value'] }}</p>
                </div>
            @endforeach
        </div>
    </article>

</div>

{{-- ── Alpine.js tooltip component ── --}}
<script>
function trendChart(svgId, tooltipData) {
    return {
        hoverIdx: null,
        hoverX: 0,
        hoverLabel: '',
        hoverMahasiswa: '',
        hoverDosen: '',
        hoverIpk: '',
        hoverPublikasi: '',
        tooltipLeft: 0,
        tooltipTop: 0,
        points: [],

        init() {
            // Pre-compute X positions from the SVG polyline points
            const svg = document.getElementById(svgId);
            if (!svg) return;
            const poly = svg.querySelector('polyline');
            if (!poly || !tooltipData || tooltipData.length === 0) return;
            const raw = poly.getAttribute('points').trim().split(/\s+/);
            this.points = raw.map(p => parseFloat(p.split(',')[0]));
        },

        onMouseMove(event, chartType) {
            if (!tooltipData || tooltipData.length === 0 || this.points.length === 0) return;

            const svg = document.getElementById(svgId);
            if (!svg) return;
            const rect = svg.getBoundingClientRect();
            const vbWidth = 344; // viewBox width for chartA; 360 for B but ratio same
            const scaleX = rect.width / (chartType === 'B' ? 360 : 344);
            const mouseX = (event.clientX - rect.left) / scaleX;

            // Find closest point
            let closest = 0;
            let minDist = Infinity;
            this.points.forEach((px, i) => {
                const d = Math.abs(px - mouseX);
                if (d < minDist) { minDist = d; closest = i; }
            });

            if (minDist > 18) { this.hoverIdx = null; return; }

            this.hoverIdx = closest;
            this.hoverX = this.points[closest];
            const d = tooltipData[closest] || {};
            this.hoverLabel = d.label || '';
            this.hoverMahasiswa = typeof d.mahasiswa === 'number' ? d.mahasiswa.toLocaleString('id-ID') : d.mahasiswa;
            this.hoverDosen = typeof d.dosen === 'number' ? d.dosen.toLocaleString('id-ID') : d.dosen;
            this.hoverIpk = typeof d.ipk === 'number' ? d.ipk.toFixed(2).replace('.', ',') : d.ipk;
            this.hoverPublikasi = typeof d.publikasi === 'number' ? d.publikasi.toLocaleString('id-ID') : d.publikasi;

            // Position tooltip relative to chart container
            const container = svg.parentElement;
            const cRect = container.getBoundingClientRect();
            const svgLeft = (this.hoverX * scaleX);
            this.tooltipLeft = Math.max(4, Math.min(svgLeft - 65, cRect.width - 150));
            this.tooltipTop = 4;
        },

        onMouseLeave() {
            this.hoverIdx = null;
        }
    };
}
</script>
