<?php

namespace App\Livewire\Pages;

use App\Models\HomePageSetting;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
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
    public string $contactMapEmbedUrl = '';
    public array $contactSocialLinks = [];
    public string $kaprodiName = '';
    public string $kaprodiTitle = '';
    public string $kaprodiQuote = '';
    public string $kaprodiPhotoUrl = '';
    public array $quickHighlights = [];

    public array $heroItems = [];
    public array $galleryItems = [];
    public string $galeriKategoriDipilih = 'Semua';

    public array $heroImageFiles = [];
    public array $galleryImageFiles = [];
    public $headerLogoFile;
    public $kaprodiPhotoFile;

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
        $this->galleryItems[] = [
            'title' => 'Foto Kegiatan Baru',
            'category' => 'Kegiatan Akademik',
            'category_slug' => HomePageSetting::slugFromCategory('Kegiatan Akademik'),
            'image_url' => '',
        ];
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

    public function hapusQuickHighlight(int $index): void
    {
        if (!isset($this->quickHighlights[$index])) {
            return;
        }

        unset($this->quickHighlights[$index]);
        $this->quickHighlights = array_values($this->quickHighlights);
    }

    public function hapusSocialLink(int $index): void
    {
        if (!isset($this->contactSocialLinks[$index])) {
            return;
        }

        unset($this->contactSocialLinks[$index]);
        $this->contactSocialLinks = array_values($this->contactSocialLinks);
    }

    public function hapusGalleryItem(int $index): void
    {
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
        $this->validate([
            'headerLogoLabel' => ['required', 'string', 'max:120'],
            'headerTitleText' => ['required', 'string', 'max:180'],
            'headerLogoFile' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($this->headerLogoFile) {
            $path = $this->headerLogoFile->store('home-content/header', 'public');
            $this->headerLogoUrl = asset('storage/' . $path);
        }

        $row = $this->getSettingsRow();
        $row->fill([
            'header_logo_url' => $this->headerLogoUrl,
            'header_logo_label' => $this->headerLogoLabel,
            'header_title_text' => $this->headerTitleText,
        ]);
        $row->save();

        $this->headerLogoFile = null;
        $this->flashStatus('Header portal berhasil dipublikasikan.');
    }

    public function simpanKontak(): void
    {
        $this->validate([
            'contactEmail' => ['required', 'email', 'max:120'],
            'contactPhone' => ['nullable', 'string', 'max:50'],
            'contactWhatsapp' => ['nullable', 'string', 'max:50'],
            'contactAddress' => ['required', 'string', 'max:500'],
            'contactMapEmbedUrl' => ['required', 'url', 'max:2000'],
            'contactSocialLinks' => ['required', 'array', 'min:1'],
            'contactSocialLinks.*.label' => ['required', 'string', 'max:60'],
            'contactSocialLinks.*.url' => ['nullable', 'url', 'max:255'],
        ]);

        $socialLinks = collect($this->contactSocialLinks)
            ->map(fn($item) => [
                'label' => trim((string) data_get($item, 'label', '')),
                'url' => trim((string) data_get($item, 'url', '')),
            ])
            ->filter(fn($item) => $item['label'] !== '')
            ->values()
            ->all();

        if (count($socialLinks) === 0) {
            $this->addError('contactSocialLinks', 'Minimal satu media sosial wajib tersedia.');
            return;
        }

        $row = $this->getSettingsRow();
        $row->fill([
            'contact_email' => $this->contactEmail,
            'contact_phone' => $this->contactPhone,
            'contact_whatsapp' => $this->contactWhatsapp,
            'contact_address' => $this->contactAddress,
            'contact_socials' => collect($socialLinks)->pluck('label')->implode(' · '),
            'contact_social_links' => $socialLinks,
            'contact_map_embed_url' => $this->contactMapEmbedUrl,
        ]);
        $row->save();

        $this->flashStatus('Informasi kontak berhasil dipublikasikan.');
    }

    public function simpanQuickHighlights(): void
    {
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

        $this->galleryItems = $this->normalizedItems($this->galleryItems, true)
            ->filter(fn($item) => data_get($item, 'image_url') !== '')
            ->values()
            ->all();

        if (count($this->galleryItems) === 0) {
            $this->addError('galleryItems', 'Minimal satu foto galeri wajib tersedia.');
            return;
        }

        $row = $this->getSettingsRow();
        $row->gallery_items = $this->galleryItems;
        $row->save();

        $this->galleryImageFiles = [];

        $this->flashStatus('Galeri beranda berhasil dipublikasikan.');
    }

    private function loadSettings(): void
    {
        $settings = HomePageSetting::current();

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
        $this->kaprodiName = $settings['kaprodi_name'];
        $this->kaprodiTitle = $settings['kaprodi_title'];
        $this->kaprodiQuote = $settings['kaprodi_quote'];
        $this->kaprodiPhotoUrl = $settings['kaprodi_photo_url'];
        $this->quickHighlights = $settings['quick_highlights'];
        $this->galleryItems = $settings['gallery_items'];

        if (count($this->contactSocialLinks) === 0) {
            $this->contactSocialLinks = HomePageSetting::defaults()['contact_social_links'];
        }
    }

    private function getSettingsRow(): HomePageSetting
    {
        HomePageSetting::ensureDefaults();

        return HomePageSetting::query()->first() ?? new HomePageSetting();
    }

    private function normalizedItems(array $items, bool $withTitle): Collection
    {
        return collect($items)
            ->map(function ($item) use ($withTitle): array {
                $payload = [
                    'image_url' => trim((string) data_get($item, 'image_url', '')),
                ];

                if ($withTitle) {
                    $payload['title'] = trim((string) data_get($item, 'title', 'Foto Kegiatan'));
                    $payload['category'] = trim((string) data_get($item, 'category', 'Kegiatan Akademik'));
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

    public function render()
    {
        return view('livewire.pages.admin-beranda-content-page');
    }
}
