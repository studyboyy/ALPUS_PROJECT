<?php

namespace App\Livewire\Pages;

use App\Models\HomePageSetting;
use App\Models\Prodi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Admin Konten Beranda')]
class AdminBerandaContentPage extends Component
{
    use WithFileUploads;

    public string $headerLogoUrl = '';
    public string $headerLogoLabel = '';
    public string $headerTitleText = '';
    public string $contactEmail = '';
    public string $contactPhone = '';
    public string $contactWhatsapp = '';
    public string $contactAddress = '';
    public string $contactMapQuery = '';   // nama tempat / koordinat, lebih mudah dari URL embed
    public string $contactMapEmbedUrl = ''; // disimpan juga untuk backward compat tampilan
    public array $contactSocialLinks = [];
    public string $kaprodiName = '';
    public string $kaprodiTitle = '';
    public string $kaprodiQuote = '';
    public string $kaprodiPhotoUrl = '';
    public array $quickHighlights = [];

    public array $heroItems = [];
    public array $galleryItems = [];
    #[Locked]
    public int $persistedGalleryCount = 0;
    public string $galeriKategoriDipilih = 'Semua';

    public array $heroImageFiles = [];
    public array $galleryImageFiles = [];
    public $headerLogoFile;
    public $kaprodiPhotoFile;
    public string $croppedHeaderLogoDataUrl = '';

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function tambahHeroItem(): void
    {
        $this->heroItems[] = [
            'image_url' => '',
        ];
    }

    public function hapusHeroItem(int $index): void
    {
        if (!auth()->user()?->canDelete()) { return; }
        if (!isset($this->heroItems[$index])) {
            return;
        }

        unset($this->heroItems[$index]);
        $this->heroItems = array_values($this->heroItems);
        unset($this->heroImageFiles[$index]);
        $this->heroImageFiles = array_values($this->heroImageFiles);
    }

    public function tambahGalleryItem(): void
    {
        $category = $this->galeriKategoriDipilih !== 'Semua'
            ? $this->galeriKategoriDipilih
            : 'Kegiatan Akademik';

        $this->galleryItems[] = [
            'title'           => 'Foto Kegiatan Baru',
            'description'     => '',
            'category'        => $category,
            'custom_category' => '',
            'category_slug'   => HomePageSetting::slugFromCategory($category),
            'image_url'       => '',
        ];

        $this->resetValidation();
    }

    public function tambahSocialLink(): void
    {
        $this->contactSocialLinks[] = [
            'label' => '',
            'url' => '',
        ];
    }

    public function tambahQuickHighlight(): void
    {
        $this->quickHighlights[] = [
            'title' => '',
            'description' => '',
            'link' => '#',
            'link_label' => 'Lihat Detail',
            'icon_key' => 'chart',
            'color_key' => 'blue',
        ];
    }

    public function pindahQuickHighlightKeAtas(int $index): void
    {
        if ($index <= 0 || !isset($this->quickHighlights[$index], $this->quickHighlights[$index - 1])) {
            return;
        }

        [$this->quickHighlights[$index - 1], $this->quickHighlights[$index]] = [
            $this->quickHighlights[$index],
            $this->quickHighlights[$index - 1],
        ];
    }

    public function pindahQuickHighlightKeBawah(int $index): void
    {
        if (!isset($this->quickHighlights[$index], $this->quickHighlights[$index + 1])) {
            return;
        }

        [$this->quickHighlights[$index], $this->quickHighlights[$index + 1]] = [
            $this->quickHighlights[$index + 1],
            $this->quickHighlights[$index],
        ];
    }

    public function hapusQuickHighlight(int $index): void
    {
        if (!auth()->user()?->canDelete()) { return; }
        if (!isset($this->quickHighlights[$index])) {
            return;
        }

        unset($this->quickHighlights[$index]);
        $this->quickHighlights = array_values($this->quickHighlights);
    }

    public function hapusSocialLink(int $index): void
    {
        if (!auth()->user()?->canDelete()) { return; }
        if (!isset($this->contactSocialLinks[$index])) {
            return;
        }

        unset($this->contactSocialLinks[$index]);
        $this->contactSocialLinks = array_values($this->contactSocialLinks);
    }

    public function hapusGalleryItem(int $index): void
    {
        if (!auth()->user()?->canDelete() && $index < $this->persistedGalleryCount) { return; }
        if (!isset($this->galleryItems[$index])) {
            return;
        }

        unset($this->galleryItems[$index]);
        $this->galleryItems = array_values($this->galleryItems);
        unset($this->galleryImageFiles[$index]);
        $this->galleryImageFiles = array_values($this->galleryImageFiles);
    }

