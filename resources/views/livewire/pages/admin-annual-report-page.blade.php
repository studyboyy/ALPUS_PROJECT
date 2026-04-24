<div class="space-y-6">
    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="display-font text-4xl leading-tight">Konten Laporan Tahunan</h2>
                <p class="mt-2 text-sm text-(--muted)">Kelola isi setiap bagian laporan tahunan per tahun secara dinamis.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach ($daftarTahun as $tahun)
                    <button type="button" wire:click="pilihTahun({{ $tahun }})"
                        class="rounded-full px-4 py-2 text-xs font-semibold {{ $tahunDipilih === $tahun ? 'bg-(--accent) text-white' : 'border border-slate-300 bg-white text-slate-700' }}">{{ $tahun }}</button>
                @endforeach
            </div>
        </div>

    </section>

    <section class="section-box rounded-3xl p-6 md:p-8">
        <form wire:submit="simpan" class="space-y-4">
            @foreach ($sections as $index => $section)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="grid gap-3">
                        <div
                            class="rounded-xl bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">
                            {{ str_replace('-', ' ', data_get($section, 'section_key')) }}
                        </div>
                        <label class="text-sm">Judul Bagian
                            <input wire:model.defer="sections.{{ $index }}.title" type="text"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                        </label>
                        <label class="text-sm">Ringkasan
                            <textarea wire:model.defer="sections.{{ $index }}.summary" rows="2"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                        </label>
                        <label class="text-sm">Isi Lengkap
                            <textarea wire:model.defer="sections.{{ $index }}.content" rows="6"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                        </label>
                    </div>
                </div>
            @endforeach

            <button type="submit"
                class="rounded-2xl bg-linear-to-r from-indigo-600 to-blue-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-100">Simpan
                Konten Laporan</button>
        </form>
    </section>
</div>
