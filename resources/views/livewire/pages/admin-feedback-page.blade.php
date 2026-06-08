<div class="space-y-6">

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-indigo-400">Kelola Konten</p>
            <h2 class="mt-0.5 text-lg font-extrabold text-zinc-800">Inbox Umpan Balik</h2>
            <p class="mt-0.5 text-xs text-zinc-500">Semua pesan dari form kontak beranda masuk ke sini.</p>
        </div>
        <div class="flex items-center gap-2">
            @if ($belumDibaca > 0)
                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    {{ $belumDibaca }} belum dibaca
                </span>
            @else
                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                    Semua terbaca
                </span>
            @endif
        </div>
    </div>

    {{-- ── Feedback list ── --}}
    <div class="space-y-3">
        @forelse ($feedbackItems as $item)
            <article class="section-box rounded-2xl p-5 transition-all {{ $item->read_at ? '' : 'border-l-4 border-l-indigo-400' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            @if (!$item->read_at)
                                <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-indigo-700">Baru</span>
                            @endif
                            <h3 class="text-sm font-bold text-zinc-800">{{ $item->subject }}</h3>
                        </div>
                        <p class="mt-1 text-xs text-zinc-400">
                            <span class="font-semibold text-zinc-600">{{ $item->name }}</span>
                            &nbsp;·&nbsp;{{ $item->email }}
                            &nbsp;·&nbsp;{{ $item->created_at?->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex flex-shrink-0 items-center gap-2">
                        @if (!$item->read_at)
                            <button type="button" wire:click="tandaiDibaca({{ $item->id }})"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Tandai Dibaca
                            </button>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                Dibaca
                            </span>
                        @endif
                        <button type="button" wire:click="hapusFeedback({{ $item->id }})"
                            wire:confirm="Hapus umpan balik ini?"
                            wire:loading.attr="disabled"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-rose-50 text-rose-600 transition hover:bg-rose-100 active:scale-95">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
                <div class="mt-4 rounded-xl bg-zinc-50 px-4 py-3 text-sm leading-relaxed text-zinc-700">
                    {{ $item->message }}
                </div>
            </article>
        @empty
            <div class="flex flex-col items-center gap-4 rounded-2xl border-2 border-dashed border-zinc-200 py-16 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-zinc-100">
                    <svg class="h-7 w-7 text-zinc-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-zinc-500">Belum ada umpan balik</p>
                    <p class="mt-0.5 text-xs text-zinc-400">Pesan dari form kontak akan muncul di sini.</p>
                </div>
            </div>
        @endforelse
    </div>

</div>
