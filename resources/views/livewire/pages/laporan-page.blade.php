<section class="grid gap-4 lg:grid-cols-[260px,1fr]">
    <aside class="section-box h-max rounded-2xl p-5">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-(--olive)">Daftar Tahun</p>
        @php
            $tahunLaporanAwal = collect($daftarTahun)->take(6)->all();
            $tahunLaporanLanjutan = collect($daftarTahun)->slice(6)->all();
        @endphp
        <div class="mt-3 space-y-2">
            @foreach ($tahunLaporanAwal as $tahun)
                <button type="button" wire:click="pilihTahun({{ $tahun }})"
                    class="w-full rounded-xl px-4 py-2 text-left text-sm font-semibold {{ $laporanAktif && $laporanAktif->year === $tahun ? 'bg-(--accent) text-white' : 'border border-(--line) bg-white' }}">{{ $tahun }}</button>
            @endforeach
            @if (count($tahunLaporanLanjutan) > 0)
                <details>
                    <summary class="cursor-pointer text-xs font-semibold text-(--muted)">Lihat semua tahun</summary>
                    <div class="mt-2 space-y-2">
                        @foreach ($tahunLaporanLanjutan as $tahun)
                            <button type="button" wire:click="pilihTahun({{ $tahun }})"
                                class="w-full rounded-xl px-4 py-2 text-left text-sm font-semibold {{ $laporanAktif && $laporanAktif->year === $tahun ? 'bg-(--accent) text-white' : 'border border-(--line) bg-white' }}">{{ $tahun }}</button>
                        @endforeach
                    </div>
                </details>
            @endif
        </div>
        <a href="{{ route('laporan.pdf', ['year' => $laporanAktif?->year]) }}"
            class="mt-4 inline-flex w-full items-center justify-center rounded-xl border border-(--line) bg-slate-50 px-4 py-2 text-sm font-semibold">Unduh
            versi PDF</a>
        <a href="{{ route('laporan.pdf.semua') }}"
            class="mt-2 inline-flex w-full items-center justify-center rounded-xl border border-(--line) bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700">Unduh
            PDF Semua Tahun</a>
    </aside>

    <article class="section-box rounded-2xl p-6">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="display-font text-4xl leading-tight">Laporan Tahunan {{ $laporanAktif?->year }}</h2>
            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">Update
                {{ now()->locale('id')->translatedFormat('F Y') }}</span>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-4">
            @foreach ($laporanAktif?->kpi ?? [] as $item)
                <div class="rounded-2xl border border-(--line) bg-white p-4 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.16em] text-(--muted)">{{ data_get($item, 'label') }}</p>
                    <p class="mt-2 text-2xl font-extrabold text-slate-800">
                        {{ number_format((float) data_get($item, 'value', 0), (int) data_get($item, 'decimals', 0), ',', '.') }}
                    </p>
                </div>
            @endforeach
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @foreach ($sections as $section)
                <div class="rounded-xl border border-(--line) bg-slate-50 p-4">
                    <h3 class="font-semibold">{{ $section->title }}</h3>
                    <p class="mt-2 text-sm text-(--muted)">{{ $section->summary }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-(--line) p-4">
                <p class="text-sm font-semibold">Grafik Capaian Indikator</p>
                <div class="mt-3 space-y-3 text-xs">
                    @foreach ($laporanAktif?->capaian ?? [] as $item)
                        <div>
                            <div class="mb-1 flex justify-between">
                                <span>{{ data_get($item, 'label') }}</span><span>{{ number_format((float) data_get($item, 'percent', 0), 0, ',', '.') }}%</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-linear-to-r from-sky-500 to-blue-600"
                                    style="width: {{ max(0, min(100, (float) data_get($item, 'percent', 0))) }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-(--line) p-4">
                <p class="text-sm font-semibold">Tabel Indikator Utama</p>
                <div class="mt-3 overflow-hidden rounded-lg border border-(--line)">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2">Indikator</th>
                                <th class="px-3 py-2">Realisasi</th>
                                <th class="px-3 py-2">Format</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporanAktif?->kpi ?? [] as $item)
                                <tr class="border-t border-(--line)">
                                    <td class="px-3 py-2">{{ data_get($item, 'label') }}</td>
                                    <td class="px-3 py-2">
                                        {{ number_format((float) data_get($item, 'value', 0), (int) data_get($item, 'decimals', 0), ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ (int) data_get($item, 'decimals', 0) > 0 ? 'Desimal' : 'Bilangan bulat' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-xl border border-(--line) p-4">
            <div class="mb-3 flex items-center justify-between gap-3">
                <p class="text-sm font-semibold">Program dan Agenda Terkait</p>
                <span
                    class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ count($programItems) }}
                    item</span>
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                @foreach ($programItems as $item)
                    <div class="rounded-xl border border-(--line) bg-slate-50 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">
                            {{ data_get($item, 'type') }}</p>
                        <h3 class="mt-2 font-semibold text-slate-800">{{ data_get($item, 'title') }}</h3>
                        <p class="mt-1 text-sm text-(--muted)">{{ data_get($item, 'description') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 space-y-4">
            @foreach ($sections as $section)
                <section class="rounded-2xl border border-(--line) bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="display-font text-2xl leading-tight">{{ $section->title }}</h3>
                        <span
                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $laporanAktif?->year }}</span>
                    </div>
                    @if ($section->summary)
                        <p class="mt-2 text-sm font-medium text-slate-600">{{ $section->summary }}</p>
                    @endif
                    <div class="mt-4 text-sm leading-relaxed text-slate-700 whitespace-pre-line">
                        {{ $section->content }}</div>
                </section>
            @endforeach
        </div>
    </article>
</section>
