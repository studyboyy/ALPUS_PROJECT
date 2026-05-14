<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class HomePageSetting extends Model
{
    protected $fillable = [
        'hero_background_url',
        'hero_items',
        'quick_highlights',
        'header_logo_url',
        'header_logo_label',
        'header_title_text',
        'contact_email',
        'contact_phone',
        'contact_whatsapp',
        'contact_address',
        'contact_socials',
        'contact_social_links',
        'contact_map_embed_url',
        'kaprodi_name',
        'kaprodi_title',
        'kaprodi_quote',
        'kaprodi_photo_url',
        'gallery_items',
    ];

    protected $casts = [
        'hero_items' => 'array',
        'quick_highlights' => 'array',
        'contact_social_links' => 'array',
        'gallery_items' => 'array',
    ];

    public static function defaults(): array
    {
        return [
            'hero_background_url' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1800&q=80',
            'hero_items' => [
                [
                    'image_url' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1800&q=80',
                ],
                [
                    'image_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1800&q=80',
                ],
                [
                    'image_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1800&q=80',
                ],
            ],
            'quick_highlights' => [
                [
                    'title' => 'Dosen & Penelitian',
                    'description' => 'Daftar dokumen DTPS, penelitian, dan publikasi dosen.',
                    'link' => '/dokumen/kategori/dosen-penelitian',
                    'link_label' => 'Lihat Detail',
                    'icon_key' => 'users',
                    'color_key' => 'emerald',
                ],
                [
                    'title' => 'Prestasi Mahasiswa',
                    'description' => 'Dokumentasi prestasi, kompetisi, dan kegiatan unggulan.',
                    'link' => '/galeri/kategori/prestasi-mahasiswa',
                    'link_label' => 'Lihat Detail',
                    'icon_key' => 'award',
                    'color_key' => 'amber',
                ],
                [
                    'title' => 'Laporan Tahunan',
                    'description' => 'Ringkasan eksekutif, akademik, riset, dan keuangan.',
                    'link' => '/laporan',
                    'link_label' => 'Lihat Detail',
                    'icon_key' => 'document',
                    'color_key' => 'violet',
                ],
                [
                    'title' => 'Statistik Kinerja Tahun Berjalan',
                    'description' => 'Ringkasan mahasiswa, dosen, publikasi, dan akreditasi.',
                    'link' => '/statistik',
                    'link_label' => 'Lihat Detail',
                    'icon_key' => 'chart',
                    'color_key' => 'blue',
                ],
            ],
            'header_logo_url' => '',
            'header_logo_label' => 'Logo Program Studi',
            'header_title_text' => 'Laporan Tahunan Kepala Program Studi [Nama Prodi]',
            'contact_email' => 'sekretariat@prodi.ac.id',
            'contact_phone' => '(021) 1234567',
            'contact_whatsapp' => '6281234567890',
            'contact_address' => 'Gedung Prodi, Kampus Utama',
            'contact_socials' => 'Instagram · YouTube · LinkedIn',
            'contact_social_links' => [
                [
                    'label' => 'Instagram',
                    'url' => 'https://instagram.com/prodi',
                ],
                [
                    'label' => 'YouTube',
                    'url' => 'https://youtube.com/@prodi',
                ],
                [
                    'label' => 'LinkedIn',
                    'url' => 'https://linkedin.com/company/prodi',
                ],
            ],
            'contact_map_embed_url' => 'https://maps.google.com/maps?q=universitas%20indonesia&t=&z=13&ie=UTF8&iwloc=&output=embed',
            'kaprodi_name' => 'Dr. Nama Kepala Prodi',
            'kaprodi_title' => 'Kepala Program Studi',
            'kaprodi_quote' => 'Laporan ini tidak hanya memotret capaian, tetapi menjadi kompas evaluasi berkelanjutan untuk membangun budaya mutu, inovasi, dan kolaborasi di Program Studi kami.',
            'kaprodi_photo_url' => 'https://images.unsplash.com/photo-1607746882042-944635dfe10e?auto=format&fit=crop&w=220&q=80',
            'gallery_items' => [
                [
                    'title' => 'Kegiatan Akademik',
                    'category' => 'Kegiatan Akademik',
                    'category_slug' => 'kegiatan-akademik',
                    'image_url' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'title' => 'Prestasi Mahasiswa: Juara Inovasi',
                    'category' => 'Prestasi Mahasiswa',
                    'category_slug' => 'prestasi-mahasiswa',
                    'image_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'title' => 'Kegiatan Mahasiswa',
                    'category' => 'Kegiatan Mahasiswa',
                    'category_slug' => 'kegiatan-mahasiswa',
                    'image_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'title' => 'Prestasi Mahasiswa: Finalis PKM',
                    'category' => 'Prestasi Mahasiswa',
                    'category_slug' => 'prestasi-mahasiswa',
                    'image_url' => 'https://images.unsplash.com/photo-1517486808906-6ca8b3f8e1e0?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'title' => 'Pengabdian Masyarakat',
                    'category' => 'Pengabdian Masyarakat',
                    'category_slug' => 'pengabdian-masyarakat',
                    'image_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'title' => 'Kerjasama dan MoU',
                    'category' => 'Kerjasama & MoU',
                    'category_slug' => 'kerjasama-mou',
                    'image_url' => 'https://images.unsplash.com/photo-1517486808906-6ca8b3f8e1e0?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'title' => 'Seminar dan Workshop',
                    'category' => 'Kegiatan Akademik',
                    'category_slug' => 'kegiatan-akademik',
                    'image_url' => 'https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=900&q=80',
                ],
                [
                    'title' => 'Kegiatan Eksternal',
                    'category' => 'Kerjasama & MoU',
                    'category_slug' => 'kerjasama-mou',
                    'image_url' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=900&q=80',
                ],
            ],
        ];
    }

    public static function ensureDefaults(): void
    {
        if (!Schema::hasTable('home_page_settings')) {
            return;
        }

        if (static::query()->exists()) {
            return;
        }

        static::query()->create(static::defaults());
    }

    public static function current(): array
    {
        if (!Schema::hasTable('home_page_settings')) {
            return static::defaults();
        }

        static::ensureDefaults();
        $row = static::query()->first();

        if (!$row) {
            return static::defaults();
        }

        $galleryItemsPayload = collect($row->gallery_items ?? static::defaults()['gallery_items'])
            ->map(function ($item): array {
                $category = static::inferGalleryCategory(
                    (string) data_get($item, 'category', ''),
                    (string) data_get($item, 'title', '')
                );

                return [
                    'title' => (string) data_get($item, 'title', ''),
                    'category' => $category,
                    'category_slug' => (string) data_get($item, 'category_slug', static::slugFromCategory($category)),
                    'image_url' => (string) data_get($item, 'image_url', ''),
                ];
            })
            ->values();

        if (Schema::hasTable('home_page_settings') && $row->gallery_items !== $galleryItemsPayload->all()) {
            $row->gallery_items = $galleryItemsPayload->all();
            $row->save();
        }

        $heroItems = collect($row->hero_items ?? static::defaults()['hero_items'])
            ->map(fn($item) => [
                'image_url' => (string) data_get($item, 'image_url', ''),
            ])
            ->filter(fn($item) => $item['image_url'] !== '')
            ->values();

        if ($heroItems->isEmpty()) {
            $heroItems = collect([
                ['image_url' => (string) ($row->hero_background_url ?: static::defaults()['hero_background_url'])],
            ]);
        }

        $quickHighlights = static::normalizeQuickHighlights($row->quick_highlights);

        $socialLinks = static::normalizeSocialLinks($row->contact_social_links);

        if ($socialLinks->isEmpty()) {
            $socialLinks = collect(preg_split('/\s*[·|,]\s*/u', (string) ($row->contact_socials ?: '')))
                ->map(fn($label) => trim((string) $label))
                ->filter()
                ->map(fn($label) => [
                    'label' => $label,
                    'url' => '',
                ]);
        }

        if ($socialLinks->isEmpty()) {
            $socialLinks = static::normalizeSocialLinks(static::defaults()['contact_social_links']);
        }

        return [
            'hero_background_url' => (string) ($row->hero_background_url ?: static::defaults()['hero_background_url']),
            'hero_items' => $heroItems->all(),
            'quick_highlights' => $quickHighlights->all(),
            'header_logo_url' => (string) ($row->header_logo_url ?: static::defaults()['header_logo_url']),
            'header_logo_label' => (string) ($row->header_logo_label ?: static::defaults()['header_logo_label']),
            'header_title_text' => (string) ($row->header_title_text ?: static::defaults()['header_title_text']),
            'contact_email' => (string) ($row->contact_email ?: static::defaults()['contact_email']),
            'contact_phone' => (string) ($row->contact_phone ?: static::defaults()['contact_phone']),
            'contact_whatsapp' => (string) ($row->contact_whatsapp ?: static::defaults()['contact_whatsapp']),
            'contact_address' => (string) ($row->contact_address ?: static::defaults()['contact_address']),
            'contact_socials' => $socialLinks->pluck('label')->implode(' · '),
            'contact_social_links' => $socialLinks->all(),
            'contact_map_embed_url' => (string) ($row->contact_map_embed_url ?: static::defaults()['contact_map_embed_url']),
            'kaprodi_name' => (string) ($row->kaprodi_name ?: static::defaults()['kaprodi_name']),
            'kaprodi_title' => (string) ($row->kaprodi_title ?: static::defaults()['kaprodi_title']),
            'kaprodi_quote' => (string) ($row->kaprodi_quote ?: static::defaults()['kaprodi_quote']),
            'kaprodi_photo_url' => (string) ($row->kaprodi_photo_url ?: static::defaults()['kaprodi_photo_url']),
            'gallery_items' => $galleryItemsPayload->all(),
        ];
    }

    private static function normalizeSocialLinks(?array $links): \Illuminate\Support\Collection
    {
        return collect($links ?? [])
            ->map(fn($item) => [
                'label' => trim((string) data_get($item, 'label', '')),
                'url' => trim((string) data_get($item, 'url', '')),
            ])
            ->filter(fn($item) => $item['label'] !== '');
    }

    private static function normalizeQuickHighlights(?array $items): \Illuminate\Support\Collection
    {
        return collect($items ?? [])
            ->map(fn($item) => [
                'title' => trim((string) data_get($item, 'title', '')),
                'description' => trim((string) data_get($item, 'description', '')),
                'link' => trim((string) data_get($item, 'link', '#')),
                'link_label' => trim((string) data_get($item, 'link_label', 'Lihat Detail')),
                'icon_key' => trim((string) data_get($item, 'icon_key', 'chart')),
                'color_key' => trim((string) data_get($item, 'color_key', 'blue')),
            ])
            ->filter(fn($item) => $item['title'] !== '');
    }

    private static function inferGalleryCategory(string $category, string $title): string
    {
        if (trim($category) !== '') {
            return trim($category);
        }

        $haystack = mb_strtolower(trim($title));

        return match (true) {
            str_contains($haystack, 'prestasi'),
            str_contains($haystack, 'juara'),
            str_contains($haystack, 'finalis'),
            str_contains($haystack, 'kompetisi'),
            str_contains($haystack, 'pkm') => 'Prestasi Mahasiswa',
            str_contains($haystack, 'mahasiswa') => 'Kegiatan Mahasiswa',
            str_contains($haystack, 'pengabdian') => 'Pengabdian Masyarakat',
            str_contains($haystack, 'mou'),
            str_contains($haystack, 'kerjasama'),
            str_contains($haystack, 'kerja sama'),
            str_contains($haystack, 'eksternal') => 'Kerjasama & MoU',
            default => 'Kegiatan Akademik',
        };
    }

    public static function slugFromCategory(string $category): string
    {
        return Str::slug($category) ?: 'kategori-galeri';
    }
}
