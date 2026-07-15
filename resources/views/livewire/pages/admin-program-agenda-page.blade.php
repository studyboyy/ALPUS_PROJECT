<div class="space-y-5">

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-violet-400">Manajemen Konten</p>
            <h2 class="mt-0.5 text-lg font-extrabold text-zinc-800">Program &amp; Agenda</h2>
            <p class="mt-0.5 text-xs text-zinc-500">Kelola kartu program unggulan dan agenda di portal publik.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Year tabs --}}
            @foreach ($daftarTahun as $tahun)
                <button type="button" wire:click="pilihTahun({{ $tahun }})"
                    class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition
                           {{ $tahunDipilih === $tahun
                               ? 'bg-violet-600 text-white shadow-sm ring-2 ring-violet-200'
                               : 'border border-zinc-300 bg-white text-zinc-600 hover:border-violet-300 hover:text-violet-700' }}">
                    {{ $tahun }}
                </button>
            @endforeach
            <button type="button" wire:click="tambahAgenda"
                class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-100">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah
            </button>
        </div>
    </div>

    {{-- ── Stats row ── --}}
    @php
        $items = collect($programItems ?? []);
        $statsRow = [
            ['label'=>'Total Item',        'value'=>$items->count(),                                                         'color'=>'text-blue-700',   'border'=>'border-t-blue-500'],
            ['label'=>'Program',           'value'=>$items->where('type','Program')->count(),                                 'color'=>'text-violet-700', 'border'=>'border-t-violet-500'],
            ['label'=>'Agenda',            'value'=>$items->where('type','Agenda')->count(),                                  'color'=>'text-emerald-700','border'=>'border-t-emerald-500'],
            ['label'=>'Agenda Terlaksana', 'value'=>$items->where('type','Agenda')->where('execution_status','terlaksana')->count(), 'color'=>'text-teal-700','border'=>'border-t-teal-500'],
        ];
    @endphp
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($statsRow as $s)
            <div class="section-box rounded-2xl border-t-4 {{ $s['border'] }} p-4">
                <p class="text-[11px] font-bold uppercase tracking-wide text-zinc-400">{{ $s['label'] }}</p>
                <p class="mt-2 text-2xl font-extrabold {{ $s['color'] }}">{{ $s['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ── Items form ── --}}
    <div class="section-box rounded-2xl p-6">
        {{-- Reorder hint --}}
        <div class="mb-5 flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3">
            <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-xs text-blue-700">Gunakan <strong>↑ Naik</strong> / <strong>↓ Turun</strong> untuk mengatur urutan tampil. Urutan tersimpan otomatis.</p>
        </div>

        <form wire:submit="simpanProgram" class="space-y-4">
            @foreach ($programItems as $index => $item)
                <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5" wire:key="program-item-{{ $index }}">
                    {{-- Item header: order controls + delete --}}
                    <div class="mb-4 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <span class="rounded-lg bg-white px-2.5 py-1 text-[10px] font-bold text-zinc-400 ring-1 ring-zinc-200">#{{ $index + 1 }}</span>
                            @if ($index > 0)
                                <button type="button" wire:click="naikItem({{ $index }})"
                                    class="rounded-lg border border-zinc-300 bg-white px-2.5 py-1.5 text-xs font-bold text-zinc-600 transition hover:bg-zinc-100" title="Naik">↑</button>
                            @endif
                            @if ($index < count($programItems) - 1)
                                <button type="button" wire:click="turunItem({{ $index }})"
                                    class="rounded-lg border border-zinc-300 bg-white px-2.5 py-1.5 text-xs font-bold text-zinc-600 transition hover:bg-zinc-100" title="Turun">↓</button>
                            @endif
                        </div>
                        @if(auth()->user()?->isAdmin())<button type="button" wire:click="hapusAgenda({{ $index }})"
                            wire:confirm="Hapus item ini?"
                            class="inline-flex items-center gap-1 rounded-xl bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>@endif
                    </div>

                    {{-- Fields --}}
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Tipe</label>
                            <input wire:model.defer="programItems.{{ $index }}.type" type="text"
                                class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Warna Kartu</label>
                            <select wire:model.defer="programItems.{{ $index }}.style_key"
                                class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                @foreach ($styleOptions as $option)
                                    <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Status</label>
                            <select wire:model.defer="programItems.{{ $index }}.execution_status"
                                class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                <option value="terlaksana">Terlaksana</option>
                                <option value="belum_terlaksana">Belum Terlaksana</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Judul</label>
                            <input wire:model.defer="programItems.{{ $index }}.title" type="text"
                                class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                        </div>
                        <div class="md:col-span-3">
                            <label class="mb-1.5 block text-xs font-semibold text-zinc-600">Deskripsi <span class="text-zinc-400">(detail lengkap program/agenda)</span></label>
                            <textarea wire:model.defer="programItems.{{ $index }}.description" rows="4"
                                class="w-full resize-none rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                        </div>
                    </div>
                </div>
            @endforeach

            @if (empty($programItems))
                <div class="flex flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-zinc-200 py-14 text-center">
                    <svg class="h-8 w-8 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-zinc-500">Belum ada item untuk tahun {{ $tahunDipilih }}</p>
                        <p class="mt-0.5 text-xs text-zinc-400">Klik <strong>Tambah</strong> di atas untuk menambahkan.</p>
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-end border-t border-zinc-100 pt-4">
                <button type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95 disabled:opacity-60">
                    <svg wire:loading.remove class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    Simpan Program &amp; Agenda
                </button>
            </div>
        </form>
    </div>

</div>
