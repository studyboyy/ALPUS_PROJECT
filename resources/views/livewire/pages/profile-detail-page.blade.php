<div class="mx-auto max-w-3xl space-y-6">

    {{-- ── Back nav + title ── --}}
    <div class="section-box rounded-2xl p-6 md:p-8">
        <a wire:navigate href="{{ route('profil') }}"
            class="inline-flex items-center gap-2 rounded-full border border-(--line) bg-white px-3 py-1.5 text-xs font-semibold text-slate-500 shadow-sm transition hover:border-blue-300 hover:text-(--accent)">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Profil
        </a>
        <h1 class="display-font mt-4 text-4xl leading-tight text-slate-900">{{ $section['title'] ?? 'Profil' }}</h1>
        @if (!empty($section['summary']))
            <p class="mt-3 text-sm leading-relaxed text-(--muted)">{{ $section['summary'] }}</p>
        @endif
    </div>

    {{-- ── Content ── --}}
    @if ($section)
        <div class="section-box rounded-2xl p-6 md:p-8">
            <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-700">
                {!! nl2br(e($section['full_content'] ?? $section['summary'])) !!}
            </div>
        </div>
    @endif

    {{-- ── Footer nav ── --}}
    <div class="flex items-center justify-between gap-4 rounded-2xl border border-(--line) bg-white p-4 shadow-sm">
        <a wire:navigate href="{{ route('profil') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-(--accent) transition hover:underline">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Semua Profil
        </a>
        <a wire:navigate href="{{ route('laporan') }}"
            class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700">
            Lihat Laporan Tahunan
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>
