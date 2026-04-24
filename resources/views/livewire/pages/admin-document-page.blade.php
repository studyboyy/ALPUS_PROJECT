<div class="space-y-6">
    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="display-font text-4xl leading-tight">Dokumen Publik</h2>
                <p class="mt-2 text-sm text-(--muted)">Kelola dokumen unduhan yang tampil di portal publik.</p>
            </div>
            <button type="button" wire:click="tambahDokumen"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Tambah
                Dokumen</button>
        </div>

    </section>

    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="mb-4 flex flex-wrap gap-2">
            @php
                $adminKategoriList = collect($documents)->pluck('category')->filter()->unique()->values();
            @endphp
            <button type="button" wire:click="pilihKategori('Semua')"
                class="rounded-full px-4 py-2 text-xs font-semibold {{ $kategoriDipilih === 'Semua' ? 'bg-(--accent) text-white' : 'border border-slate-300 bg-white text-slate-700' }}">Semua</button>
            @foreach ($adminKategoriList as $kategori)
                <button type="button" wire:click="pilihKategori('{{ $kategori }}')"
                    class="rounded-full px-4 py-2 text-xs font-semibold {{ $kategoriDipilih === $kategori ? 'bg-(--accent) text-white' : 'border border-slate-300 bg-white text-slate-700' }}">{{ $kategori }}</button>
            @endforeach
        </div>

        <form wire:submit="simpanDokumen" class="space-y-4">
            @foreach ($documents as $index => $document)
                @continue($kategoriDipilih !== 'Semua' && data_get($document, 'category') !== $kategoriDipilih)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="text-sm md:col-span-2">Judul Dokumen
                            <input wire:model.defer="documents.{{ $index }}.title" type="text"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                        </label>
                        <label class="text-sm">Kategori Dokumen
                            <input wire:model.defer="documents.{{ $index }}.category" type="text"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                        </label>
                        <label class="text-sm md:col-span-2">Deskripsi
                            <textarea wire:model.defer="documents.{{ $index }}.description" rows="3"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                        </label>
                        <label class="text-sm">Upload File Dokumen
                            <input wire:model="documentFiles.{{ $index }}" type="file"
                                class="mt-2 w-full rounded-xl border border-dashed border-indigo-200 bg-indigo-50/50 px-3 py-2 text-sm text-indigo-700" />
                        </label>
                        <div class="rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-600">
                            <p class="font-semibold text-slate-800">File aktif</p>
                            <p class="mt-1 break-all">{{ data_get($document, 'file_name') ?: 'Belum ada file' }}</p>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="button" wire:click="hapusDokumen({{ $index }})"
                            class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">Hapus</button>
                    </div>
                </div>
            @endforeach

            @if (collect($documents)->filter(fn($document) => $kategoriDipilih === 'Semua' || data_get($document, 'category') === $kategoriDipilih)->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-500">
                    Belum ada dokumen pada kategori ini di panel admin.
                </div>
            @endif

            <button type="submit"
                class="rounded-2xl bg-linear-to-r from-indigo-600 to-blue-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-100">Publikasikan
                Dokumen</button>
        </form>
    </section>
</div>
