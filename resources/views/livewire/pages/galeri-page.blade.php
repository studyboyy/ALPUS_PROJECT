<section
    class="section-box rounded-2xl p-6 md:p-8"
    x-data="{
        open: false,
        image: '',
        title: '',
        category: '',
        description: '',
        openModal(img, ttl, cat, desc) {
            this.image = img;
            this.title = ttl;
            this.category = cat;
            this.description = desc || '';
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        closeModal() {
            this.open = false;
            document.body.style.overflow = '';
        }
    }"
    @keydown.escape.window="closeModal()">

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-(--olive)">Dokumentasi Kegiatan</p>
            <h2 class="display-font mt-1.5 text-4xl leading-tight">Galeri</h2>
            <p class="mt-2 max-w-lg text-sm leading-relaxed text-(--muted)">
                Foto-foto kegiatan akademik, kemahasiswaan, pengabdian masyarakat, dan kolaborasi institusional.
            </p>
        </div>
        <span class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-600">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ count($galleryItems) }} foto
        </span>
    </div>

    {{-- ── Category filter ── --}}
    <div class="mt-6 flex flex-wrap gap-2">
        <button type="button" wire:click="pilihKategori('Semua')"
            class="rounded-full px-4 py-2 text-xs font-semibold transition-all duration-150
                   {{ $kategoriDipilih === 'Semua' ? 'bg-(--accent) text-white shadow-sm' : 'border border-(--line) bg-white text-slate-600 hover:border-blue-300 hover:text-(--accent)' }}">
            Semua
        </button>
        @foreach ($kategoriList as $kategori)
            <button type="button" wire:click="pilihKategori('{{ $kategori }}')"
                class="rounded-full px-4 py-2 text-xs font-semibold transition-all duration-150
                       {{ $kategoriDipilih === $kategori ? 'bg-(--accent) text-white shadow-sm' : 'border border-(--line) bg-white text-slate-600 hover:border-blue-300 hover:text-(--accent)' }}">
                {{ $kategori }}
            </button>
        @endforeach
    </div>

    {{-- ── Gallery groups ── --}}
    <div class="mt-10 space-y-12">
        @foreach (collect($galleryItems)->groupBy(fn($item) => data_get($item, 'category', 'Galeri')) as $category => $items)
            <div>
                {{-- Section header --}}
                <div class="mb-5 flex items-center gap-3">
                    <h3 class="text-base font-bold text-slate-800">{{ $category }}</h3>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-500">{{ $items->count() }}</span>
                    <div class="ml-auto h-px flex-1 bg-slate-100"></div>
                    <a wire:navigate.hover
                        href="{{ route('galeri.category', ['kategori' => $kategoriSlugMap[$category] ?? Illuminate\Support\Str::slug($category)]) }}"
                        class="text-xs font-semibold text-slate-400 transition-colors hover:text-(--accent)">
                        Lihat semua →
                    </a>
                </div>

                {{-- Photo grid --}}
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($items as $item)
                        @php
                            $imgUrl  = data_get($item, 'image_url', '');
                            $imgTitle = data_get($item, 'title', '');
                            $imgCat  = data_get($item, 'category', '');
                            $imgDesc = data_get($item, 'description', '');
                        @endphp
                        <article
                            class="group cursor-pointer overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:ring-blue-200"
                            @click="openModal(@js($imgUrl), @js($imgTitle), @js($imgCat), @js($imgDesc))">

                            {{-- Image wrapper --}}
                            <div class="relative overflow-hidden bg-slate-100">
                                <img src="{{ $imgUrl }}" alt="{{ $imgTitle }}"
                                    class="h-52 w-full object-cover transition-transform duration-500 group-hover:scale-[1.04]"
                                    loading="lazy">

                                {{-- Hover overlay --}}
                                <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-t from-slate-900/40 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-white/95 shadow-lg backdrop-blur-sm transition-transform duration-300 group-hover:scale-110">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Category badge --}}
                                <div class="absolute left-3 top-3">
                                    <span class="rounded-full bg-slate-900/60 px-2.5 py-0.5 text-[10px] font-semibold text-white backdrop-blur-sm">
                                        {{ $imgCat }}
                                    </span>
                                </div>
                            </div>

                            {{-- Card footer --}}
                            <div class="px-4 py-3.5">
                                <p class="text-sm font-semibold leading-snug text-slate-800">{{ $imgTitle }}</p>
                                @if ($imgDesc)
                                    <p class="mt-1 line-clamp-1 text-xs text-(--muted)">{{ $imgDesc }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if (count($galleryItems) === 0)
            <div class="flex flex-col items-center gap-4 rounded-2xl border-2 border-dashed border-slate-200 py-20 text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                    <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-500">Belum ada foto</p>
                    <p class="mt-1 text-xs text-slate-400">Foto akan muncul setelah ditambahkan melalui panel admin.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════
         LIGHTBOX MODAL
    ═══════════════════════════════════════════════ --}}
    <div
        x-show="open"
        x-transition:enter="transition duration-200 ease-out"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-150 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-8"
        style="display: none;"
        @click.self="closeModal()">

        {{-- Frosted backdrop --}}
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md"></div>

        {{-- Modal card --}}
        <div
            x-show="open"
            x-transition:enter="transition duration-200 ease-out"
            x-transition:enter-start="opacity-0 scale-[0.96] translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition duration-150 ease-in"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-[0.96]"
            class="relative z-10 flex w-full max-w-3xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">

            {{-- Top bar --}}
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div class="min-w-0 pr-4">
                    <p class="truncate text-sm font-bold text-slate-800" x-text="title"></p>
                    <p class="mt-0.5 text-[11px] font-medium uppercase tracking-wide text-slate-400" x-text="category"></p>
                </div>
                <button type="button" @click="closeModal()"
                    aria-label="Tutup lightbox"
                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-red-100 hover:text-red-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Image with padding + rounded ── NEW --}}
            <div class="bg-slate-50 px-5 py-4">
                <div class="overflow-hidden rounded-2xl bg-slate-100 shadow-inner">
                    <img :src="image" :alt="title"
                        class="block max-h-[55vh] w-full object-contain"
                        style="image-rendering: auto;">
                </div>
            </div>

            {{-- Description (visible only when present) --}}
            <div x-show="description !== ''" class="border-t border-slate-100 px-5 py-3.5">
                <p class="flex items-start gap-2 text-sm leading-relaxed text-slate-600">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="description"></span>
                </p>
            </div>

            {{-- Bottom bar --}}
            <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/70 px-5 py-3.5">
                <span class="text-[11px] text-slate-400">
                    Tekan <kbd class="rounded bg-slate-200 px-1.5 py-0.5 font-mono text-[10px] text-slate-500">Esc</kbd> untuk menutup
                </span>
                <a :href="image" target="_blank" rel="noreferrer"
                    class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Buka Penuh
                </a>
            </div>

        </div>
    </div>

</section>
