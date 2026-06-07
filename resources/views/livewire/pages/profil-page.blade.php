<div class="space-y-6">

    {{-- ── Hero header ── --}}
    <section class="section-box overflow-hidden rounded-2xl">
        <div class="relative bg-gradient-to-br from-slate-800 to-slate-900 px-6 py-10 md:px-10 md:py-12">
            {{-- decorative blobs --}}
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-12 left-8 h-48 w-48 rounded-full bg-teal-500/10 blur-3xl"></div>
            <div class="relative">
                <p class="text-xs font-semibold uppercase tracking-widest text-teal-400">Program Studi</p>
                <h2 class="display-font mt-2 text-4xl leading-tight text-white md:text-5xl">Profil</h2>
                <p class="mt-3 max-w-xl text-sm leading-relaxed text-slate-300">
                    Identitas program studi meliputi sejarah, visi misi, struktur organisasi, sumber daya manusia, serta capaian strategis yang mendukung akreditasi dan daya saing lulusan.
                </p>
            </div>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- ── Profile sections grid ── --}}
        <div class="lg:col-span-2">
            <div class="grid gap-4 sm:grid-cols-2">
                @if (!empty($profileSections))
                    @foreach ($profileSections as $section)
                        @php $colors = \App\Models\ProfileSection::getColorClasses($section['color_class'] ?? 'blue'); @endphp
                        <a wire:navigate.hover href="{{ route('profil.detail', ['slug' => $section['slug']]) }}"
                            class="group flex flex-col rounded-2xl border border-(--line) bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-blue-200">
                            {{-- color bar --}}
                            <div class="mb-4 h-1 w-10 rounded-full {{ $colors['top-border'] ?? 'bg-blue-500' }}"></div>
                            <h3 class="font-bold text-slate-800 leading-snug group-hover:text-(--accent) transition-colors">
                                {{ $section['title'] }}
                            </h3>
                            <p class="mt-2 flex-1 text-sm leading-relaxed text-(--muted)">
                                {{ Str::limit($section['summary'], 80, '…') }}
                            </p>
                            <span class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-(--accent)">
                                Lihat detail
                                <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </a>
                    @endforeach
                @else
                    <div class="flex flex-col items-center gap-3 rounded-2xl border-2 border-dashed border-slate-200 py-12 text-center sm:col-span-2">
                        <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium text-slate-400">Data profil sedang dimuat…</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Quick highlights sidebar ── --}}
        <div class="space-y-4">
            <div class="section-box rounded-2xl p-5">
                <p class="text-[11px] font-bold uppercase tracking-widest text-(--olive)">Highlight Cepat</p>
                <ul class="mt-4 space-y-3">
                    @foreach ($highlightItems as $item)
                        <li class="flex items-start gap-3 rounded-xl border border-(--line) bg-slate-50 px-4 py-3">
                            <span class="mt-0.5 h-2 w-2 flex-shrink-0 rounded-full bg-(--accent)"></span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $item['label'] }}</p>
                                <p class="mt-0.5 text-sm font-bold text-slate-800">{{ $item['value'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- CTA --}}
            <a wire:navigate.hover href="{{ route('laporan') }}"
                class="flex items-center gap-3 rounded-2xl border border-blue-100 bg-blue-50 p-5 transition-all hover:bg-blue-100">
                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-blue-800">Laporan Tahunan</p>
                    <p class="text-xs text-blue-600">Lihat capaian & indikator</p>
                </div>
            </a>
        </div>

    </div>
</div>
