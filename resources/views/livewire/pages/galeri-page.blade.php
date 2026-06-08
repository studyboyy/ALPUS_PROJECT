<div
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
            window.dispatchEvent(new CustomEvent('gallery-modal-open', {
                detail: { image: img, title: ttl, category: cat, description: desc || '' }
            }));
        },
        closeModal() {
            this.open = false;
            window.dispatchEvent(new CustomEvent('gallery-modal-close'));
        }
    }"
    @keydown.escape.window="closeModal()">

<section class="section-box rounded-2xl p-6 md:p-8">

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

    {{-- Loading skeleton --}}
    <div wire:loading wire:target="pilihKategori" class="mt-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @for ($i = 0; $i < 6; $i++)
            <div class="overflow-hidden rounded-2xl bg-slate-100">
                <div class="h-52 animate-pulse bg-slate-200"></div>
                <div class="p-4 space-y-2">
                    <div class="h-3 animate-pulse rounded bg-slate-200 w-3/4"></div>
                    <div class="h-3 animate-pulse rounded bg-slate-100 w-1/2"></div>
                </div>
            </div>
        @endfor
    </div>

    {{-- ── Gallery groups ── --}}
    <div wire:loading.remove wire:target="pilihKategori" class="mt-10 space-y-12">
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
    {{-- /wire:loading.remove --}}

    {{-- ═══════════════════════════════════════════════
         LIGHTBOX MODAL — rendered via portal layout
         trigger: window.openGalleryModal(img, title, cat, desc)
    ═══════════════════════════════════════════════ --}}

</section>{{-- /section.section-box --}}

</div>{{-- /x-data root --}}