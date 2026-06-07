<section class="section-box rounded-2xl p-6 md:p-10">

    {{-- ── Header ── --}}
    <div class="mb-8">
        <h2 class="display-font text-4xl leading-tight">Kontak &amp; Umpan Balik</h2>
        <p class="mt-2 max-w-xl text-sm leading-relaxed text-(--muted)">
            Kirim saran, pertanyaan, atau masukan untuk peningkatan layanan akademik dan tata kelola Program Studi.
        </p>
    </div>

    <div class="grid gap-8 lg:grid-cols-5">

        {{-- ── Left: Contact info + map ── --}}
        <div class="space-y-4 lg:col-span-2">

            {{-- Contact cards --}}
            @php
                $contacts = [
                    ['icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label'=>'Email', 'value'=>$homeContent['contact_email'], 'href'=>'mailto:'.$homeContent['contact_email']],
                    ['icon'=>'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label'=>'Telepon', 'value'=>$homeContent['contact_phone'], 'href'=>'tel:'.$homeContent['contact_phone']],
                    ['icon'=>'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label'=>'Alamat', 'value'=>$homeContent['contact_address'], 'href'=>null],
                ];
            @endphp

            @foreach ($contacts as $c)
                <div class="flex items-start gap-3.5 rounded-xl border border-(--line) bg-white p-4 shadow-sm">
                    <span class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $c['icon'] }}"/>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $c['label'] }}</p>
                        @if ($c['href'])
                            <a href="{{ $c['href'] }}" class="mt-0.5 block text-sm font-semibold text-(--ink) hover:text-(--accent) transition-colors break-all">{{ $c['value'] }}</a>
                        @else
                            <p class="mt-0.5 text-sm font-medium text-slate-700">{{ $c['value'] }}</p>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- WhatsApp --}}
            @if (!empty($homeContent['contact_whatsapp']))
                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $homeContent['contact_whatsapp']) }}"
                    target="_blank" rel="noreferrer"
                    class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 transition-all hover:bg-emerald-100">
                    <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white">
                        <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.553 4.116 1.52 5.843L0 24l6.345-1.495A11.941 11.941 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.652-.518-5.163-1.42l-.37-.22-3.767.887.903-3.674-.24-.38A9.945 9.945 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700">WhatsApp</p>
                        <p class="text-sm font-semibold text-emerald-800">{{ $homeContent['contact_whatsapp'] }}</p>
                    </div>
                </a>
            @endif

            {{-- Social links --}}
            @if (!empty($homeContent['contact_social_links']))
                <div class="rounded-xl border border-(--line) bg-white p-4 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-3">Media Sosial</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($homeContent['contact_social_links'] as $social)
                            @if (!empty($social['url']))
                                <a href="{{ $social['url'] }}" target="_blank" rel="noreferrer"
                                    class="rounded-full bg-slate-100 px-3.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">
                                    {{ $social['label'] }}
                                </a>
                            @else
                                <span class="rounded-full bg-slate-100 px-3.5 py-1.5 text-xs font-semibold text-slate-500">{{ $social['label'] }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Map --}}
            @if (!empty($homeContent['contact_map_embed_url']))
                <div class="overflow-hidden rounded-xl border border-(--line) shadow-sm">
                    <iframe title="Lokasi Prodi" class="h-52 w-full"
                        src="{{ $homeContent['contact_map_embed_url'] }}"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            @endif
        </div>

        {{-- ── Right: Feedback form ── --}}
        <div class="lg:col-span-3">
            <div class="rounded-2xl border border-(--line) bg-white p-6 shadow-sm md:p-8">
                <h3 class="text-lg font-bold text-slate-800">Kirim Umpan Balik</h3>
                <p class="mt-1 text-sm text-(--muted)">Semua kolom wajib diisi. Kami akan merespons secepatnya.</p>

                @if (session()->has('contact_status'))
                    <div class="mt-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5">
                        <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-medium text-emerald-800">{{ session('contact_status') }}</p>
                    </div>
                @endif

                <form wire:submit="kirimUmpanBalik" class="mt-6 space-y-5">

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700" for="feedback-name">Nama Lengkap</label>
                            <input id="feedback-name" wire:model.defer="feedbackName" type="text"
                                placeholder="Masukkan nama Anda"
                                class="form-input">
                            @error('feedbackName')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700" for="feedback-email">Alamat Email</label>
                            <input id="feedback-email" wire:model.defer="feedbackEmail" type="email"
                                placeholder="email@contoh.com"
                                class="form-input">
                            @error('feedbackEmail')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700" for="feedback-subject">Subjek</label>
                        <input id="feedback-subject" wire:model.defer="feedbackSubject" type="text"
                            placeholder="Topik umpan balik Anda"
                            class="form-input">
                        @error('feedbackSubject')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700" for="feedback-message">Pesan / Saran</label>
                        <textarea id="feedback-message" wire:model.defer="feedbackMessage" rows="5"
                            placeholder="Tuliskan pesan atau saran Anda di sini..."
                            class="form-input resize-none"></textarea>
                        @error('feedbackMessage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn-primary w-full">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim Umpan Balik
                    </button>
                </form>
            </div>
        </div>

    </div>
</section>
