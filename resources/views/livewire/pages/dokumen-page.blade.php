<section class="section-box rounded-2xl p-6 md:p-8">
    <h2 class="display-font text-4xl leading-tight">Dokumen Pendukung</h2>
    <p class="mt-3 text-sm text-(--muted)">Daftar file yang dapat diunduh untuk kebutuhan audit mutu, evaluasi tahunan,
        dan persiapan akreditasi.</p>

    <div class="mt-5 flex flex-wrap gap-2">
        <button type="button" wire:click="pilihKategori('Semua')"
            class="rounded-full px-4 py-2 text-xs font-semibold {{ $kategoriDipilih === 'Semua' ? 'bg-(--accent) text-white' : 'border border-(--line) bg-white text-slate-700' }}">Semua</button>
        <a href="{{ route('dokumen.pdf') }}"
            class="rounded-full border border-(--line) bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Export
            PDF Semua</a>
        @foreach ($kategoriList as $kategori)
            <div class="flex items-center overflow-hidden rounded-full border border-(--line) bg-white">
                <button type="button" wire:click="pilihKategori('{{ $kategori }}')"
                    class="px-4 py-2 text-xs font-semibold {{ $kategoriDipilih === $kategori ? 'bg-(--accent) text-white' : 'text-slate-700' }}">{{ $kategori }}</button>
                <a wire:navigate.hover
                    href="{{ route('dokumen.category', ['kategori' => $kategoriSlugMap[$kategori] ?? Illuminate\Support\Str::slug($kategori)]) }}"
                    class="border-l border-(--line) px-3 py-2 text-[11px] font-semibold text-slate-500 hover:bg-slate-50">URL</a>
                <a href="{{ route('dokumen.pdf', ['kategori' => $kategoriSlugMap[$kategori] ?? Illuminate\Support\Str::slug($kategori)]) }}"
                    class="border-l border-(--line) px-3 py-2 text-[11px] font-semibold text-slate-500 hover:bg-slate-50">PDF</a>
            </div>
        @endforeach
    </div>

    <div class="mt-6 space-y-6">
        @foreach ($documents->groupBy(fn($document) => $document->category ?: 'Dokumen Pendukung') as $category => $items)
            <section>
                <div class="mb-3 flex items-center gap-3">
                    <h3 class="text-lg font-bold">{{ $category }}</h3>
                    <span
                        class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $items->count() }}
                        dokumen</span>
                    <a wire:navigate.hover
                        href="{{ route('dokumen.category', ['kategori' => $kategoriSlugMap[$category] ?? Illuminate\Support\Str::slug($category)]) }}"
                        class="text-xs font-semibold text-(--accent) hover:underline">Buka kategori ini</a>
                    <a href="{{ route('dokumen.pdf', ['kategori' => $kategoriSlugMap[$category] ?? Illuminate\Support\Str::slug($category)]) }}"
                        class="text-xs font-semibold text-slate-500 hover:underline">Export PDF</a>
                </div>
                <div class="space-y-3">
                    @foreach ($items as $document)
                        <article
                            class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-(--line) bg-slate-50 p-4">
                            <div>
                                <h3 class="font-semibold">{{ $document->title }}</h3>
                                <p class="mt-1 text-sm text-(--muted)">{{ $document->description }}</p>
                            </div>
                            <a href="{{ $document->file_url }}" download
                                class="rounded-full bg-(--accent) px-4 py-2 text-xs font-semibold text-white">Unduh
                                {{ $document->file_name ?: 'Dokumen' }}</a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        @if ($documents->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-500">
                Belum ada dokumen pada kategori ini.
            </div>
        @endif
    </div>
</section>
