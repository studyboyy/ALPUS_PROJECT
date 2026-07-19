<div
    x-data="{ tab: window.location.hash ? window.location.hash.replace('#','') : 'header' }"
    class="space-y-5">

    {{-- ── Page header ── --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-zinc-800">Konten Beranda</h2>
            <p class="mt-0.5 text-sm text-zinc-500">Kelola semua konten halaman utama portal.</p>
        </div>
        <span class="rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">CMS</span>
    </div>

    {{-- ── Tab nav ── --}}
    <div class="section-box flex flex-wrap gap-1 rounded-2xl p-2">
        @php
            $tabs = [
                ['id'=>'header',    'label'=>'Header',    'icon'=>'M4 6h16M4 12h8m-8 6h16'],
                ['id'=>'hero',      'label'=>'Hero',      'icon'=>'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14'],
                ['id'=>'kaprodi',   'label'=>'Kaprodi',   'icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['id'=>'kontak',    'label'=>'Kontak',    'icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['id'=>'highlight', 'label'=>'Highlight', 'icon'=>'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                ['id'=>'galeri',    'label'=>'Galeri',    'icon'=>'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ];
        @endphp
        @foreach ($tabs as $t)
            <button type="button" @click="tab = '{{ $t['id'] }}'"
                :class="tab === '{{ $t['id'] }}' ? 'bg-indigo-600 text-white shadow-sm' : 'text-zinc-500 hover:text-zinc-800 hover:bg-zinc-100'"
                class="flex items-center gap-2 rounded-xl px-3.5 py-2 text-xs font-semibold transition-all duration-150">
                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $t['icon'] }}"/>
                </svg>
                {{ $t['label'] }}
            </button>
        @endforeach
    </div>

    {{-- ════════════════════════════════
         TAB: HEADER
    ════════════════════════════════ --}}
    <div x-show="tab === 'header'" x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="section-box rounded-2xl p-6">
            <h3 class="text-sm font-bold text-zinc-800">Header Portal</h3>
            <p class="mt-1 text-xs text-zinc-500">Teks label di atas logo, judul besar, dan file logo.</p>

            <form wire:submit="simpanHeaderPortal"
                x-data="headerLogoCropper({ initialLogo: @js($headerLogoUrl) })"
                class="mt-5 space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Teks Atas Logo</label>
                        <input wire:model.defer="headerLogoLabel" type="text" placeholder="Program Studi"
                            class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Judul Header (Nama Portal)</label>
                        <input wire:model.defer="headerTitleText" type="text" placeholder="Laporan Tahunan [Nama Prodi]"
                            class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Upload Logo</label>
                        <input type="file" accept="image/*" @change="selectFile($event)"
                            class="w-full rounded-xl border border-dashed border-indigo-300 bg-indigo-50/40 px-3.5 py-2.5 text-sm text-indigo-700" />
                        <p class="mt-1.5 text-[11px] text-zinc-400">Logo akan dicrop persegi 1:1 sebelum disimpan.</p>
                        @error('croppedHeaderLogoDataUrl')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Preview --}}
                <div class="overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50">
                    <div class="flex items-center justify-between gap-4 border-b border-zinc-200 bg-white px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <img x-show="previewLogoUrl" :src="previewLogoUrl"
                                class="h-10 w-10 rounded-lg border border-zinc-200 object-cover shadow-sm" alt="Logo"/>
                            <div x-show="!previewLogoUrl" class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 to-violet-600 grid place-items-center text-white text-xs font-bold shadow-sm">
                                PS
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-teal-700">{{ $headerLogoLabel }}</p>
                                <p class="text-sm font-bold text-zinc-800 leading-tight">{{ $headerTitleText }}</p>
                            </div>
                        </div>
                        <div class="hidden items-center gap-1.5 lg:flex">
                            @foreach(['Beranda','Profil','Laporan','Statistik'] as $nav)
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $loop->first ? 'bg-indigo-100 text-indigo-700' : 'text-zinc-500' }}">{{ $nav }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div x-show="open" x-cloak class="fixed inset-0 z-[9999] grid place-items-center bg-slate-950/75 p-4">
                    <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-2xl">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-extrabold text-zinc-900">Crop Logo</h4>
                                <p class="mt-0.5 text-xs text-zinc-500">Geser gambar dan atur zoom sampai pas di kotak.</p>
                            </div>
                            <button type="button" @click="cancel()"
                                class="rounded-lg border border-zinc-200 bg-white px-2.5 py-1.5 text-xs font-bold text-zinc-500 hover:bg-zinc-50">Tutup</button>
                        </div>

                        <div class="mx-auto h-72 w-72 overflow-hidden rounded-2xl border-2 border-indigo-500 bg-zinc-100 shadow-inner"
                            x-ref="cropBox"
                            @mousedown.prevent="startDrag($event)"
                            @mousemove.prevent="drag($event)"
                            @mouseup="stopDrag()"
                            @mouseleave="stopDrag()"
                            @touchstart.prevent="startDrag($event.touches[0])"
                            @touchmove.prevent="drag($event.touches[0])"
                            @touchend="stopDrag()">
                            <img x-ref="cropImage" :src="sourceUrl" alt="Crop logo"
                                class="h-full w-full select-none object-contain"
                                :style="`transform: translate(${offsetX}px, ${offsetY}px) scale(${zoom}); transform-origin: center;`"
                                draggable="false"
                                @load="imageLoaded()">
                        </div>

                        <label class="mt-4 block text-xs font-semibold text-zinc-600">Zoom</label>
                        <input type="range" :min="minZoom" max="3" step="0.01" x-model.number="zoom" @input="clampOffset()"
                            class="mt-2 w-full accent-indigo-600">

                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" @click="cancel()"
                                class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-bold text-zinc-600 hover:bg-zinc-50">Batal</button>
                            <button type="button" @click="applyCrop()"
                                class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-700">Pakai Logo</button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan Header
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════
         TAB: HERO
    ════════════════════════════════ --}}
    <div x-show="tab === 'hero'" x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="section-box rounded-2xl p-6">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div>
                    <h3 class="text-sm font-bold text-zinc-800">Hero Carousel</h3>
                    <p class="mt-0.5 text-xs text-zinc-500">Gambar latar hero di halaman beranda (carousel).</p>
                </div>
                <button type="button" wire:click="tambahHeroItem"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-300 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 shadow-sm hover:bg-zinc-50">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Slide
                </button>
            </div>

            <form wire:submit="simpanHero" class="space-y-3">
                @foreach ($heroItems as $index => $item)
                    @php $heroUpload = $heroImageFiles[$index] ?? null; @endphp
                    <div class="flex items-center gap-4 rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                        <img src="{{ $heroUpload ? $heroUpload->temporaryUrl() : data_get($item, 'image_url') }}"
                            alt="Slide {{ $index + 1 }}"
                            class="h-20 w-36 flex-shrink-0 rounded-lg border border-zinc-200 object-cover shadow-sm"/>
                        <div class="min-w-0 flex-1 space-y-2">
                            <p class="text-xs font-semibold text-zinc-500">Slide {{ $index + 1 }}</p>
                            <input wire:model="heroImageFiles.{{ $index }}" type="file" accept="image/*"
                                class="w-full rounded-lg border border-dashed border-indigo-300 bg-indigo-50/40 px-3 py-2 text-xs text-indigo-700"/>
                        </div>
                        <button type="button" wire:click="hapusHeroItem({{ $index }})"
                            class="flex-shrink-0 rounded-lg bg-rose-50 p-2 text-rose-600 hover:bg-rose-100">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                @endforeach

                <div class="flex justify-end pt-1">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan Hero
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════
         TAB: KAPRODI
    ════════════════════════════════ --}}
    <div x-show="tab === 'kaprodi'" x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="section-box rounded-2xl p-6">
            <h3 class="text-sm font-bold text-zinc-800 mb-5">Profil Kepala Prodi</h3>

            <form wire:submit="simpanKaprodi" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Nama Lengkap</label>
                        <input wire:model.defer="kaprodiName" type="text"
                            class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Jabatan</label>
                        <input wire:model.defer="kaprodiTitle" type="text"
                            class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Kutipan / Pesan Kepala Prodi</label>
                        <textarea wire:model.defer="kaprodiQuote" rows="4"
                            class="w-full resize-none rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Upload Foto</label>
                        <input wire:model="kaprodiPhotoFile" type="file" accept="image/*"
                            class="w-full rounded-xl border border-dashed border-indigo-300 bg-indigo-50/40 px-3.5 py-2.5 text-sm text-indigo-700"/>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="flex items-center gap-4 rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                    <img src="{{ $kaprodiPhotoFile ? $kaprodiPhotoFile->temporaryUrl() : $kaprodiPhotoUrl }}"
                        class="h-14 w-14 rounded-full border-2 border-white object-cover shadow-sm flex-shrink-0" alt="Foto"/>
                    <div class="min-w-0">
                        <p class="font-bold text-zinc-800 leading-snug">{{ $kaprodiName }}</p>
                        <p class="text-xs text-zinc-500">{{ $kaprodiTitle }}</p>
                        <p class="mt-1.5 text-xs leading-relaxed text-zinc-500 italic line-clamp-2">"{{ $kaprodiQuote }}"</p>
                    </div>
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan Kaprodi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════
         TAB: KONTAK
    ════════════════════════════════ --}}
    <div x-show="tab === 'kontak'" x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="section-box rounded-2xl p-6">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div>
                    <h3 class="text-sm font-bold text-zinc-800">Kontak &amp; Peta</h3>
                    <p class="mt-0.5 text-xs text-zinc-500">Email, telepon, WhatsApp, media sosial, dan embed peta.</p>
                </div>
                <button type="button" wire:click="tambahSocialLink"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-300 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 shadow-sm hover:bg-zinc-50">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Sosial
                </button>
            </div>

            <form wire:submit="simpanKontak" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Email</label>
                        <input wire:model.defer="contactEmail" type="email"
                            class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Telepon</label>
                        <input wire:model.defer="contactPhone" type="text" placeholder="(021) 1234567"
                            class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">WhatsApp</label>
                        <input wire:model.defer="contactWhatsapp" type="text" placeholder="6281234567890"
                            class="w-full rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold text-zinc-600 mb-1.5">Alamat</label>
                        <textarea wire:model.defer="contactAddress" rows="2"
                            class="w-full resize-none rounded-xl border border-zinc-300 bg-white px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                    </div>
                </div>

                {{-- Social links --}}
                @if (count($contactSocialLinks))
                    <div class="space-y-2.5">
                        <p class="text-xs font-semibold text-zinc-600">Media Sosial</p>
                        @foreach ($contactSocialLinks as $index => $social)
                            <div wire:key="social-{{ $index }}"
                                class="grid items-end gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-3.5 md:grid-cols-[1fr_2fr_auto]">
                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">Platform</label>
                                    <input wire:model.defer="contactSocialLinks.{{ $index }}.label" type="text"
                                        placeholder="Instagram"
                                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100"/>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">URL</label>
                                    <input wire:model.defer="contactSocialLinks.{{ $index }}.url" type="url"
                                        placeholder="https://instagram.com/prodi"
                                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100"/>
                                </div>
                                <button type="button" wire:click="hapusSocialLink({{ $index }})"
                                    class="rounded-lg bg-rose-50 p-2 text-rose-600 hover:bg-rose-100">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-zinc-600 mb-1.5">
                        Lokasi Peta
                    </label>

                    {{-- wire:model handles saving; Alpine reads the DOM value just for preview --}}
                    <div x-data="{
                            embedUrl: '',
                            debounceTimer: null,

                            buildEmbed(val) {
                                val = (val || '').trim();
                                if (!val) { this.embedUrl = ''; return; }
                                if (val.includes('output=embed') || val.includes('/maps/embed')) {
                                    this.embedUrl = val; return;
                                }
                                if (val.startsWith('http')) {
                                    try {
                                        const u = new URL(val);
                                        const q = u.searchParams.get('q') || u.searchParams.get('query');
                                        this.embedUrl = q
                                            ? 'https://maps.google.com/maps?q=' + encodeURIComponent(q) + '&output=embed'
                                            : 'https://maps.google.com/maps?q=' + encodeURIComponent(val) + '&output=embed';
                                    } catch(e) {
                                        this.embedUrl = 'https://maps.google.com/maps?q=' + encodeURIComponent(val) + '&output=embed';
                                    }
                                    return;
                                }
                                this.embedUrl = 'https://maps.google.com/maps?q=' + encodeURIComponent(val) + '&output=embed';
                            },

                            onInput(e) {
                                clearTimeout(this.debounceTimer);
                                this.debounceTimer = setTimeout(() => this.buildEmbed(e.target.value), 700);
                            },

                            init() {
                                const el = this.$refs.mapInput;
                                if (el) this.buildEmbed(el.value);
                            }
                        }"
                        x-init="init()">

                        {{-- Input: wire:model.defer sends value to Livewire on save; Alpine reads DOM for preview --}}
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            <input
                                x-ref="mapInput"
                                wire:model.defer="contactMapQuery"
                                @input="onInput($event)"
                                type="text"
                                placeholder="Ketik nama kampus, koordinat, atau tempel link Google Maps…"
                                class="w-full rounded-xl border border-zinc-300 bg-white py-2.5 pl-9 pr-3.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"/>
                        </div>

                        <p class="mt-1.5 text-[11px] text-zinc-400 leading-relaxed">
                            Contoh: <code class="rounded bg-zinc-100 px-1 py-0.5 font-mono">STMIK JABAR</code>&nbsp;·&nbsp;
                            <code class="rounded bg-zinc-100 px-1 py-0.5 font-mono">-6.9377,107.6772</code>&nbsp;·&nbsp;
                            atau link <code class="rounded bg-zinc-100 px-1 py-0.5 font-mono">https://maps.app.goo.gl/…</code>
                        </p>

                        {{-- Live preview --}}
                        <div x-show="embedUrl !== ''" class="mt-3 overflow-hidden rounded-xl border border-zinc-200 shadow-sm" style="display:none">
                            <iframe :src="embedUrl" title="Preview Peta"
                                class="h-52 w-full border-0"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen></iframe>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan Kontak
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════
         TAB: HIGHLIGHT
    ════════════════════════════════ --}}
    <div x-show="tab === 'highlight'" x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="section-box rounded-2xl p-6">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div>
                    <h3 class="text-sm font-bold text-zinc-800">Highlight Cepat Beranda</h3>
                    <p class="mt-0.5 text-xs text-zinc-500">Kartu highlight yang muncul di bawah hero beranda.</p>
                </div>
                <button type="button" wire:click="tambahQuickHighlight"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-300 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 shadow-sm hover:bg-zinc-50">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah
                </button>
            </div>

            <form wire:submit="simpanQuickHighlights" class="space-y-3">
                @foreach ($quickHighlights as $index => $item)
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                        <div class="mb-3 flex items-center justify-between gap-2">
                            <span class="rounded-full bg-white px-2.5 py-0.5 text-xs font-semibold text-zinc-600 ring-1 ring-zinc-200">
                                #{{ $index + 1 }}
                            </span>
                            <div class="flex items-center gap-1.5">
                                <button type="button" wire:click="pindahQuickHighlightKeAtas({{ $index }})"
                                    class="rounded-lg border border-zinc-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-zinc-600 hover:bg-zinc-100" title="Naik">↑</button>
                                <button type="button" wire:click="pindahQuickHighlightKeBawah({{ $index }})"
                                    class="rounded-lg border border-zinc-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-zinc-600 hover:bg-zinc-100" title="Turun">↓</button>
                                <button type="button" wire:click="hapusQuickHighlight({{ $index }})"
                                    class="rounded-lg bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-100">Hapus</button>
                            </div>
                        </div>
                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-medium text-zinc-500 mb-1">Judul</label>
                                <input wire:model.defer="quickHighlights.{{ $index }}.title" type="text"
                                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100"/>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-zinc-500 mb-1">Label Tombol</label>
                                <input wire:model.defer="quickHighlights.{{ $index }}.link_label" type="text"
                                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100"/>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-zinc-500 mb-1">Deskripsi</label>
                                <textarea wire:model.defer="quickHighlights.{{ $index }}.description" rows="2"
                                    class="w-full resize-none rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-zinc-500 mb-1">Link</label>
                                <input wire:model.defer="quickHighlights.{{ $index }}.link" type="text"
                                    placeholder="/laporan atau #statistik-beranda"
                                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm font-mono outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100"/>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">Ikon</label>
                                    <select wire:model.defer="quickHighlights.{{ $index }}.icon_key"
                                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100">
                                        <option value="chart">Chart</option>
                                        <option value="document">Document</option>
                                        <option value="users">Users</option>
                                        <option value="award">Award</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">Warna</label>
                                    <select wire:model.defer="quickHighlights.{{ $index }}.color_key"
                                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100">
                                        <option value="blue">Biru</option>
                                        <option value="violet">Violet</option>
                                        <option value="emerald">Hijau</option>
                                        <option value="amber">Amber</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if (empty($quickHighlights))
                    <div class="flex flex-col items-center gap-2 rounded-xl border-2 border-dashed border-zinc-200 py-10 text-center">
                        <p class="text-sm font-medium text-zinc-400">Belum ada highlight.</p>
                    </div>
                @endif

                <div class="flex justify-end pt-1">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan Highlight
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════
         TAB: GALERI
    ════════════════════════════════ --}}
    <div x-show="tab === 'galeri'" x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="section-box rounded-2xl p-6">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-zinc-800">Foto Galeri</h3>
                    <p class="mt-0.5 text-xs text-zinc-500">{{ count($galleryItems) }} foto tersimpan.</p>
                </div>
                <button type="button" wire:click="tambahGalleryItem"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-300 bg-white px-3.5 py-2 text-xs font-semibold text-zinc-700 shadow-sm hover:bg-zinc-50">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Foto
                </button>
            </div>

            {{-- Category filter --}}
            @php
                $adminGalleryKategori = collect(['Prestasi Mahasiswa','Kegiatan Akademik','Kegiatan Mahasiswa','Pengabdian Masyarakat','Kerjasama & MoU'])
                    ->merge(collect($galleryItems)->pluck('category')->filter())->unique()->values();
            @endphp
            <div class="mb-4 flex flex-wrap gap-2">
                <button type="button" wire:click="pilihKategoriGaleri('Semua')"
                    class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all {{ $galeriKategoriDipilih === 'Semua' ? 'bg-indigo-600 text-white shadow-sm' : 'border border-zinc-300 bg-white text-zinc-600 hover:border-indigo-300 hover:text-indigo-700' }}">
                    Semua
                </button>
                @foreach ($adminGalleryKategori as $kat)
                    <button type="button" wire:click="pilihKategoriGaleri('{{ $kat }}')"
                        class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all {{ $galeriKategoriDipilih === $kat ? 'bg-indigo-600 text-white shadow-sm' : 'border border-zinc-300 bg-white text-zinc-600 hover:border-indigo-300 hover:text-indigo-700' }}">
                        {{ $kat }}
                    </button>
                @endforeach
            </div>

            <form wire:submit="simpanGaleri" class="space-y-3">
                @foreach ($galleryItems as $index => $item)
                    @continue($galeriKategoriDipilih !== 'Semua' && data_get($item,'category') !== $galeriKategoriDipilih)
                    @php $galleryUpload = $galleryImageFiles[$index] ?? null; @endphp
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4">
                        <div class="flex gap-4">
                            {{-- Thumbnail --}}
                            <img src="{{ $galleryUpload ? $galleryUpload->temporaryUrl() : data_get($item,'image_url') }}"
                                alt="Preview"
                                class="h-24 w-36 flex-shrink-0 rounded-lg border border-zinc-200 object-cover shadow-sm"/>
                            {{-- Fields --}}
                            <div class="min-w-0 flex-1 grid gap-3 md:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">Judul</label>
                                    <input wire:model.defer="galleryItems.{{ $index }}.title" type="text"
                                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100"/>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">Kategori</label>
                                    <select wire:model.defer="galleryItems.{{ $index }}.category"
                                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100">
                                        @foreach ($adminGalleryKategori as $ko)
                                            <option value="{{ $ko }}">{{ $ko }}</option>
                                        @endforeach
                                        <option value="__new__">+ Kategori Baru</option>
                                    </select>
                                    @if ((string) data_get($item,'category') === '__new__')
                                        <input wire:model.defer="galleryItems.{{ $index }}.custom_category" type="text"
                                            placeholder="Nama kategori baru"
                                            class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100"/>
                                        @error("galleryItems.$index.custom_category")
                                            <p class="mt-0.5 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    @endif
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">Deskripsi <span class="text-zinc-400">(tampil di modal)</span></label>
                                    <textarea wire:model.defer="galleryItems.{{ $index }}.description" rows="2"
                                        placeholder="Keterangan singkat tentang foto ini..."
                                        class="w-full resize-none rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-400 focus:ring-1 focus:ring-indigo-100"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-zinc-500 mb-1">Ganti Gambar</label>
                                    <input wire:model="galleryImageFiles.{{ $index }}" type="file" accept="image/*"
                                        class="w-full rounded-lg border border-dashed border-indigo-300 bg-indigo-50/40 px-3 py-2 text-xs text-indigo-700"/>
                                </div>
                            </div>
                            {{-- Delete --}}
                            <button type="button" wire:click="hapusGalleryItem({{ $index }})"
                                class="flex-shrink-0 self-start rounded-lg bg-rose-50 p-2 text-rose-600 hover:bg-rose-100">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                @endforeach

                @if (collect($galleryItems)->filter(fn($item) => $galeriKategoriDipilih === 'Semua' || data_get($item,'category') === $galeriKategoriDipilih)->isEmpty())
                    <div class="flex flex-col items-center gap-2 rounded-xl border-2 border-dashed border-zinc-200 py-10 text-center">
                        <p class="text-sm font-medium text-zinc-400">Belum ada foto di kategori ini.</p>
                    </div>
                @endif

                <div class="flex justify-end pt-1">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Publikasikan Galeri
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    window.headerLogoCropper = window.headerLogoCropper || function (config) {
        return {
            open: false,
            sourceUrl: '',
            previewLogoUrl: config.initialLogo || '',
            zoom: 1,
            minZoom: 1,
            offsetX: 0,
            offsetY: 0,
            dragging: false,
            lastX: 0,
            lastY: 0,
            naturalWidth: 0,
            naturalHeight: 0,

            selectFile(event) {
                const file = event.target.files && event.target.files[0];
                if (!file) return;

                this.sourceUrl = URL.createObjectURL(file);
                this.zoom = 1;
                this.minZoom = 1;
                this.offsetX = 0;
                this.offsetY = 0;
                this.open = true;
                event.target.value = '';
            },

            imageLoaded() {
                const img = this.$refs.cropImage;
                if (!img) return;

                this.naturalWidth = img.naturalWidth || 1;
                this.naturalHeight = img.naturalHeight || 1;

                const box = this.boxSize();
                const containScale = Math.min(box / this.naturalWidth, box / this.naturalHeight);
                const renderedWidth = this.naturalWidth * containScale;
                const renderedHeight = this.naturalHeight * containScale;
                this.minZoom = Math.max(1, box / renderedWidth, box / renderedHeight);
                this.zoom = this.minZoom;
                this.offsetX = 0;
                this.offsetY = 0;
                this.clampOffset();
            },

            startDrag(point) {
                this.dragging = true;
                this.lastX = point.clientX;
                this.lastY = point.clientY;
            },

            drag(point) {
                if (!this.dragging) return;
                this.offsetX += point.clientX - this.lastX;
                this.offsetY += point.clientY - this.lastY;
                this.lastX = point.clientX;
                this.lastY = point.clientY;
                this.clampOffset();
            },

            stopDrag() {
                this.dragging = false;
            },

            cancel() {
                this.open = false;
                if (this.sourceUrl) URL.revokeObjectURL(this.sourceUrl);
                this.sourceUrl = '';
            },

            applyCrop() {
                const img = new Image();
                img.onload = () => {
                    const box = this.boxSize();
                    const scale = this.imageScale();
                    const renderedWidth = this.naturalWidth * scale;
                    const renderedHeight = this.naturalHeight * scale;
                    const left = ((box - renderedWidth) / 2) + this.offsetX;
                    const top = ((box - renderedHeight) / 2) + this.offsetY;

                    const sourceX = Math.max(0, (0 - left) / scale);
                    const sourceY = Math.max(0, (0 - top) / scale);
                    const sourceSize = Math.min(
                        this.naturalWidth - sourceX,
                        this.naturalHeight - sourceY,
                        box / scale
                    );

                    const canvas = document.createElement('canvas');
                    canvas.width = 512;
                    canvas.height = 512;
                    const ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, 512, 512);
                    ctx.drawImage(img, sourceX, sourceY, sourceSize, sourceSize, 0, 0, 512, 512);

                    const dataUrl = canvas.toDataURL('image/png', 0.92);
                    this.previewLogoUrl = dataUrl;
                    this.$wire.set('croppedHeaderLogoDataUrl', dataUrl);
                    this.cancel();
                };
                img.src = this.sourceUrl;
            },

            boxSize() {
                return this.$refs.cropBox ? this.$refs.cropBox.clientWidth : 288;
            },

            imageScale() {
                const box = this.boxSize();
                const containScale = Math.min(box / this.naturalWidth, box / this.naturalHeight);
                return containScale * this.zoom;
            },

            clampOffset() {
                this.zoom = Math.max(this.minZoom, Number(this.zoom || this.minZoom));
                const box = this.boxSize();
                const scale = this.imageScale();
                const renderedWidth = this.naturalWidth * scale;
                const renderedHeight = this.naturalHeight * scale;
                const maxX = Math.max(0, (renderedWidth - box) / 2);
                const maxY = Math.max(0, (renderedHeight - box) / 2);
                this.offsetX = Math.max(-maxX, Math.min(maxX, this.offsetX));
                this.offsetY = Math.max(-maxY, Math.min(maxY, this.offsetY));
            },
        };
    };
</script>
