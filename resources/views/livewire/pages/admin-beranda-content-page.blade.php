<div class="space-y-6">
    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="display-font text-4xl leading-tight">Konten Beranda</h2>
                <p class="mt-2 text-sm text-(--muted)">Kelola hero background, data Kepala Prodi, dan foto galeri dari
                    satu panel modern.</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Home CMS</span>
        </div>

    </section>

    <section class="section-box rounded-3xl p-6 md:p-8">
        <h3 class="text-lg font-bold">Header Portal</h3>
        <form wire:submit="simpanHeaderPortal" class="mt-4 grid gap-4 md:grid-cols-2">
            <label class="text-sm">Teks Atas Logo
                <input wire:model.defer="headerLogoLabel" type="text"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm">Judul Header
                <input wire:model.defer="headerTitleText" type="text"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm md:col-span-2">Upload Logo Header
                <input wire:model="headerLogoFile" type="file" accept="image/*"
                    class="mt-2 w-full rounded-2xl border border-dashed border-indigo-200 bg-indigo-50/50 px-4 py-3 text-sm text-indigo-700" />
            </label>
            <div class="md:col-span-2 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 bg-slate-50/90 px-4 py-4">
                    <div class="flex items-center gap-4">
                        @if ($headerLogoFile || $headerLogoUrl)
                            <img src="{{ $headerLogoFile ? $headerLogoFile->temporaryUrl() : $headerLogoUrl }}"
                                alt="Preview Logo Header"
                                class="h-12 w-12 rounded-xl border border-slate-200 bg-white object-cover shadow-sm" />
                        @else
                            <div class="logo-badge">PS</div>
                        @endif
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                                {{ $headerLogoLabel }}</p>
                            <p class="display-font text-lg font-bold leading-tight text-slate-800">
                                {{ $headerTitleText }}</p>
                        </div>
                    </div>
                    <div class="hidden items-center gap-2 lg:flex">
                        <span
                            class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Beranda</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold text-slate-500">Profil</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold text-slate-500">Laporan</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold text-slate-500">Galeri</span>
                    </div>
                </div>
            </div>
            <div class="md:col-span-2">
                <button type="submit"
                    class="rounded-2xl bg-linear-to-r from-blue-600 to-cyan-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-100">Publikasikan
                    Header Portal</button>
            </div>
        </form>
    </section>

    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold">Kontak dan Peta</h3>
                <p class="mt-1 text-sm text-(--muted)">Atur email, telepon, WhatsApp, media sosial, dan embed map
                    publik.</p>
            </div>
            <button type="button" wire:click="tambahSocialLink"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Tambah
                Media Sosial</button>
        </div>
        <form wire:submit="simpanKontak" class="mt-4 grid gap-4 md:grid-cols-2">
            <label class="text-sm">Email Kontak
                <input wire:model.defer="contactEmail" type="email"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm">Telepon
                <input wire:model.defer="contactPhone" type="text" placeholder="(021) 1234567"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm">WhatsApp
                <input wire:model.defer="contactWhatsapp" type="text" placeholder="6281234567890"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm md:col-span-2">Alamat
                <textarea wire:model.defer="contactAddress" rows="3"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
            </label>
            <div class="md:col-span-2 space-y-3">
                @foreach ($contactSocialLinks as $index => $social)
                    <div wire:key="social-link-{{ $index }}"
                        class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:grid-cols-[0.9fr_1.4fr_auto] md:items-end">
                        <label class="text-sm">Nama Platform
                            <input wire:model.defer="contactSocialLinks.{{ $index }}.label" type="text"
                                placeholder="Instagram"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                        </label>
                        <label class="text-sm">URL Platform
                            <input wire:model.defer="contactSocialLinks.{{ $index }}.url" type="url"
                                placeholder="https://instagram.com/prodi"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                        </label>
                        <button type="button" wire:click="hapusSocialLink({{ $index }})"
                            class="rounded-xl bg-rose-50 px-4 py-2.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">Hapus</button>
                    </div>
                @endforeach
            </div>
            <label class="text-sm md:col-span-2">URL Embed Google Maps
                <textarea wire:model.defer="contactMapEmbedUrl" rows="3"
                    placeholder="https://maps.google.com/maps?...&output=embed"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
            </label>
            <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="grid gap-3 md:grid-cols-4">
                    <div class="rounded-2xl border border-blue-100 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-blue-700">Email</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $contactEmail }}</p>
                    </div>
                    <div class="rounded-2xl border border-sky-100 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-sky-700">Telepon</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $contactPhone }}</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-100 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700">Alamat</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $contactAddress }}</p>
                    </div>
                    <div class="rounded-2xl border border-violet-100 bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-violet-700">WhatsApp</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $contactWhatsapp }}</p>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($contactSocialLinks as $social)
                        <span
                            class="rounded-full border border-indigo-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-700">
                            {{ data_get($social, 'label') }}
                        </span>
                    @endforeach
                </div>
                <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <iframe title="Preview Peta Lokasi" class="h-56 w-full" src="{{ $contactMapEmbedUrl }}"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            <div class="md:col-span-2">
                <button type="submit"
                    class="rounded-2xl bg-linear-to-r from-sky-600 to-cyan-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-100">Publikasikan
                    Kontak</button>
            </div>
        </form>
    </section>

    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex items-center justify-between gap-3">
            <h3 class="text-lg font-bold">Hero Beranda (Carousel)</h3>
            <button type="button" wire:click="tambahHeroItem"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Tambah
                Slide</button>
        </div>
        <form wire:submit="simpanHero" class="mt-4 space-y-4">
            @foreach ($heroItems as $index => $item)
                @php $heroUpload = $heroImageFiles[$index] ?? null; @endphp
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="text-sm">Upload Gambar Slide {{ $index + 1 }}
                        <input wire:model="heroImageFiles.{{ $index }}" type="file" accept="image/*"
                            class="mt-2 w-full rounded-xl border border-dashed border-indigo-200 bg-indigo-50/50 px-3 py-2 text-sm text-indigo-700" />
                    </label>
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <img src="{{ $heroUpload ? $heroUpload->temporaryUrl() : data_get($item, 'image_url') }}"
                            alt="Preview Hero {{ $index + 1 }}"
                            class="h-28 w-52 rounded-xl border border-slate-200 object-cover" />
                        <button type="button" wire:click="hapusHeroItem({{ $index }})"
                            class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">Hapus</button>
                    </div>
                </div>
            @endforeach
            <button type="submit"
                class="rounded-2xl bg-linear-to-r from-indigo-600 to-blue-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-100">Publikasikan
                Hero</button>
        </form>
    </section>

    <section class="section-box rounded-3xl p-6 md:p-8">
        <h3 class="text-lg font-bold">Kepala Prodi</h3>
        <form wire:submit="simpanKaprodi" class="mt-4 grid gap-4 md:grid-cols-2">
            <label class="text-sm">Nama Kepala Prodi
                <input wire:model.defer="kaprodiName" type="text"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm">Jabatan
                <input wire:model.defer="kaprodiTitle" type="text"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
            </label>
            <label class="text-sm md:col-span-2">Upload Foto Kepala Prodi
                <input wire:model="kaprodiPhotoFile" type="file" accept="image/*"
                    class="mt-2 w-full rounded-2xl border border-dashed border-indigo-200 bg-indigo-50/50 px-4 py-3 text-sm text-indigo-700" />
            </label>
            <label class="text-sm md:col-span-2">Kutipan Kepala Prodi
                <textarea wire:model.defer="kaprodiQuote" rows="4"
                    class="mt-2 w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
            </label>
            <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center gap-4">
                    <img src="{{ $kaprodiPhotoFile ? $kaprodiPhotoFile->temporaryUrl() : $kaprodiPhotoUrl }}"
                        alt="Preview Kepala Prodi"
                        class="h-16 w-16 rounded-full border border-slate-200 object-cover" />
                    <div>
                        <p class="font-semibold text-slate-800">{{ $kaprodiName }}</p>
                        <p class="text-xs text-slate-500">{{ $kaprodiTitle }}</p>
                    </div>
                </div>
                <p class="mt-3 text-sm text-slate-600">"{{ $kaprodiQuote }}"</p>
            </div>
            <div class="md:col-span-2">
                <button type="submit"
                    class="rounded-2xl bg-linear-to-r from-violet-600 to-fuchsia-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-violet-100">Publikasikan
                    Profil Kaprodi</button>
            </div>
        </form>
    </section>

    <section id="highlight-cepat" class="section-box rounded-3xl p-6 md:p-8 scroll-mt-28">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold">Highlight Cepat Beranda</h3>
                <p class="mt-1 text-sm text-(--muted)">Seluruh kartu highlight di beranda diambil dari daftar ini.</p>
            </div>
            <button type="button" wire:click="tambahQuickHighlight"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Tambah
                Highlight</button>
        </div>

        <form wire:submit="simpanQuickHighlights" class="mt-4 space-y-4">
            @foreach ($quickHighlights as $index => $item)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <span
                            class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                            Posisi {{ $index + 1 }}
                        </span>
                        <div class="flex gap-2">
                            <button type="button" wire:click="pindahQuickHighlightKeAtas({{ $index }})"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Naik</button>
                            <button type="button" wire:click="pindahQuickHighlightKeBawah({{ $index }})"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Turun</button>
                        </div>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="text-sm">Judul
                            <input wire:model.defer="quickHighlights.{{ $index }}.title" type="text"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                        </label>
                        <label class="text-sm">Label Tombol
                            <input wire:model.defer="quickHighlights.{{ $index }}.link_label" type="text"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                        </label>
                        <label class="text-sm md:col-span-2">Deskripsi
                            <textarea wire:model.defer="quickHighlights.{{ $index }}.description" rows="2"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"></textarea>
                        </label>
                        <label class="text-sm">Link Tujuan
                            <input wire:model.defer="quickHighlights.{{ $index }}.link" type="text"
                                placeholder="/laporan atau #statistik-beranda"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                        </label>
                        <label class="text-sm">Ikon
                            <select wire:model.defer="quickHighlights.{{ $index }}.icon_key"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                <option value="chart">Chart</option>
                                <option value="document">Document</option>
                                <option value="users">Users</option>
                                <option value="award">Award</option>
                            </select>
                        </label>
                        <label class="text-sm">Warna
                            <select wire:model.defer="quickHighlights.{{ $index }}.color_key"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                <option value="blue">Biru</option>
                                <option value="violet">Violet</option>
                                <option value="emerald">Hijau</option>
                                <option value="amber">Amber</option>
                            </select>
                        </label>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="button" wire:click="hapusQuickHighlight({{ $index }})"
                            class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">Hapus</button>
                    </div>
                </div>
            @endforeach

            @if (empty($quickHighlights))
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-500">
                    Belum ada highlight cepat. Tambahkan data baru lalu klik publikasi.
                </div>
            @endif

            <button type="submit"
                class="rounded-2xl bg-linear-to-r from-indigo-600 to-blue-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-100">Publikasikan
                Highlight Cepat</button>
        </form>
    </section>

    <section class="section-box rounded-3xl p-6 md:p-8">
        <div class="flex items-center justify-between gap-3">
            <h3 class="text-lg font-bold">Foto Galeri</h3>
            <button type="button" wire:click="tambahGalleryItem"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Tambah
                Foto</button>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            @php
                $adminGalleryKategori = collect([
                    'Prestasi Mahasiswa',
                    'Kegiatan Akademik',
                    'Kegiatan Mahasiswa',
                    'Pengabdian Masyarakat',
                    'Kerjasama & MoU',
                ])
                    ->merge(collect($galleryItems)->pluck('category')->filter())
                    ->unique()
                    ->values();
            @endphp
            <button type="button" wire:click="pilihKategoriGaleri('Semua')"
                class="rounded-full px-4 py-2 text-xs font-semibold {{ $galeriKategoriDipilih === 'Semua' ? 'bg-(--accent) text-white' : 'border border-slate-300 bg-white text-slate-700' }}">Semua</button>
            @foreach ($adminGalleryKategori as $kategori)
                <button type="button" wire:click="pilihKategoriGaleri('{{ $kategori }}')"
                    class="rounded-full px-4 py-2 text-xs font-semibold {{ $galeriKategoriDipilih === $kategori ? 'bg-(--accent) text-white' : 'border border-slate-300 bg-white text-slate-700' }}">{{ $kategori }}</button>
            @endforeach
        </div>

        <form wire:submit="simpanGaleri" class="mt-4 space-y-4">
            @foreach ($galleryItems as $index => $item)
                @continue($galeriKategoriDipilih !== 'Semua' && data_get($item, 'category') !== $galeriKategoriDipilih)
                @php $galleryUpload = $galleryImageFiles[$index] ?? null; @endphp
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="text-sm">Judul
                            <input wire:model.defer="galleryItems.{{ $index }}.title" type="text"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                        </label>
                        <label class="text-sm">Kategori
                            <select wire:model.defer="galleryItems.{{ $index }}.category"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                @foreach ($adminGalleryKategori as $kategoriOption)
                                    <option value="{{ $kategoriOption }}">{{ $kategoriOption }}</option>
                                @endforeach
                                <option value="__new__">+ Tambah Kategori Baru</option>
                            </select>

                            @if ((string) data_get($item, 'category') === '__new__')
                                <input wire:model.defer="galleryItems.{{ $index }}.custom_category"
                                    type="text" placeholder="Nama kategori baru"
                                    class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                                @error("galleryItems.$index.custom_category")
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            @endif

                            <p class="mt-1 text-xs text-slate-500">Pilih dari daftar, atau tambah kategori baru.</p>
                        </label>
                        <label class="text-sm">Upload Gambar Galeri
                            <input wire:model="galleryImageFiles.{{ $index }}" type="file"
                                accept="image/*"
                                class="mt-2 w-full rounded-xl border border-dashed border-indigo-200 bg-indigo-50/50 px-3 py-2 text-sm text-indigo-700" />
                        </label>
                    </div>
                    <div class="mt-3 flex items-center justify-between gap-3">
                        <img src="{{ $galleryUpload ? $galleryUpload->temporaryUrl() : data_get($item, 'image_url') }}"
                            alt="Preview Galeri" class="h-24 w-40 rounded-xl border border-slate-200 object-cover" />
                        <span
                            class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">{{ data_get($item, 'category') }}</span>
                        <button type="button" wire:click="hapusGalleryItem({{ $index }})"
                            class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">Hapus</button>
                    </div>
                </div>
            @endforeach

            @if (collect($galleryItems)->filter(fn($item) => $galeriKategoriDipilih === 'Semua' || data_get($item, 'category') === $galeriKategoriDipilih)->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-500">
                    Belum ada foto pada kategori ini di panel admin.
                </div>
            @endif

            <button type="submit"
                class="rounded-2xl bg-linear-to-r from-emerald-600 to-teal-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-100">Publikasikan
                Galeri</button>
        </form>
    </section>
</div>
