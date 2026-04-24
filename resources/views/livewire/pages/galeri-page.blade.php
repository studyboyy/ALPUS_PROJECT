<section class="section-box rounded-2xl p-6 md:p-8" x-data="{ open: false, image: '', title: '' }" @keydown.escape.window="open = false">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="display-font text-4xl leading-tight">Galeri Kegiatan</h2>
            <p class="mt-3 text-sm text-(--muted)">Dokumentasi lengkap kegiatan akademik, kemahasiswaan, pengabdian
                masyarakat,
                dan kolaborasi institusional.</p>
        </div>
        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Total foto:
            {{ count($galleryItems) }}</span>
    </div>

    <div class="mt-5 flex flex-wrap gap-2">
        <button type="button" wire:click="pilihKategori('Semua')"
            class="rounded-full px-4 py-2 text-xs font-semibold {{ $kategoriDipilih === 'Semua' ? 'bg-(--accent) text-white' : 'border border-(--line) bg-white text-slate-700' }}">Semua</button>
        @foreach ($kategoriList as $kategori)
            <div class="flex items-center overflow-hidden rounded-full border border-(--line) bg-white">
                <button type="button" wire:click="pilihKategori('{{ $kategori }}')"
                    class="px-4 py-2 text-xs font-semibold {{ $kategoriDipilih === $kategori ? 'bg-(--accent) text-white' : 'text-slate-700' }}">{{ $kategori }}</button>
                <a wire:navigate.hover
                    href="{{ route('galeri.category', ['kategori' => $kategoriSlugMap[$kategori] ?? Illuminate\Support\Str::slug($kategori)]) }}"
                    class="border-l border-(--line) px-3 py-2 text-[11px] font-semibold text-slate-500 hover:bg-slate-50">URL</a>
            </div>
        @endforeach
    </div>

    <div class="mt-6 space-y-8">
        @foreach (collect($galleryItems)->groupBy(fn($item) => data_get($item, 'category', 'Galeri')) as $category => $items)
            <section>
                <div class="mb-4 flex items-center gap-3">
                    <h3 class="text-xl font-bold">{{ $category }}</h3>
                    <span
                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $items->count() }}
                        foto</span>
                    <a wire:navigate.hover
                        href="{{ route('galeri.category', ['kategori' => $kategoriSlugMap[$category] ?? Illuminate\Support\Str::slug($category)]) }}"
                        class="text-xs font-semibold text-(--accent) hover:underline">Buka kategori ini</a>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($items as $item)
                        <article
                            class="group overflow-hidden rounded-2xl border border-(--line) bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <button type="button" class="block w-full text-left"
                                @click="image = @js(data_get($item, 'image_url')); title = @js(data_get($item, 'title')); open = true">
                                <img src="{{ data_get($item, 'image_url') }}" alt="{{ data_get($item, 'title') }}"
                                    class="h-56 w-full object-cover transition duration-300 group-hover:scale-105">
                            </button>
                            <div class="px-4 py-3">
                                <p class="text-sm font-semibold">{{ data_get($item, 'title') }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        @if (count($galleryItems) === 0)
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-500">
                Belum ada foto pada kategori ini.
            </div>
        @endif
    </div>

    <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/80 p-4" style="display: none;"
        @click.self="open = false">
        <div class="mx-auto mt-8 max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl">
            <img :src="image" :alt="title" class="max-h-[75vh] w-full object-contain bg-slate-100">
            <div class="flex items-center justify-between px-5 py-3">
                <p class="text-sm font-semibold text-slate-800" x-text="title"></p>
                <button type="button" @click="open = false"
                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Tutup</button>
            </div>
        </div>
    </div>
</section>
