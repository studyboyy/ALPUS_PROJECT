<div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="section-box rounded-2xl border-t-4 border-blue-500 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Program Aktif</p>
            <p class="mt-2 text-3xl font-extrabold text-blue-700">{{ collect($programItems ?? [])->count() }}</p>
            <p class="mt-1 text-xs text-slate-500">Total item program dan agenda</p>
        </article>
        <article class="section-box rounded-2xl border-t-4 border-violet-500 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Program</p>
            <p class="mt-2 text-3xl font-extrabold text-violet-700">
                {{ collect($programItems ?? [])->where('type', 'Program')->count() }}</p>
            <p class="mt-1 text-xs text-slate-500">Item bertipe program</p>
        </article>
        <article class="section-box rounded-2xl border-t-4 border-emerald-500 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Agenda</p>
            <p class="mt-2 text-3xl font-extrabold text-emerald-700">
                {{ collect($programItems ?? [])->where('type', 'Agenda')->count() }}</p>
            <p class="mt-1 text-xs text-slate-500">Item bertipe agenda</p>
        </article>
    </section>

    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="display-font text-4xl leading-tight">Program dan Agenda</h2>
                <p class="mt-2 text-sm text-(--muted)">Kelola kartu program unggulan dan agenda yang tampil di dashboard
                    publik.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">Content
                    Manager</span>
                <button type="button" wire:click="tambahAgenda"
                    class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">Tambah
                    Agenda</button>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            @foreach ($daftarTahun as $tahun)
                <button type="button" wire:click="pilihTahun({{ $tahun }})"
                    class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $tahunDipilih === $tahun ? 'bg-violet-600 text-white shadow-md shadow-violet-100' : 'border border-slate-300 bg-white hover:bg-slate-50' }}">{{ $tahun }}</button>
            @endforeach
        </div>

    </section>

    <section class="section-box rounded-3xl p-6 md:p-8">
        <form wire:submit="simpanProgram" class="space-y-4">
            @foreach ($programItems as $index => $item)
                <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-2">
                    <div class="md:col-span-2 flex justify-end">
                        <button type="button" wire:click="hapusAgenda({{ $index }})"
                            class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">Hapus
                            Item</button>
                    </div>
                    <label class="text-sm">Tipe
                        <input wire:model.defer="programItems.{{ $index }}.type" type="text"
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                    </label>
                    <label class="text-sm">Warna
                        <select wire:model.defer="programItems.{{ $index }}.style_key"
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                            @foreach ($styleOptions as $option)
                                <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm md:col-span-2">Judul
                        <input wire:model.defer="programItems.{{ $index }}.title" type="text"
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                    </label>
                    <label class="text-sm md:col-span-2">Deskripsi
                        <textarea wire:model.defer="programItems.{{ $index }}.description" rows="3"
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                    </label>
                </div>
            @endforeach

            <button type="submit"
                class="rounded-2xl bg-linear-to-r from-emerald-600 to-teal-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-100">Simpan
                Program & Agenda</button>
        </form>
    </section>
</div>