    public function pilihKategoriGaleri(string $kategori): void
    {
        $this->galeriKategoriDipilih = $kategori;
    }

    public function simpanHero(): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->flashStatus('Mengubah hero hanya dapat dilakukan oleh Admin.');
            return;
        }

        $this->validate([
            'heroImageFiles.*' => ['nullable', 'image', 'max:4096'],
        ]);

        foreach ($this->heroImageFiles as $index => $file) {
            if (!$file || !isset($this->heroItems[$index])) {
                continue;
            }

            $path = $file->store('home-content/hero', 'public');
            $this->heroItems[$index]['image_url'] = asset('storage/' . $path);
        }

        $this->heroItems = $this->normalizedItems($this->heroItems, false)
            ->filter(fn($item) => data_get($item, 'image_url') !== '')
            ->values()
            ->all();

        if (count($this->heroItems) === 0) {
            $this->addError('heroItems', 'Minimal satu gambar hero wajib tersedia.');
            return;
        }

        $row = $this->getSettingsRow();
        $row->hero_background_url = data_get($this->heroItems, '0.image_url', HomePageSetting::defaults()['hero_background_url']);
        $row->hero_items = $this->heroItems;
        $row->save();

        $this->heroImageFiles = [];
        $this->flashStatus('Hero beranda berhasil dipublikasikan.');
    }

    public function simpanKaprodi(): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->flashStatus('Mengubah konten kaprodi hanya dapat dilakukan oleh Admin.');
            return;
        }

        $this->validate([
            'kaprodiName' => ['required', 'string', 'max:120'],
            'kaprodiTitle' => ['required', 'string', 'max:120'],
            'kaprodiQuote' => ['required', 'string', 'max:1000'],
            'kaprodiPhotoFile' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($this->kaprodiPhotoFile) {
            $path = $this->kaprodiPhotoFile->store('home-content/kaprodi', 'public');
            $this->kaprodiPhotoUrl = asset('storage/' . $path);
        }

        if ($this->kaprodiPhotoUrl === '') {
            $this->addError('kaprodiPhotoFile', 'Foto kepala prodi wajib diupload.');
            return;
        }

        $row = $this->getSettingsRow();
        $row->fill([
            'kaprodi_name' => $this->kaprodiName,
            'kaprodi_title' => $this->kaprodiTitle,
            'kaprodi_quote' => $this->kaprodiQuote,
            'kaprodi_photo_url' => $this->kaprodiPhotoUrl,
        ]);
        $row->save();

        $this->kaprodiPhotoFile = null;
        $this->flashStatus('Profil kepala prodi berhasil dipublikasikan.');
    }

    public function simpanHeaderPortal(): void
    {
        $user = auth()->user();
        $isAdmin = $user?->isAdmin() === true;
        $canChangeLogo = $isAdmin || $user?->role === 'kaprodi';
        if (! $canChangeLogo) {
            return;
        }

        $this->validate($isAdmin ? [
            'headerLogoLabel' => ['required', 'string', 'max:120'],
            'headerTitleText' => ['required', 'string', 'max:180'],
            'croppedHeaderLogoDataUrl' => ['nullable', 'string'],
        ] : [
            'croppedHeaderLogoDataUrl' => ['required', 'string'],
        ]);

        $row = $this->getSettingsRow();

        if ($this->croppedHeaderLogoDataUrl !== '') {
            $this->headerLogoUrl = $this->storeCroppedHeaderLogo($this->croppedHeaderLogoDataUrl);
            if ($this->getErrorBag()->has('croppedHeaderLogoDataUrl')) {
                return;
            }
        }

        $row->header_logo_url = $this->headerLogoUrl;
        if ($isAdmin) {
            $row->header_logo_label = $this->headerLogoLabel;
            $row->header_title_text = $this->headerTitleText;
        }
        $row->save();

        $this->headerLogoFile = null;
        $this->croppedHeaderLogoDataUrl = '';
        $this->flashStatus('Header portal berhasil dipublikasikan.');
    }

    public function simpanKontak(): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->flashStatus('Mengubah kontak hanya dapat dilakukan oleh Admin.');
            return;
        }

        $this->validate([
            'contactEmail'    => ['required', 'email', 'max:120'],
            'contactPhone'    => ['nullable', 'string', 'max:50'],
            'contactWhatsapp' => ['nullable', 'string', 'max:50'],
            'contactAddress'  => ['required', 'string', 'max:500'],
            'contactMapQuery' => ['nullable', 'string', 'max:500'],
            'contactSocialLinks'         => ['required', 'array', 'min:1'],
            'contactSocialLinks.*.label' => ['required', 'string', 'max:60'],
            'contactSocialLinks.*.url'   => ['nullable', 'string', 'max:500'],
        ]);

        $socialLinks = collect($this->contactSocialLinks)
            ->map(fn($item) => [
                'label' => trim((string) data_get($item, 'label', '')),
                'url'   => trim((string) data_get($item, 'url', '')),
            ])
            ->filter(fn($item) => $item['label'] !== '')
            ->values()
            ->all();

        if (count($socialLinks) === 0) {
            $this->addError('contactSocialLinks', 'Minimal satu media sosial wajib tersedia.');
            return;
        }

        // Bangun embed URL dari query (nama/koordinat), atau simpan langsung jika sudah URL embed
        $mapQuery = trim($this->contactMapQuery);
        if ($mapQuery !== '') {
            if (str_contains($mapQuery, 'output=embed') || str_contains($mapQuery, '/maps/embed')) {
                // Already a proper embed URL
                $this->contactMapEmbedUrl = $mapQuery;
            } elseif (str_starts_with($mapQuery, 'http')) {
                // Try to extract q= param from Google Maps share links
                $parsed = parse_url($mapQuery);
                if (isset($parsed['query'])) {
                    parse_str($parsed['query'], $params);
                    $q = $params['q'] ?? $params['query'] ?? null;
                    if ($q) {
                        $this->contactMapEmbedUrl = 'https://maps.google.com/maps?q=' . urlencode($q) . '&output=embed';
                    } else {
                        // Short link or unrecognised format — use URL itself as search query
                        $this->contactMapEmbedUrl = 'https://maps.google.com/maps?q=' . urlencode($mapQuery) . '&output=embed';
                    }
                } else {
                    $this->contactMapEmbedUrl = 'https://maps.google.com/maps?q=' . urlencode($mapQuery) . '&output=embed';
                }
            } else {
                // Plain name or coordinates
                $this->contactMapEmbedUrl = 'https://maps.google.com/maps?q=' . urlencode($mapQuery) . '&output=embed';
            }
        }

        $row = $this->getSettingsRow();
        $row->contact_email          = $this->contactEmail;
        $row->contact_phone          = $this->contactPhone;
        $row->contact_whatsapp       = $this->contactWhatsapp;
        $row->contact_address        = $this->contactAddress;
        $row->contact_map_embed_url  = $this->contactMapEmbedUrl;
        $row->contact_socials        = collect($socialLinks)->pluck('label')->implode(' · ');
        $row->contact_social_links   = $socialLinks;
        $row->save();

        // Reload agar state sinkron
        $this->contactMapQuery = $this->contactMapEmbedUrl;
        $this->flashStatus('Informasi kontak berhasil dipublikasikan.');
    }

    public function simpanQuickHighlights(): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->flashStatus('Mengubah highlight hanya dapat dilakukan oleh Admin.');
            return;
        }

        $this->validate([
            'quickHighlights' => ['array', 'max:8'],
            'quickHighlights.*.title' => ['required', 'string', 'max:120'],
            'quickHighlights.*.description' => ['required', 'string', 'max:255'],
            'quickHighlights.*.link' => ['required', 'string', 'max:255'],
            'quickHighlights.*.link_label' => ['required', 'string', 'max:50'],
            'quickHighlights.*.icon_key' => ['required', 'in:chart,document,users,award'],
            'quickHighlights.*.color_key' => ['required', 'in:blue,violet,emerald,amber'],
        ]);

        $highlights = collect($this->quickHighlights)
            ->map(fn($item) => [
                'title' => trim((string) data_get($item, 'title', '')),
                'description' => trim((string) data_get($item, 'description', '')),
                'link' => trim((string) data_get($item, 'link', '#')),
                'link_label' => trim((string) data_get($item, 'link_label', 'Lihat Detail')),
                'icon_key' => trim((string) data_get($item, 'icon_key', 'chart')),
                'color_key' => trim((string) data_get($item, 'color_key', 'blue')),
            ])
            ->filter(fn($item) => $item['title'] !== '')
            ->values()
            ->all();

        $row = $this->getSettingsRow();
        $row->quick_highlights = $highlights;
        $row->save();

        $this->flashStatus('Highlight cepat berhasil dipublikasikan.');
    }

    public function simpanGaleri(): void
    {
        $this->resetValidation();

        foreach ($this->galleryItems as $index => $item) {
            if ((string) data_get($item, 'category') !== '__new__') {
                continue;
            }

            if (trim((string) data_get($item, 'custom_category', '')) === '') {
                $this->addError("galleryItems.$index.custom_category", 'Isi nama kategori baru terlebih dahulu.');
            }
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $this->galleryItems = collect($this->galleryItems)
            ->map(function (array $item): array {
                $selectedCategory = trim((string) data_get($item, 'category', ''));
                $customCategory = trim((string) data_get($item, 'custom_category', ''));

                if ($selectedCategory === '__new__') {
                    $item['category'] = $customCategory !== '' ? $customCategory : 'Kegiatan Akademik';
                }

                return $item;
            })
            ->values()
            ->all();

        $this->validate([
            'galleryItems' => ['required', 'array', 'min:1'],
            'galleryItems.*.title' => ['required', 'string', 'max:120'],
            'galleryItems.*.category' => ['required', 'string', 'max:120'],
            'galleryImageFiles.*' => ['nullable', 'image', 'max:4096'],
        ]);

        foreach ($this->galleryImageFiles as $index => $file) {
            if (!$file || !isset($this->galleryItems[$index])) {
                continue;
            }

            $path = $file->store('home-content/gallery', 'public');
            $this->galleryItems[$index]['image_url'] = asset('storage/' . $path);
        }

        $normalizedItems = $this->normalizedItems($this->galleryItems, true)
            ->filter(fn($item) => data_get($item, 'image_url') !== '')
            ->values()
            ->all();

        if (count($normalizedItems) === 0) {
            $this->addError('galleryItems', 'Minimal satu foto galeri wajib tersedia.');
            return;
        }

        $row = $this->getSettingsRow();
        if (auth()->user()?->isAdmin()) {
            $row->gallery_items = $normalizedItems;
            $row->save();
        } else {
            // Data lama selalu dimuat ulang, sehingga kaprodi/sekprodi hanya
            // dapat menambahkan foto dan tidak dapat mengubah data tersimpan.
            $newItems = array_slice($normalizedItems, $this->persistedGalleryCount);
            if ($newItems === []) {
                $this->addError('galleryItems', 'Tambahkan minimal satu foto baru untuk dipublikasikan.');
                return;
            }

            $persistedItems = $this->normalizedItems($row->gallery_items ?? [], true)
                ->filter(fn($item) => data_get($item, 'image_url') !== '')
                ->values()
                ->all();
            $row->gallery_items = [...$persistedItems, ...$newItems];
            $row->saveQuietly();
        }

        $this->galleryImageFiles = [];
        $this->loadSettings();

        $this->flashStatus('Galeri beranda berhasil dipublikasikan.');
    }

    private function loadSettings(): void
    {
        $settings = HomePageSetting::current($this->activeProdiId());

        $this->heroItems = collect($settings['hero_items'] ?? [])
            ->map(fn($item) => [
                'image_url' => (string) data_get($item, 'image_url', ''),
            ])
            ->values()
            ->all();
        if (count($this->heroItems) === 0 && data_get($settings, 'hero_background_url')) {
            $this->heroItems = [['image_url' => (string) $settings['hero_background_url']]];
        }

        $this->headerLogoUrl = $settings['header_logo_url'];
        $this->headerLogoLabel = $settings['header_logo_label'];
        $this->headerTitleText = $settings['header_title_text'];
        $this->contactEmail = $settings['contact_email'];
        $this->contactPhone = $settings['contact_phone'];
        $this->contactWhatsapp = $settings['contact_whatsapp'];
        $this->contactAddress = $settings['contact_address'];
        $this->contactSocialLinks = $settings['contact_social_links'];
        $this->contactMapEmbedUrl = $settings['contact_map_embed_url'];
        // Show the saved embed URL in the input so user can see and edit it
        // Strip output=embed suffix for cleaner display if it's a simple q= URL
        $savedUrl = $settings['contact_map_embed_url'];
        if (preg_match('/maps\.google\.com\/maps\?q=([^&]+)&output=embed/', $savedUrl, $m)) {
            $this->contactMapQuery = urldecode($m[1]);
        } else {
            $this->contactMapQuery = $savedUrl;
        }
        $this->kaprodiName = $settings['kaprodi_name'];
        $this->kaprodiTitle = $settings['kaprodi_title'];
        $this->kaprodiQuote = $settings['kaprodi_quote'];
        $this->kaprodiPhotoUrl = $settings['kaprodi_photo_url'];
        $this->quickHighlights = $settings['quick_highlights'];
        $this->galleryItems = collect($settings['gallery_items'] ?? [])
            ->map(fn($item) => [
                'title'           => (string) data_get($item, 'title', 'Foto Kegiatan'),
                'description'     => (string) data_get($item, 'description', ''),
                'category'        => (string) data_get($item, 'category', 'Kegiatan Akademik'),
                'custom_category' => '',
                'category_slug'   => (string) data_get($item, 'category_slug', HomePageSetting::slugFromCategory((string) data_get($item, 'category', 'Kegiatan Akademik'))),
                'image_url'       => (string) data_get($item, 'image_url', ''),
            ])
            ->values()
            ->all();
        $this->persistedGalleryCount = count($this->galleryItems);

        if (count($this->contactSocialLinks) === 0) {
            $this->contactSocialLinks = HomePageSetting::defaults()['contact_social_links'];
        }
    }

    private function getSettingsRow(): HomePageSetting
    {
        $prodiId = $this->activeProdiId();
        $query = HomePageSetting::query()->withoutGlobalScope('prodi');
        if ($prodiId) {
            $query->where('prodi_id', $prodiId);
        }

        $row = $query->first();
        if ($row) {
            return $row;
        }

        $row = new HomePageSetting(HomePageSetting::defaults());
        if ($prodiId) {
            $row->prodi_id = $prodiId;
        }
        $row->saveQuietly();

        return $row;
    }

    private function activeProdiId(): ?int
    {
        $user = auth()->user();
        if ($user?->isAdmin()) {
            return (int) (session('admin_prodi_id')
                ?: Prodi::query()->where('code', '!=', 'ADMIN')->where('is_active', true)->orderBy('name')->value('id'));
        }

        return $user?->prodi_id ? (int) $user->prodi_id : (session('public_prodi_id') ? (int) session('public_prodi_id') : null);
    }

    private function normalizedItems(array $items, bool $withTitle): Collection
    {
        return collect($items)
            ->map(function ($item) use ($withTitle): array {
                $payload = [
                    'image_url' => trim((string) data_get($item, 'image_url', '')),
                ];

                if ($withTitle) {
                    $payload['title']         = trim((string) data_get($item, 'title', 'Foto Kegiatan'));
                    $payload['description']   = trim((string) data_get($item, 'description', ''));
                    $payload['category']      = trim((string) data_get($item, 'category', 'Kegiatan Akademik'));
                    $payload['category_slug'] = HomePageSetting::slugFromCategory($payload['category']);
                }

                return $payload;
            });
    }

    private function flashStatus(string $message): void
    {
        session()->flash('status', $message);
        $this->dispatch('admin-toast', message: $message);
    }

    private function storeCroppedHeaderLogo(string $dataUrl): string
    {
        if (! preg_match('/^data:image\/(png|jpe?g|webp);base64,/', $dataUrl, $match)) {
            $this->addError('croppedHeaderLogoDataUrl', 'Format hasil crop logo tidak valid.');
            return $this->headerLogoUrl;
        }

        $extension = $match[1] === 'jpeg' ? 'jpg' : $match[1];
        $payload = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $binary = base64_decode($payload, true);

        if ($binary === false) {
            $this->addError('croppedHeaderLogoDataUrl', 'Logo hasil crop gagal diproses.');
            return $this->headerLogoUrl;
        }

        if (strlen($binary) > 4 * 1024 * 1024) {
            $this->addError('croppedHeaderLogoDataUrl', 'Ukuran logo hasil crop maksimal 4 MB.');

            return $this->headerLogoUrl;
        }

        $path = 'home-content/header/logo-'.Str::uuid().'.'.$extension;
        if (! Storage::disk('public')->put($path, $binary)) {
            $this->addError('croppedHeaderLogoDataUrl', 'Logo gagal disimpan ke penyimpanan aplikasi.');

            return $this->headerLogoUrl;
        }

        return asset('storage/'.$path);
    }

    public function render()
    {
        return view('livewire.pages.admin-beranda-content-page');
    }
}
