<div class="space-y-5">

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-indigo-400">Manajemen Konten</p>
            <h2 class="mt-0.5 text-lg font-extrabold text-zinc-800">Dokumen Publik</h2>
            <p class="mt-0.5 text-xs text-zinc-500">Kelola file unduhan yang tampil di portal publik.</p>
        </div>
        <button type="button" wire:click="tambahDokumen"
            class="inline-flex items-center gap-2 rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-xs font-semibold text-zinc-700 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Dokumen
        </button>
    </div>

    {{-- ── Category filter ── --}}
    @php
        $adminKategoriList = collect($documents)->pluck('category')->filter()->unique()->values();
    @endphp
    <div class="flex flex-wrap gap-2">
        <button type="button" wire:click="pilihKategori('Semua')"
            class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition
                   {{ $kategoriDipilih === 'Semua' ? 'bg-indigo-600 text-white shadow-sm' : 'border border-zinc-300 bg-white text-zinc-600 hover:border-indigo-300 hover:text-indigo-700' }}">
            Semua
        </button>
        @foreach ($adminKategoriList as $kategori)
            <button type="button" wire:click="pilihKategori('{{ $kategori }}')"
                class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition
                       {{ $kategoriDipilih === $kategori ? 'bg-indigo-600 text-white shadow-sm' : 'border border-zinc-300 bg-white text-zinc-600 hover:border-indigo-300 hover:text-indigo-700' }}">
                {{ $kategori }}
            </button>
        @endforeach
    </div>

    {{-- ── Form ── --}}
    <div class="section-box rounded-2xl p-6">
        <form wire:submit="simpanDokumen" class="space-y-4">
            @foreach ($documents as $index => $document)
                @continue($kategoriDipilih !== 'Semua' && data_get($document, 'category') !== $kategoriDipilih)
                <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Judul Dokumen</label>
                            <input wire:model.defer="documents.{{ $index }}.title" type="text"
                                @disabled(!auth()->user()?->isAdmin() && data_get($document, 'id'))
                                class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                            @error("documents.$index.title")<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Kategori</label>
                            <input wire:model.defer="documents.{{ $index }}.category" type="text"
                                @disabled(!auth()->user()?->isAdmin() && data_get($document, 'id'))
                                class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                            @error("documents.$index.category")<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Upload File</label>
                            <input wire:model="documentFiles.{{ $index }}" type="file"
                                @disabled(!auth()->user()?->isAdmin() && data_get($document, 'id'))
                                class="w-full rounded-xl border border-dashed border-indigo-300 bg-indigo-50/40 px-3.5 py-2.5 text-sm text-indigo-700"/>
                            <div wire:loading wire:target="documentFiles.{{ $index }}" class="mt-1 text-xs font-medium text-indigo-600">Mengunggah file...</div>
                            @error("documentFiles.$index")<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Deskripsi</label>
                            <textarea wire:model.defer="documents.{{ $index }}.description" rows="2"
                                @disabled(!auth()->user()?->isAdmin() && data_get($document, 'id'))
                                class="w-full resize-none rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                            @error("documents.$index.description")<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    {{-- File info + delete --}}
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2 rounded-lg bg-white px-3 py-2 ring-1 ring-zinc-200">
                            <svg class="h-4 w-4 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span class="max-w-xs truncate text-xs font-mono text-zinc-500">
                                {{ data_get($document, 'file_name') ?: 'Belum ada file' }}
                            </span>
                        </div>
                        @if(auth()->user()?->isAdmin())<button type="button" wire:click="hapusDokumen({{ $index }})"
                            wire:confirm="Hapus dokumen ini?"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>@endif
                    </div>
                </div>
            @endforeach

            @if (collect($documents)->filter(fn($d) => $kategoriDipilih === 'Semua' || data_get($d,'category') === $kategoriDipilih)->isEmpty())
                <div class="flex flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-zinc-200 py-12 text-center">
                    <svg class="h-8 w-8 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-zinc-400">Belum ada dokumen di kategori ini.</p>
                </div>
            @endif

            <div class="flex items-center justify-end border-t border-zinc-100 pt-4">
                <button type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95 disabled:opacity-60">
                    <svg wire:loading.remove class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    Publikasikan Dokumen
                </button>
            </div>
        </form>
    </div>

</div>
