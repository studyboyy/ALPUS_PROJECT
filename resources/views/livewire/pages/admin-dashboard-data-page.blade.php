<div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="section-box rounded-2xl border-t-4 border-blue-500 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Mahasiswa Aktif</p>
            <p class="mt-2 text-3xl font-extrabold text-blue-700">
                {{ number_format((float) $statistik['mahasiswa_aktif'], 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-500">Data tahun {{ $tahunDipilih }}</p>
        </article>
        <article class="section-box rounded-2xl border-t-4 border-violet-500 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">IPK Rata-rata</p>
            <p class="mt-2 text-3xl font-extrabold text-violet-700">
                {{ number_format((float) $statistik['ipk'], 2, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-500">Indikator mutu akademik</p>
        </article>
        <article class="section-box rounded-2xl border-t-4 border-emerald-500 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Publikasi</p>
            <p class="mt-2 text-3xl font-extrabold text-emerald-700">
                {{ number_format((float) $statistik['publikasi'], 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-slate-500">Output riset dosen/mahasiswa</p>
        </article>
        <article class="section-box rounded-2xl border-t-4 border-amber-500 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Program Aktif</p>
            <p class="mt-2 text-3xl font-extrabold text-amber-700">{{ collect($programItems ?? [])->count() }}</p>
            <p class="mt-1 text-xs text-slate-500">Item program & agenda</p>
        </article>
    </section>

    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="display-font text-4xl leading-tight">Admin Data Dashboard</h2>
                <p class="mt-2 text-sm text-(--muted)">Kelola data statistik tahunan dan program unggulan</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Data Manager</span>
        </div>

    </section>

    <section id="statistik-form" class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-bold">Statistik Tahunan</h3>
            <div class="flex flex-wrap items-center gap-2">
                <form wire:submit="tambahTahun" class="flex items-center gap-2">
                    <input wire:model.defer="tahunBaru" type="number" min="2000" max="2100"
                        placeholder="Tambah tahun"
                        class="w-32 rounded-full border border-slate-300 bg-white px-4 py-2 text-xs font-semibold shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                    <button type="submit"
                        class="rounded-full bg-slate-900 px-4 py-2 text-xs font-semibold text-white">Tambah</button>
                </form>
                @foreach ($daftarTahun as $tahun)
                    <div
                        class="flex items-center gap-1 rounded-full {{ $tahunDipilih === $tahun ? 'bg-(--accent) shadow-md shadow-blue-100' : 'border border-slate-300 bg-white' }} p-1">
                        <button type="button" wire:click="pilihTahun({{ $tahun }})"
                            class="rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $tahunDipilih === $tahun ? 'text-white' : 'text-slate-700 hover:bg-slate-50' }}">{{ $tahun }}</button>
                        <button type="button" wire:click="hapusTahun({{ $tahun }})"
                            class="rounded-full px-2 py-1 text-[11px] font-bold {{ $tahunDipilih === $tahun ? 'text-white/80 hover:bg-white/10' : 'text-rose-600 hover:bg-rose-50' }}">×</button>
                    </div>
                @endforeach
            </div>
        </div>

        <form wire:submit="simpanStatistik" class="mt-5 grid gap-4 md:grid-cols-2">
            <label class="text-sm">Mahasiswa Aktif
                <input wire:model.defer="statistik.mahasiswa_aktif" type="number" step="1" min="0"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm">IPK Rata-rata
                <input wire:model.defer="statistik.ipk" type="number" step="0.01" min="0" max="4"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm">Dosen Tetap
                <input wire:model.defer="statistik.dosen_tetap" type="number" step="1" min="0"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm">Publikasi
                <input wire:model.defer="statistik.publikasi" type="number" step="1" min="0"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>

            <label class="text-sm">Capaian Mahasiswa (%)
                <input wire:model.defer="statistik.capaian_mahasiswa" type="number" step="1" min="0"
                    max="100"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm">Capaian Lulusan (%)
                <input wire:model.defer="statistik.capaian_lulusan" type="number" step="1" min="0"
                    max="100"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm">Capaian Publikasi (%)
                <input wire:model.defer="statistik.capaian_publikasi" type="number" step="1" min="0"
                    max="100"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm">Capaian Kegiatan (%)
                <input wire:model.defer="statistik.capaian_kegiatan" type="number" step="1" min="0"
                    max="100"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <div class="md:col-span-2">
                <button type="submit"
                    class="rounded-2xl bg-linear-to-r from-sky-600 to-blue-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-100">Simpan
                    Statistik</button>
            </div>
        </form>
    </section>

    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="text-lg font-bold">Statistik Kinerja Tahunan Berjalan (Bulanan)</h3>
                <p class="mt-1 text-xs text-(--muted)">Data YTD dihitung otomatis dari input bulan Jan-Des untuk tahun
                    {{ $tahunDipilih }}. Untuk mengelola data bulanan, buka halaman khusus Bulanan Statistik.</p>
            </div>
            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">Bulan aktif sistem:
                {{ $bulanSekarang }}</span>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.monthly-stats') }}"
                class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Kelola
                Statistik Bulanan</a>
        </div>
    </section>

</div>
