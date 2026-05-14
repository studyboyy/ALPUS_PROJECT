<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Statistik Bulanan — Tahun {{ $this->tahunDipilih }}</h2>
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-600">Pilih Tahun</label>
            <select wire:change="pilihTahun($event.target.value)" class="border rounded px-2 py-1">
                @foreach ($daftarTahun as $tahun)
                    <option value="{{ $tahun }}" @if ($tahun == $this->tahunDipilih) selected @endif>
                        {{ $tahun }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="col-span-2">
            <form wire:submit.prevent="simpanBulanan">
                <div class="overflow-x-auto border rounded">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-2">Bulan</th>
                                <th class="p-2">Mahasiswa Aktif</th>
                                <th class="p-2">IPK</th>
                                <th class="p-2">Dosen Tetap</th>
                                <th class="p-2">Publikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bulanan as $index => $row)
                                <tr class="border-t">
                                    <td class="p-2">{{ $row['month_label'] }}</td>
                                    <td class="p-2"><input type="number" step="0.1"
                                            wire:model.defer="bulanan.{{ $index }}.mahasiswa_aktif"
                                            class="w-32 border rounded px-2 py-1" /></td>
                                    <td class="p-2"><input type="number" step="0.01"
                                            wire:model.defer="bulanan.{{ $index }}.ipk"
                                            class="w-20 border rounded px-2 py-1" /></td>
                                    <td class="p-2"><input type="number" step="1"
                                            wire:model.defer="bulanan.{{ $index }}.dosen_tetap"
                                            class="w-24 border rounded px-2 py-1" /></td>
                                    <td class="p-2"><input type="number" step="1"
                                            wire:model.defer="bulanan.{{ $index }}.publikasi"
                                            class="w-24 border rounded px-2 py-1" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-3">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan Bulanan</button>
                </div>
            </form>
        </div>

        <div class="col-span-1">
            <div class="border rounded p-3">
                <h3 class="font-medium mb-2">Preview: Mahasiswa Aktif</h3>
                @php
                    $values = array_map(fn($r) => (float) ($r['mahasiswa_aktif'] ?? 0), $bulanan);
                    $max = max(1, ...$values);
                @endphp
                <svg viewBox="0 0 120 84" class="w-full h-36">
                    @foreach ($values as $i => $v)
                        @php
                            $x = 5 + $i * 9;
                            $h = ($v / $max) * 60;
                            $y = 70 - $h;
                        @endphp
                        <rect x="{{ $x }}" y="{{ $y }}" width="6"
                            height="{{ $h }}" fill="#2563eb" rx="1"></rect>
                    @endforeach
                    <line x1="0" y1="70" x2="120" y2="70" stroke="#e5e7eb"
                        stroke-width="1" />
                </svg>

                <div class="text-xs text-gray-600 mt-2">
                    Nilai maksimal: {{ $max }}
                </div>
            </div>
        </div>
    </div>
</div>
