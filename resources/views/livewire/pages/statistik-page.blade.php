<div class="grid gap-4 lg:grid-cols-2">
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
                            class="rounded-full px-4 py-2 text-xs font-semibold {{ $statAktif && $statAktif->year === $tahun ? 'bg-(--accent) text-white' : 'border border-(--line) bg-white' }}">{{ $tahun }}</button>
                    @endforeach
                </div>
                @if (count($tahunStatistikLanjutan) > 0)
                    <details>
                        <summary class="cursor-pointer text-xs font-semibold text-(--muted)">Lihat semua tahun</summary>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($tahunStatistikLanjutan as $tahun)
                                <button type="button" wire:click="pilihTahun({{ $tahun }})"
                                    class="rounded-full px-4 py-2 text-xs font-semibold {{ $statAktif && $statAktif->year === $tahun ? 'bg-(--accent) text-white' : 'border border-(--line) bg-white' }}">{{ $tahun }}</button>
                            @endforeach
                        </div>
                    </details>
                @endif
            </div>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-4">
            @foreach ($statAktif?->kpi ?? [] as $item)
                <div class="rounded-xl border border-(--line) bg-slate-50 p-4">
                    <p class="text-xs text-(--muted)">{{ data_get($item, 'label') }}</p>
                    <p class="mt-1 text-2xl font-bold">
                        {{ number_format((float) data_get($item, 'value', 0), (int) data_get($item, 'decimals', 0), ',', '.') }}
                    </p>
                </div>
            @endforeach
        </div>
    </article>

    <article class="section-box rounded-2xl p-6">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm font-semibold">Tren Indikator Utama</p>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="pilihTrendMode('year')"
                    class="rounded-full px-3 py-1 text-[11px] font-semibold transition {{ data_get($trendVisual, 'trendMode', 'year') === 'year' ? 'bg-(--accent) text-white' : 'border border-(--line) bg-white text-(--muted)' }}">Per
                    Tahun</button>
                <button type="button" wire:click="pilihTrendMode('all')"
                    class="rounded-full px-3 py-1 text-[11px] font-semibold transition {{ data_get($trendVisual, 'trendMode', 'year') === 'all' ? 'bg-(--accent) text-white' : 'border border-(--line) bg-white text-(--muted)' }}">Semua
                    Tahun</button>
            </div>
        </div>
        <div class="mt-4 rounded-xl border border-(--line) bg-slate-50 p-4">
            <p class="mb-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-(--muted)">
                {{ data_get($trendVisual, 'rangeLabel', 'Ringkasan data') }}</p>
            <svg viewBox="0 0 340 170" class="h-44 w-full">
                @foreach (data_get($trendVisual, 'yTicks', []) as $tick)
                    <line x1="34" y1="{{ data_get($tick, 'y') }}" x2="310"
                        y2="{{ data_get($tick, 'y') }}" stroke="#dbe5f2" stroke-width="1" />
                    <text x="28" y="{{ data_get($tick, 'y') + 3 }}" text-anchor="end" font-size="9"
                        fill="#64748b">{{ data_get($tick, 'label') }}</text>
                @endforeach

                <polyline points="{{ data_get($trendVisual, 'mahasiswa', '') }}" fill="none" stroke="#1d4ed8"
                    stroke-width="4" />
                <polyline points="{{ data_get($trendVisual, 'ipk', '') }}" fill="none" stroke="#0f766e"
                    stroke-width="4" />
                <polyline points="{{ data_get($trendVisual, 'publikasi', '') }}" fill="none" stroke="#ea580c"
                    stroke-width="3" stroke-dasharray="6 5" />
                <polyline points="{{ data_get($trendVisual, 'dosen', '') }}" fill="none" stroke="#fb923c"
                    stroke-width="3" />
                <polyline points="{{ data_get($trendVisual, 'progressYtd', '') }}" fill="none" stroke="#7c3aed"
                    stroke-width="3" stroke-dasharray="3 3" />
                <circle cx="{{ data_get($trendVisual, 'lastX', 310) }}"
                    cy="{{ data_get($trendVisual, 'mahasiswaLastY', 90) }}" r="5" fill="#1d4ed8" />
                <circle cx="{{ data_get($trendVisual, 'lastX', 310) }}"
                    cy="{{ data_get($trendVisual, 'ipkLastY', 96) }}" r="5" fill="#0f766e" />
                <circle cx="{{ data_get($trendVisual, 'lastX', 310) }}"
                    cy="{{ data_get($trendVisual, 'publikasiLastY', 102) }}" r="4" fill="#ea580c" />
                <circle cx="{{ data_get($trendVisual, 'lastX', 310) }}"
                    cy="{{ data_get($trendVisual, 'dosenLastY', 108) }}" r="4" fill="#fb923c" />
                <circle cx="{{ data_get($trendVisual, 'lastX', 310) }}"
                    cy="{{ data_get($trendVisual, 'progressLastY', 96) }}" r="4" fill="#7c3aed" />

                @foreach (data_get($trendVisual, 'yearTicks', []) as $tick)
                    <text x="{{ data_get($tick, 'x') }}" y="147" text-anchor="middle" font-size="9"
                        fill="#64748b">{{ data_get($tick, 'year') }}</text>
                @endforeach
            </svg>
            <div class="mt-2 flex flex-wrap gap-4 text-xs text-(--muted)">
                <span>Garis biru: Mahasiswa</span>
                <span>Garis hijau: IPK</span>
                <span>Garis oranye putus: Publikasi</span>
                <span>Garis oranye terang: Dosen Tetap</span>
                <span>Garis ungu putus: Progres Kinerja YTD (%)</span>
            </div>
        </div>
    </article>

    <article class="section-box rounded-2xl p-6">
        <p class="text-sm font-semibold">Capaian Persentase</p>
        <div class="mt-6 space-y-4 text-sm">
            @foreach ($statAktif?->capaian ?? [] as $item)
                <div>
                    <div class="mb-1 flex justify-between">
                        <span>{{ data_get($item, 'label') }}</span><span>{{ number_format((float) data_get($item, 'percent', 0), 0, ',', '.') }}%</span>
                    </div>
                    <div class="h-2 overflow-hidden rounded-full bg-[#eadfce]"><span
                            class="block h-full rounded-full bg-[linear-gradient(90deg,var(--accent),#ef8d44)]"
                            style="width: {{ max(0, min(100, (float) data_get($item, 'percent', 0))) }}%"></span>
                    </div>
                </div>
            @endforeach
        </div>
    </article>

    <article class="section-box rounded-2xl p-6 lg:col-span-2">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm font-semibold">Statistik Kinerja Tahunan Berjalan (Dinamis)</p>
            <span class="rounded-full bg-sky-50 px-3 py-1 text-[11px] font-semibold text-sky-700">YTD s.d.
                {{ data_get($kinerjaTahunanBerjalan, 'monthLabel', '-') }}
                {{ data_get($kinerjaTahunanBerjalan, 'year', $tahunDipilih) }}</span>
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            @foreach (data_get($kinerjaTahunanBerjalan, 'items', []) as $item)
                @php
                    $status = data_get($item, 'status', 'danger');
                    $statusClass = match ($status) {
                        'success' => 'from-emerald-500 to-teal-600',
                        'warning' => 'from-amber-500 to-orange-600',
                        default => 'from-rose-500 to-pink-600',
                    };
                    $progress = (float) data_get($item, 'progress', 0);
                    $decimals = (int) data_get($item, 'decimals', 0);
                @endphp
                <div class="rounded-xl border border-(--line) bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">
                        {{ data_get($item, 'label') }}</p>
                    <p class="mt-2 text-lg font-bold text-slate-800">
                        {{ number_format((float) data_get($item, 'realisasi', 0), $decimals, ',', '.') }}
                        <span class="text-xs font-medium text-slate-500">/
                            {{ number_format((float) data_get($item, 'target', 0), $decimals, ',', '.') }}</span>
                    </p>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                        <span class="block h-full rounded-full bg-linear-to-r {{ $statusClass }}"
                            style="width: {{ max(0, min(100, $progress)) }}%"></span>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-[11px] text-(--muted)">
                        <span>Progres {{ number_format($progress, 1, ',', '.') }}%</span>
                        <span>Forecast
                            {{ number_format((float) data_get($item, 'forecast', 0), $decimals, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        @if (!empty(data_get($kinerjaTahunanBerjalan, 'updatedAt')))
            <p class="mt-3 text-[11px] text-(--muted)">Pembaruan terakhir:
                {{ data_get($kinerjaTahunanBerjalan, 'updatedAt') }}</p>
        @endif
    </article>

    <article class="section-box rounded-2xl p-6 lg:col-span-2">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-xl border border-(--line) bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Jumlah Mahasiswa Aktif</p>
                <p class="mt-2 text-xl font-bold text-slate-800">
                    {{ number_format((float) data_get($statAktif?->kpi, '0.value', 0), 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-(--line) bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Lulusan & Tracer Study</p>
                <p class="mt-2 text-xl font-bold text-slate-800">
                    {{ number_format((float) data_get($statAktif?->capaian, '1.percent', 0), 0, ',', '.') }}%</p>
            </div>
            <div class="rounded-xl border border-(--line) bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Publikasi Ilmiah</p>
                <p class="mt-2 text-xl font-bold text-slate-800">
                    {{ number_format((float) data_get($statAktif?->kpi, '3.value', 0), 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl border border-(--line) bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Kegiatan Dosen & Mahasiswa
                </p>
                <p class="mt-2 text-xl font-bold text-slate-800">
                    {{ number_format((float) data_get($statAktif?->capaian, '3.percent', 0), 0, ',', '.') }}%</p>
            </div>
            <div class="rounded-xl border border-(--line) bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Program Aktif</p>
                <p class="mt-2 text-xl font-bold text-slate-800">{{ $programCount }}</p>
            </div>
        </div>
    </article>
</div>
