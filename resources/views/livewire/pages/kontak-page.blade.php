<section class="section-box rounded-2xl p-6 md:p-8">
    <div class="grid gap-6 lg:grid-cols-2">
        <div>
            <h2 class="display-font text-4xl leading-tight">Kontak dan Umpan Balik</h2>
            <p class="mt-3 text-sm text-(--muted)">Silakan kirim umpan balik untuk peningkatan layanan akademik dan tata
                kelola Program Studi.</p>

            <div class="mt-5 space-y-3 text-sm text-(--muted)">
                <div class="rounded-xl border border-(--line) bg-slate-50 p-3"><strong>Email:</strong>
                    {{ $homeContent['contact_email'] }}</div>
                <div class="rounded-xl border border-(--line) bg-slate-50 p-3"><strong>Telepon:</strong>
                    {{ $homeContent['contact_phone'] }}</div>
                <div class="rounded-xl border border-(--line) bg-slate-50 p-3"><strong>WhatsApp:</strong>
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $homeContent['contact_whatsapp']) }}"
                        target="_blank" rel="noreferrer"
                        class="text-(--accent) hover:underline">{{ $homeContent['contact_whatsapp'] }}</a>
                </div>
                <div class="rounded-xl border border-(--line) bg-slate-50 p-3"><strong>Alamat:</strong>
                    {{ $homeContent['contact_address'] }}</div>
                <div class="rounded-xl border border-(--line) bg-slate-50 p-3"><strong>Media Sosial:</strong>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($homeContent['contact_social_links'] as $social)
                            @if (!empty($social['url']))
                                <a href="{{ $social['url'] }}" target="_blank" rel="noreferrer"
                                    class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-(--accent) ring-1 ring-(--line)">{{ $social['label'] }}</a>
                            @else
                                <span
                                    class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-(--accent) ring-1 ring-(--line)">{{ $social['label'] }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-5 overflow-hidden rounded-xl border border-(--line)">
                <iframe title="Lokasi Prodi" class="h-56 w-full" src="{{ $homeContent['contact_map_embed_url'] }}"
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

        <form wire:submit="kirimUmpanBalik" class="grid gap-3 rounded-2xl border border-(--line) bg-slate-50 p-5">
            @if (session()->has('contact_status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('contact_status') }}
                </div>
            @endif
            <input wire:model.defer="feedbackName" type="text" placeholder="Nama"
                class="rounded-xl border border-(--line) bg-white px-4 py-3 text-sm outline-none focus:border-(--accent)">
            <input wire:model.defer="feedbackEmail" type="email" placeholder="Email"
                class="rounded-xl border border-(--line) bg-white px-4 py-3 text-sm outline-none focus:border-(--accent)">
            <input wire:model.defer="feedbackSubject" type="text" placeholder="Subjek"
                class="rounded-xl border border-(--line) bg-white px-4 py-3 text-sm outline-none focus:border-(--accent)">
            <textarea wire:model.defer="feedbackMessage" rows="4" placeholder="Pesan / Saran"
                class="rounded-xl border border-(--line) bg-white px-4 py-3 text-sm outline-none focus:border-(--accent)"></textarea>
            <button type="submit" class="rounded-full bg-(--accent) px-5 py-3 text-sm font-semibold text-white">Kirim
                Umpan Balik</button>
        </form>
    </div>
</section>
