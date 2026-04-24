<div class="grid gap-4 lg:grid-cols-3">
    <article class="section-box rounded-2xl p-6 lg:col-span-2">
        <h2 class="display-font text-4xl leading-tight">Profil Program Studi</h2>
        <p class="mt-3 text-sm text-(--muted)">Halaman ini menampilkan identitas program studi: sejarah, visi misi,
            struktur organisasi, SDM, serta capaian strategis yang mendukung akreditasi dan daya saing lulusan.</p>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            @if (!empty($profileSections))
                @foreach ($profileSections as $section)
                    @php
                        $colors = \App\Models\ProfileSection::getColorClasses($section['color_class'] ?? 'blue');
                    @endphp
                    <a wire:navigate.hover href="{{ route('profil.detail', ['slug' => $section['slug']]) }}"
                        class="group rounded-xl border-l-4 {{ $colors['top-border'] }} border-slate-200 bg-slate-50 p-4 transition-all hover:-translate-y-0.5 hover:shadow-md">
                        <h3 class="font-semibold group-hover:{{ $colors['text'] }}">{{ $section['title'] }}</h3>
                        <p class="mt-2 text-sm text-(--muted) group-hover:text-slate-700">
                            {{ Str::limit($section['summary'], 50, '...') }}</p>
                        <span
                            class="mt-3 inline-block rounded-full {{ $colors['bg-light'] }} px-3 py-1 text-xs font-semibold {{ $colors['text'] }}">
                            Lihat Detail →
                        </span>
                    </a>
                @endforeach
            @else
                <div class="rounded-xl border border-(--line) bg-slate-50 p-4 sm:col-span-2">
                    <p class="text-sm text-(--muted)">Data profil sedang dimuat...</p>
                </div>
            @endif
        </div>
    </article>

    <article class="section-box rounded-2xl p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-(--olive)">Highlight Cepat</p>
        <ul class="mt-4 space-y-4 text-sm">
            @foreach ($highlightItems as $item)
                <li class="rounded-xl border border-(--line) bg-slate-50 p-3"><strong>{{ $item['label'] }}:</strong>
                    {{ $item['value'] }}</li>
            @endforeach
        </ul>
    </article>
</div>
