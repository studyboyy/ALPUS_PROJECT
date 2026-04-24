<div class="space-y-6">
    <section class="section-box rounded-2xl p-6 md:p-8">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <a wire:navigate.hover href="{{ route('profil') }}"
                    class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 transition hover:text-slate-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Profil
                </a>
                <h1 class="display-font mt-2 text-4xl">{{ $section['title'] ?? 'Profil' }}</h1>
            </div>
        </div>
    </section>

    @if ($section)
        <section class="section-box rounded-2xl p-6 md:p-8">
            <div class="prose prose-sm max-w-none text-slate-700">
                {!! nl2br(e($section['full_content'] ?? $section['summary'])) !!}
            </div>
        </section>
    @endif

    <section class="section-box rounded-2xl p-6 md:p-8">
        <h3 class="text-lg font-bold">Kembali</h3>
        <p class="mt-2 text-sm text-(--muted)">Lihat section profil lainnya atau kembali ke halaman profil utama.</p>
        <div class="mt-4 flex flex-wrap gap-3">
            <a wire:navigate.hover href="{{ route('profil') }}"
                class="rounded-full bg-indigo-50 px-5 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">Lihat
                Semua Profil</a>
        </div>
    </section>
</div>
