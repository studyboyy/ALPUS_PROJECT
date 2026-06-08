<div class="space-y-5">

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-indigo-400">Konten Dinamis</p>
            <h2 class="mt-0.5 text-lg font-extrabold text-zinc-800">Laporan Tahunan</h2>
            <p class="mt-0.5 text-xs text-zinc-500">Kelola isi setiap bagian laporan per tahun.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach ($daftarTahun as $tahun)
                <button type="button" wire:click="pilihTahun({{ $tahun }})"
                    class="rounded-full px-4 py-2 text-xs font-semibold transition
                           {{ $tahunDipilih === $tahun
                               ? 'bg-indigo-600 text-white shadow-sm ring-2 ring-indigo-200'
                               : 'border border-zinc-300 bg-white text-zinc-600 hover:border-indigo-300 hover:text-indigo-700' }}">
                    {{ $tahun }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── Sections form ── --}}
    <div class="section-box rounded-2xl p-6">
        <form wire:submit="simpan" class="space-y-4">
            @foreach ($sections as $index => $section)
                <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5">
                    {{-- Section key badge --}}
                    <div class="mb-4 flex items-center gap-2">
                        <span class="rounded-lg bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-zinc-400 ring-1 ring-zinc-200">
                            {{ str_replace('-', ' ', data_get($section, 'section_key')) }}
                        </span>
                        <div class="ml-auto h-px flex-1 bg-zinc-200"></div>
                    </div>

                    <div class="grid gap-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Judul Bagian</label>
                            <input wire:model.defer="sections.{{ $index }}.title" type="text"
                                class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Ringkasan</label>
                            <textarea wire:model.defer="sections.{{ $index }}.summary" rows="2"
                                class="w-full resize-none rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Isi Lengkap</label>
                            <textarea wire:model.defer="sections.{{ $index }}.content" rows="6"
                                class="w-full resize-none rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                        </div>
                    </div>
                </div>
            @endforeach

            @if (empty($sections))
                <div class="flex flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-zinc-200 py-12 text-center">
                    <svg class="h-8 w-8 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm text-zinc-400">Belum ada seksi untuk tahun ini.</p>
                </div>
            @endif

            <div class="flex items-center justify-between border-t border-zinc-100 pt-4">
                <p class="text-xs text-zinc-400">Tahun aktif: <span class="font-semibold text-zinc-600">{{ $tahunDipilih }}</span></p>
                <button type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95 disabled:opacity-60">
                    <svg wire:loading.remove class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    Simpan Konten
                </button>
            </div>
        </form>
    </div>

</div>
