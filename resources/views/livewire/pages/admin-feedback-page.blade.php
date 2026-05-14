<div class="space-y-6">
    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="display-font text-4xl leading-tight">Inbox Umpan Balik</h2>
                <p class="mt-2 text-sm text-(--muted)">Semua pesan dari form kontak beranda masuk ke sini secara
                    real-time.</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Belum dibaca:
                {{ $belumDibaca }}</span>
        </div>
    </section>

    <section class="section-box rounded-3xl p-4 md:p-6">
        <div class="space-y-3">
            @forelse ($feedbackItems as $item)
                <article
                    class="rounded-2xl border border-slate-200 p-4 {{ $item->read_at ? 'bg-white' : 'bg-indigo-50/50' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">{{ $item->subject }}</h3>
                            <p class="text-xs text-slate-500">{{ $item->name }} · {{ $item->email }} ·
                                {{ $item->created_at?->format('d M Y H:i') }}</p>
                        </div>
                        <div class="flex gap-2">
                            @if (!$item->read_at)
                                <button type="button" wire:click="tandaiDibaca({{ $item->id }})"
                                    class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700">Tandai
                                    dibaca</button>
                            @else
                                <span
                                    class="rounded-lg bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">Sudah
                                    dibaca</span>
                            @endif
                            <button type="button" wire:click="hapusFeedback({{ $item->id }})"
                                wire:confirm="Apakah Anda yakin ingin menghapus umpan balik ini?"
                                class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100">Hapus</button>
                        </div>
                    </div>
                    <p class="mt-3 text-sm leading-relaxed text-slate-700">{{ $item->message }}</p>
                </article>
            @empty
                <div
                    class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                    Belum ada umpan balik masuk.
                </div>
            @endforelse
        </div>
    </section>
</div>
