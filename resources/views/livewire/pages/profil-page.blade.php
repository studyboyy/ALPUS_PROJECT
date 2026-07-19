<div class="space-y-6">

    {{-- ── Page header ── --}}
    <div class="section-box overflow-hidden rounded-2xl">
        <div class="relative overflow-hidden px-6 py-10 md:px-10 md:py-12" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 45%, #0c2a4a 100%);">
            <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full opacity-15" style="background: radial-gradient(circle, #3b82f6, transparent 70%);"></div>
            <div class="pointer-events-none absolute -bottom-16 left-4 h-56 w-56 rounded-full opacity-10" style="background: radial-gradient(circle, #60a5fa, transparent 70%);"></div>
            <div class="relative">
                <p class="inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] text-sky-200 backdrop-blur-sm">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
                    Program Studi
                </p>
                <h2 class="display-font mt-3 text-4xl font-bold leading-tight text-white md:text-5xl">Profil</h2>
                <p class="mt-3 max-w-xl text-sm leading-relaxed text-slate-300">
                    Identitas program studi meliputi sejarah, visi misi, struktur organisasi, sumber daya manusia, serta capaian strategis yang mendukung akreditasi dan daya saing lulusan.
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a wire:navigate href="{{ route('laporan') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-bold text-blue-900 shadow transition hover:-translate-y-0.5">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Lihat Laporan
                    </a>
                    <a wire:navigate href="{{ route('statistik') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-2 text-xs font-semibold text-white backdrop-blur-sm transition hover:bg-white/20">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"/></svg>
                        Data Statistik
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- ── Profile sections grid ── --}}
        <div class="lg:col-span-2">
            {{-- Loading skeleton --}}
            <div wire:loading wire:target="pilihTahun" class="grid gap-4 sm:grid-cols-2">
                @for ($i = 0; $i < 4; $i++)
                    <div class="h-40 animate-pulse rounded-2xl bg-slate-100"></div>
                @endfor
            </div>

            <div wire:loading.remove wire:target="pilihTahun" class="grid gap-4 sm:grid-cols-2">
                @if (!empty($profileSections))
                    @foreach ($profileSections as $section)
                        @php $colors = \App\Models\ProfileSection::getColorClasses($section['color_class'] ?? 'blue'); @endphp
                        <a wire:navigate href="{{ route('profil.detail', ['slug' => $section['slug']]) }}"
                            class="group flex flex-col rounded-2xl border border-(--line) bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg">
                            <div class="mb-4 h-1 w-10 rounded-full {{ $colors['top-border'] ?? 'bg-blue-500' }}"></div>
                            <h3 class="font-bold text-slate-800 leading-snug transition-colors group-hover:text-(--accent)">
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
                        <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Belum ada seksi profil</p>
                            <p class="mt-0.5 text-xs text-slate-400">Tambahkan melalui panel admin.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Sidebar ── --}}
        <div class="space-y-4">
            <div class="section-box rounded-2xl p-5">
                <p class="text-[11px] font-bold uppercase tracking-widest text-(--olive)">Highlight Cepat</p>
                <ul class="mt-4 space-y-2.5">
                    @foreach ($highlightItems as $item)
                        <li class="flex items-start gap-3 rounded-xl border border-(--line) bg-slate-50 px-4 py-3">
                            <span class="mt-1 h-2 w-2 flex-shrink-0 rounded-full bg-(--accent)"></span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $item['label'] }}</p>
                                <p class="mt-0.5 text-sm font-bold text-slate-800">{{ $item['value'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <a wire:navigate href="{{ route('statistik') }}"
                class="flex items-center gap-3 rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-indigo-50 p-5 transition-all hover:shadow-md">
                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-blue-800">Data Statistik</p>
                    <p class="text-xs text-blue-600">Indikator kinerja tahunan</p>
                </div>
            </a>

            <a wire:navigate href="{{ route('laporan') }}"
                class="flex items-center gap-3 rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-violet-50 p-5 transition-all hover:shadow-md">
                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-indigo-800">Laporan Tahunan</p>
                    <p class="text-xs text-indigo-600">Capaian &amp; indikator kinerja</p>
                </div>
            </a>
        </div>

    </div>
</div>
