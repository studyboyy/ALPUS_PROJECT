<div class="space-y-6">
    <section class="section-box rounded-2xl p-6 md:p-8">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <a wire:navigate.hover href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 transition hover:text-slate-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Beranda
                </a>
                <h1 class="display-font mt-2 text-4xl">{{ $item['title'] ?? 'Detail Program dan Agenda' }}</h1>
            </div>
        </div>

        @if ($item)
            <div class="flex flex-wrap items-center gap-2">
                <span
                    class="inline-block rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-indigo-700">
                    {{ $item['type'] }} {{ $item['year'] }}
                </span>
                <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $item['status_class'] }}">
                    {{ $item['status_label'] }}
                </span>
            </div>
            <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5">
                <p class="text-sm leading-relaxed text-slate-700">{!! nl2br(e($item['description'])) !!}</p>
            </div>
        @endif
    </section>
</div>
