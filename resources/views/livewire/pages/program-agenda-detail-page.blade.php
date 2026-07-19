<div class="mx-auto max-w-3xl space-y-6">

    {{-- ── Back nav + title ── --}}
    <div class="section-box rounded-2xl p-6 md:p-8">
        <a wire:navigate href="{{ route('home') }}"
            class="inline-flex items-center gap-2 rounded-full border border-(--line) bg-white px-3 py-1.5 text-xs font-semibold text-slate-500 shadow-sm transition hover:border-blue-300 hover:text-(--accent)">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Beranda
        </a>

        @if ($item)
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-indigo-700">
                    {{ $item['type'] }}
                </span>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                    {{ $item['year'] }}
                </span>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item['status_class'] }}">
                    {{ $item['status_label'] }}
                </span>
            </div>
        @endif

        <h1 class="display-font mt-4 text-4xl leading-tight text-slate-900">
            {{ $item['title'] ?? 'Detail Program dan Agenda' }}
        </h1>
    </div>

    {{-- ── Description ── --}}
    @if ($item && !empty($item['description']))
        <div class="section-box rounded-2xl p-6 md:p-8">
            <div class="flex items-center gap-2 border-b border-(--line) pb-4 mb-5">
                <svg class="h-4 w-4 text-(--muted)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h2 class="text-sm font-bold text-slate-700">Deskripsi</h2>
            </div>
            <p class="text-sm leading-relaxed text-slate-700 whitespace-pre-line">{!! nl2br(e($item['description'])) !!}</p>
        </div>
    @endif

    {{-- ── Footer nav ── --}}
    <div class="flex items-center justify-between gap-4 rounded-2xl border border-(--line) bg-white p-4 shadow-sm">
        <a wire:navigate href="{{ route('home') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-(--accent) transition hover:underline">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Beranda
        </a>
        <a wire:navigate href="{{ route('laporan') }}"
            class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700">
            Laporan Tahunan
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

</div>
