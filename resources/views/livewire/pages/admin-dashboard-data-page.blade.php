<div class="space-y-5">

    {{-- ── Page header + year selector ── --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-zinc-800">Data &amp; Statistik</h2>
            <p class="mt-0.5 text-sm text-zinc-500">Kelola KPI tahunan dan rincian data bulanan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @foreach ($daftarTahun as $tahun)
                <div class="flex items-center overflow-hidden rounded-full {{ $tahunDipilih === $tahun ? 'bg-indigo-600 ring-2 ring-indigo-300' : 'border border-zinc-300 bg-white' }}">
                    <button type="button" wire:click="pilihTahun({{ $tahun }})"
                        class="px-3.5 py-1.5 text-xs font-bold transition {{ $tahunDipilih === $tahun ? 'text-white' : 'text-zinc-600 hover:text-indigo-700' }}">
                        {{ $tahun }}
                    </button>
                    <button type="button" wire:click="hapusTahun({{ $tahun }})"
                        class="border-l px-2.5 py-1.5 text-xs font-bold transition {{ $tahunDipilih === $tahun ? 'border-indigo-400 text-white/70 hover:text-white' : 'border-zinc-200 text-zinc-400 hover:text-rose-600' }}"
                        title="Hapus tahun {{ $tahun }}">×</button>
                </div>
            @endforeach
            <form wire:submit="tambahTahun" class="flex items-center gap-1.5">
                <input wire:model.defer="tahunBaru" type="number" min="2000" max="2100"
                    placeholder="Tahun baru"
                    class="w-28 rounded-full border border-zinc-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-zinc-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                <button type="submit"
                    class="rounded-full bg-zinc-800 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-zinc-900">
                    + Tambah
                </button>
            </form>
        </div>
    </div>

    {{-- ── KPI summary cards ── --}}
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $summaryCards = [
                ['label'=>'Mahasiswa Aktif','val'=>number_format((float)$statistik['mahasiswa_aktif'],0,',','.'),'color'=>'border-t-blue-500 text-blue-700','note'=>'Tahun '.$tahunDipilih],
                ['label'=>'IPK Rata-rata',  'val'=>number_format((float)$statistik['ipk'],2,',','.'),           'color'=>'border-t-violet-500 text-violet-700','note'=>'Mutu akademik'],
                ['label'=>'Dosen Tetap',    'val'=>number_format((float)$statistik['dosen_tetap'],0,',','.'),   'color'=>'border-t-emerald-500 text-emerald-700','note'=>'SDM pengajar'],
                ['label'=>'Publikasi',      'val'=>number_format((float)$statistik['publikasi'],0,',','.'),     'color'=>'border-t-amber-500 text-amber-700','note'=>'Output riset'],
            ];
        @endphp
        @foreach ($summaryCards as $c)
            @php [$borderColor, $textColor] = explode(' ', $c['color']); @endphp
            <div class="section-box rounded-2xl border-t-4 {{ $borderColor }} p-4">
                <p class="text-[11px] font-bold uppercase tracking-wide text-zinc-400">{{ $c['label'] }}</p>
                <p class="mt-2 text-2xl font-extrabold {{ $textColor }}">{{ $c['val'] }}</p>
                <p class="mt-0.5 text-[11px] text-zinc-400">{{ $c['note'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ── Tab nav (Livewire-driven, survives re-render) ── --}}
    <div class="section-box flex gap-1 rounded-2xl p-2">
        <button type="button" wire:click="switchTab('tahunan')"
            class="flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition-all duration-150
                   {{ $activeTab === 'tahunan' ? 'bg-indigo-600 text-white shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-800' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Data Tahunan
        </button>
        <button type="button" wire:click="switchTab('bulanan')"
            class="flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition-all duration-150
                   {{ $activeTab === 'bulanan' ? 'bg-indigo-600 text-white shadow-sm' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-800' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Rincian Bulanan
        </button>
    </div>

    {{-- ══════════════════════════════════
         TAB: DATA TAHUNAN
    ══════════════════════════════════ --}}
    @if ($activeTab === 'tahunan')
        <div class="section-box rounded-2xl p-6">
            <div class="mb-5 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-zinc-800">KPI &amp; Capaian — Tahun {{ $tahunDipilih }}</h3>
                    <p class="mt-0.5 text-xs text-zinc-500">Nilai ditampilkan di halaman statistik dan laporan portal publik.</p>
                </div>
                <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                    {{ $tahunDipilih }}
                </span>
            </div>

            <form wire:submit="simpanStatistik" class="space-y-6">

                {{-- KPI utama --}}
                <div>
                    <p class="mb-3 text-[11px] font-bold uppercase tracking-widest text-zinc-400">Indikator Kinerja Utama</p>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @php
                            $kpiFields = [
                                ['key'=>'mahasiswa_aktif','label'=>'Mahasiswa Aktif','step'=>'1','min'=>'0','max'=>null],
                                ['key'=>'ipk',           'label'=>'IPK Rata-rata',  'step'=>'0.01','min'=>'0','max'=>'4'],
                                ['key'=>'dosen_tetap',   'label'=>'Dosen Tetap',    'step'=>'1','min'=>'0','max'=>null],
                                ['key'=>'publikasi',     'label'=>'Publikasi',      'step'=>'1','min'=>'0','max'=>null],
                            ];
                        @endphp
                        @foreach ($kpiFields as $f)
                            <div>
                                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">{{ $f['label'] }}</label>
                                <input wire:model.defer="statistik.{{ $f['key'] }}"
                                    type="number" step="{{ $f['step'] }}" min="{{ $f['min'] }}"
                                    @if($f['max']) max="{{ $f['max'] }}" @endif
                                    class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Capaian --}}
                <div>
                    <p class="mb-3 text-[11px] font-bold uppercase tracking-widest text-zinc-400">Capaian (%)</p>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @php
                            $capaianFields = [
                                ['key'=>'capaian_mahasiswa','label'=>'Mahasiswa Aktif'],
                                ['key'=>'capaian_lulusan',  'label'=>'Lulusan Tepat Waktu'],
                                ['key'=>'capaian_publikasi','label'=>'Publikasi Ilmiah'],
                                ['key'=>'capaian_kegiatan', 'label'=>'Kegiatan Dosen & Mhs'],
                            ];
                        @endphp
                        @foreach ($capaianFields as $f)
                            <div>
                                <label class="block text-xs font-semibold text-zinc-600 mb-1.5">{{ $f['label'] }}</label>
                                <div class="relative">
                                    <input wire:model.defer="statistik.{{ $f['key'] }}"
                                        type="number" step="1" min="0" max="100"
                                        class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 pr-8 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-zinc-400">%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end border-t border-zinc-100 pt-4">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Data Tahunan
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- ══════════════════════════════════
         TAB: RINCIAN BULANAN
    ══════════════════════════════════ --}}
    @if ($activeTab === 'bulanan')
        <div class="section-box rounded-2xl p-6">
            <div class="mb-5 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-zinc-800">Rincian Bulanan — Tahun {{ $tahunDipilih }}</h3>
                    <p class="mt-0.5 text-xs text-zinc-500">Data ini digunakan untuk grafik tren bulanan dan YTD di portal. Nilai minimum 1.</p>
                </div>
                <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                    Bulan aktif sistem: {{ $bulanSekarang }}
                </span>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Tabel input bulanan --}}
                <div class="lg:col-span-2">
                    <form wire:submit="simpanBulanan">
                        <div class="overflow-x-auto rounded-xl border border-zinc-200 shadow-sm">
                            <table class="w-full text-sm">
                                <thead class="bg-zinc-50 text-xs font-semibold uppercase tracking-wide text-zinc-400">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Bulan</th>
                                        <th class="px-4 py-3 text-right">Mahasiswa</th>
                                        <th class="px-4 py-3 text-right">IPK</th>
                                        <th class="px-4 py-3 text-right">Dosen</th>
                                        <th class="px-4 py-3 text-right">Publikasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100">
                                    @foreach ($bulanan as $index => $row)
                                        <tr class="transition hover:bg-zinc-50 {{ $row['month'] == $bulanSekarang ? 'bg-indigo-50/40' : '' }}">
                                            <td class="px-4 py-2.5">
                                                <span class="font-semibold text-zinc-700">{{ $row['month_label'] }}</span>
                                                @if ($row['month'] == $bulanSekarang)
                                                    <span class="ml-1.5 rounded-full bg-indigo-100 px-1.5 py-0.5 text-[10px] font-bold text-indigo-600">Aktif</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <input type="number" step="1" min="1"
                                                    wire:model.defer="bulanan.{{ $index }}.mahasiswa_aktif"
                                                    class="w-24 rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-right text-sm outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-100"/>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <input type="number" step="0.01" min="0" max="4"
                                                    wire:model.defer="bulanan.{{ $index }}.ipk"
                                                    class="w-20 rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-right text-sm outline-none focus:border-teal-400 focus:ring-1 focus:ring-teal-100"/>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <input type="number" step="1" min="1"
                                                    wire:model.defer="bulanan.{{ $index }}.dosen_tetap"
                                                    class="w-20 rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-right text-sm outline-none focus:border-violet-400 focus:ring-1 focus:ring-violet-100"/>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <input type="number" step="1" min="1"
                                                    wire:model.defer="bulanan.{{ $index }}.publikasi"
                                                    class="w-20 rounded-lg border border-zinc-200 bg-white px-2 py-1.5 text-right text-sm outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-100"/>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex items-center gap-3">
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Data Bulanan
                            </button>
                            <p class="text-xs text-zinc-400">Perubahan langsung mempengaruhi grafik dan YTD di portal.</p>
                        </div>
                    </form>
                </div>

                {{-- Mini charts preview --}}
                <div class="space-y-3">
                    @php
                        $previewCharts = [
                            ['vals' => array_map(fn($r) => max(0, (float)($r['mahasiswa_aktif'] ?? 0)), $bulanan), 'color' => '#4f46e5', 'label' => 'Mahasiswa'],
                            ['vals' => array_map(fn($r) => max(0, (float)($r['ipk'] ?? 0)), $bulanan),             'color' => '#0f766e', 'label' => 'IPK'],
                            ['vals' => array_map(fn($r) => max(0, (float)($r['dosen_tetap'] ?? 0)), $bulanan),     'color' => '#7c3aed', 'label' => 'Dosen'],
                            ['vals' => array_map(fn($r) => max(0, (float)($r['publikasi'] ?? 0)), $bulanan),       'color' => '#d97706', 'label' => 'Publikasi'],
                        ];
                    @endphp
                    @foreach ($previewCharts as $chart)
                        @php
                            $maxVal = max(1, ...array_values($chart['vals']));
                            $bars = '';
                            foreach (array_values($chart['vals']) as $i => $v) {
                                $x = 4 + $i * 9;
                                $h = max(2, round(($v / $maxVal) * 52));
                                $y = 56 - $h;
                                $bars .= "<rect x=\"{$x}\" y=\"{$y}\" width=\"7\" height=\"{$h}\" fill=\"{$chart['color']}\" rx=\"1.5\" opacity=\"0.85\"/>";
                            }
                        @endphp
                        <div class="rounded-xl border border-zinc-200 bg-white p-3 shadow-sm">
                            <p class="mb-1.5 text-[11px] font-semibold text-zinc-500">{{ $chart['label'] }}</p>
                            <svg viewBox="0 0 120 62" class="h-14 w-full">
                                {!! $bars !!}
                                <line x1="0" y1="58" x2="120" y2="58" stroke="#e4e4e7" stroke-width="1"/>
                                @foreach ([0=>'Jan',3=>'Apr',6=>'Jul',9=>'Okt'] as $mi => $ml)
                                    <text x="{{ 4 + $mi * 9 + 3.5 }}" y="62" text-anchor="middle" font-size="5.5" fill="#a1a1aa">{{ $ml }}</text>
                                @endforeach
                            </svg>
                            <p class="mt-1 text-right text-[10px] text-zinc-400">Maks: {{ number_format($maxVal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    @endif

</div>
