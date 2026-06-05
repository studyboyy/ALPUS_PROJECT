<div class="space-y-5">

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold">Statistik Bulanan</h2>
            <p class="mt-0.5 text-xs text-slate-500">Data KPI per bulan untuk tahun yang dipilih. Semua nilai minimum 1.</p>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-slate-600">Tahun</label>
            <select wire:change="pilihTahun($event.target.value)"
                class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm shadow-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                @foreach ($daftarTahun as $tahun)
                    <option value="{{ $tahun }}" @selected($tahun == $tahunDipilih)>{{ $tahun }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">

        {{-- ── Form tabel 12 bulan ── --}}
        <div class="lg:col-span-2">
            <form wire:submit="simpanBulanan">
                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Bulan</th>
                                <th class="px-4 py-3 text-right">Mahasiswa</th>
                                <th class="px-4 py-3 text-right">IPK</th>
                                <th class="px-4 py-3 text-right">Dosen</th>
                                <th class="px-4 py-3 text-right">Publikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($bulanan as $index => $row)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-2.5 font-medium text-slate-700">{{ $row['month_label'] }}</td>
                                    <td class="px-4 py-2.5">
                                        <input type="number" step="1" min="1"
                                            wire:model.defer="bulanan.{{ $index }}.mahasiswa_aktif"
                                            class="w-24 rounded-lg border border-slate-200 px-2 py-1.5 text-right text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-100" />
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <input type="number" step="0.01" min="0" max="4"
                                            wire:model.defer="bulanan.{{ $index }}.ipk"
                                            class="w-20 rounded-lg border border-slate-200 px-2 py-1.5 text-right text-sm focus:border-teal-400 focus:outline-none focus:ring-1 focus:ring-teal-100" />
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <input type="number" step="1" min="1"
                                            wire:model.defer="bulanan.{{ $index }}.dosen_tetap"
                                            class="w-20 rounded-lg border border-slate-200 px-2 py-1.5 text-right text-sm focus:border-violet-400 focus:outline-none focus:ring-1 focus:ring-violet-100" />
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <input type="number" step="1" min="1"
                                            wire:model.defer="bulanan.{{ $index }}.publikasi"
                                            class="w-20 rounded-lg border border-slate-200 px-2 py-1.5 text-right text-sm focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-100" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Simpan Data Bulanan
                    </button>
                    <p class="text-xs text-slate-400">Perubahan langsung mempengaruhi grafik dan YTD di portal.</p>
                </div>
            </form>
        </div>

        {{-- ── Preview mini charts ── --}}
        <div class="space-y-4">

            @php
                $mhsVals = array_map(fn($r) => max(0, (float) ($r['mahasiswa_aktif'] ?? 0)), $bulanan);
                $ipkVals = array_map(fn($r) => max(0, (float) ($r['ipk'] ?? 0)), $bulanan);
                $dosVals = array_map(fn($r) => max(0, (float) ($r['dosen_tetap'] ?? 0)), $bulanan);
                $pubVals = array_map(fn($r) => max(0, (float) ($r['publikasi'] ?? 0)), $bulanan);

                $renderBar = function(array $vals, string $color, string $label) {
                    $max = max(1, ...array_values($vals));
                    $bars = '';
                    foreach (array_values($vals) as $i => $v) {
                        $x = 4 + $i * 9;
                        $h = max(1, round(($v / $max) * 56));
                        $y = 60 - $h;
                        $bars .= "<rect x=\"$x\" y=\"$y\" width=\"7\" height=\"$h\" fill=\"$color\" rx=\"1.5\" opacity=\"0.85\" />";
                    }
                    return ['bars' => $bars, 'max' => $max, 'label' => $label];
                };

                $charts = [
                    $renderBar($mhsVals, '#2563eb', 'Mahasiswa Aktif'),
                    $renderBar($ipkVals, '#0f766e', 'IPK Rata-rata'),
                    $renderBar($dosVals, '#7c3aed', 'Dosen Tetap'),
                    $renderBar($pubVals, '#d97706', 'Publikasi'),
                ];
            @endphp

            @foreach ($charts as $chart)
                <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <p class="mb-2 text-xs font-semibold text-slate-500">{{ $chart['label'] }}</p>
                    <svg viewBox="0 0 120 66" class="h-16 w-full">
                        {!! $chart['bars'] !!}
                        <line x1="0" y1="62" x2="120" y2="62" stroke="#e2e8f0" stroke-width="1"/>
                        {{-- Month labels: Jan, Apr, Jul, Oct --}}
                        @foreach ([0=>'Jan',3=>'Apr',6=>'Jul',9=>'Okt'] as $mi => $ml)
                            <text x="{{ 4 + $mi * 9 + 3.5 }}" y="66" text-anchor="middle" font-size="5.5" fill="#94a3b8">{{ $ml }}</text>
                        @endforeach
                    </svg>
                    <p class="mt-1 text-right text-[10px] text-slate-400">Maks: {{ number_format($chart['max'], 0, ',', '.') }}</p>
                </div>
            @endforeach

        </div>
    </div>

</div>
