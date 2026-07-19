<section class="section-box rounded-2xl p-6 md:p-8">

    {{-- ── Header ── --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="display-font text-4xl leading-tight">Dokumen Pendukung</h2>
            <p class="mt-2 max-w-xl text-sm leading-relaxed text-(--muted)">
                File resmi yang dapat diunduh untuk kebutuhan audit mutu, evaluasi tahunan, dan persiapan akreditasi.
            </p>
        </div>
        <a href="{{ route('dokumen.pdf') }}"
            class="btn-outline flex-shrink-0">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Export PDF Semua
        </a>
    </div>

    {{-- ── Category filter ── --}}
    <div class="mt-6 flex flex-wrap items-center gap-2">
        <button type="button" wire:click="pilihKategori('Semua')"
            class="rounded-full px-4 py-2 text-xs font-semibold transition-all
                   {{ $kategoriDipilih === 'Semua' ? 'pill-active' : 'pill-inactive' }}">
            Semua
        </button>
        @foreach ($kategoriList as $kategori)
            <div class="flex overflow-hidden rounded-full {{ $kategoriDipilih === $kategori ? 'ring-2 ring-blue-400' : 'ring-1 ring-slate-200' }} bg-white transition-all">
                <button type="button" wire:click="pilihKategori('{{ $kategori }}')"
                    class="px-4 py-2 text-xs font-semibold transition-colors
                           {{ $kategoriDipilih === $kategori ? 'bg-(--accent) text-white' : 'text-slate-700 hover:text-(--accent)' }}">
                    {{ $kategori }}
                </button>
                <a href="{{ route('dokumen.pdf', ['kategori' => $kategoriSlugMap[$kategori] ?? Illuminate\Support\Str::slug($kategori)]) }}"
                    class="flex items-center border-l border-slate-200 px-3 text-[11px] font-semibold text-slate-400 transition-colors hover:bg-slate-50 hover:text-red-600"
                    title="Export PDF kategori {{ $kategori }}">
                    PDF
                </a>
            </div>
        @endforeach
    </div>

    {{-- Loading state --}}
    <div wire:loading wire:target="pilihKategori" class="mt-8 space-y-3">
        @for ($i = 0; $i < 3; $i++)
            <div class="h-20 animate-pulse rounded-xl bg-slate-100"></div>
        @endfor
    </div>

    {{-- ── Document list ── --}}
    <div wire:loading.remove wire:target="pilihKategori" class="mt-8 space-y-8">
        @foreach ($documents->groupBy(fn($doc) => $doc->category ?: 'Dokumen Pendukung') as $category => $items)
            <div>
                {{-- Category header --}}
                <div class="mb-4 flex flex-wrap items-center gap-3">
                    <h3 class="text-base font-bold text-slate-800">{{ $category }}</h3>
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-500">
                        {{ $items->count() }} dokumen
                    </span>
                    <div class="ml-auto flex items-center gap-3">
                        <a wire:navigate
                            href="{{ route('dokumen.category', ['kategori' => $kategoriSlugMap[$category] ?? Illuminate\Support\Str::slug($category)]) }}"
                            class="text-xs font-semibold text-(--accent) hover:underline transition-colors">
                            Lihat kategori →
                        </a>
                        <a href="{{ route('dokumen.pdf', ['kategori' => $kategoriSlugMap[$category] ?? Illuminate\Support\Str::slug($category)]) }}"
                            class="text-xs font-semibold text-slate-400 hover:text-red-600 transition-colors">
                            Export PDF
                        </a>
                    </div>
                </div>

                {{-- Document cards --}}
                <div class="space-y-3">
                    @foreach ($items as $document)
                        <article class="flex items-center gap-4 rounded-xl border border-(--line) bg-white p-5 shadow-sm transition-all hover:border-blue-200 hover:shadow-md">
                            <div class="flex min-w-0 flex-1 items-start gap-4">
                                {{-- File icon --}}
                                <span class="mt-0.5 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-500">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 3v6h6"/>
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <h4 class="font-semibold text-slate-800 leading-snug">{{ $document->title }}</h4>
                                    @if ($document->description)
                                        <p class="mt-0.5 text-sm text-(--muted) leading-relaxed">{{ $document->description }}</p>
                                    @endif
                                    @if ($document->file_name)
                                        <p class="mt-1 text-[11px] font-mono text-slate-400">{{ $document->file_name }}</p>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('dokumen.download', $document) }}"
                                class="btn-primary ml-auto flex-shrink-0 !py-2.5 !px-5 !text-xs">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Unduh
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if ($documents->isEmpty())
            <div class="flex flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 py-16 text-center">
                <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-medium text-slate-400">Belum ada dokumen pada kategori ini.</p>
            </div>
        @endif
    </div>
    {{-- /wire:loading.remove --}}

</section>
