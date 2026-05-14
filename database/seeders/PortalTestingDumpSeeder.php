<?php

namespace Database\Seeders;

use App\Models\AnnualReportSection;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use App\Models\DocumentItem;
use App\Models\HomePageSetting;
use App\Models\ProfileSection;
use Illuminate\Database\Seeder;

class PortalTestingDumpSeeder extends Seeder
{
    public function run(): void
    {
        $years = range(2019, 2028);

        DashboardProgramItem::query()->delete();
        AnnualReportSection::query()->delete();
        DashboardYearStat::query()->delete();
        DocumentItem::query()->delete();
        ProfileSection::query()->delete();
        HomePageSetting::query()->delete();

        foreach ($years as $year) {
            DashboardYearStat::query()->create([
                'year' => $year,
                'kpi' => [
                    ['label' => 'Mahasiswa Aktif', 'value' => 780 + (($year - 2019) * 90), 'decimals' => 0],
                    ['label' => 'IPK Rata-rata', 'value' => 3.15 + (($year - 2019) * 0.05), 'decimals' => 2],
                    ['label' => 'Dosen Tetap', 'value' => 28 + (($year - 2019) * 3), 'decimals' => 0],
                    ['label' => 'Publikasi', 'value' => 18 + (($year - 2019) * 7), 'decimals' => 0],
                ],
                'trend' => [
                    'mahasiswa' => '10,145 80,132 150,116 220,96 300,' . (84 - (($year - 2019) * 4)),
                    'ipk' => '10,148 80,139 150,130 220,118 300,' . (112 - (($year - 2019) * 2)),
                    'mahasiswaLastY' => 84 - (($year - 2019) * 4),
                    'ipkLastY' => 112 - (($year - 2019) * 2),
                ],
                'capaian' => [
                    ['label' => 'Mahasiswa Aktif', 'percent' => min(98, 68 + (($year - 2019) * 3))],
                    ['label' => 'Lulusan Tepat Waktu', 'percent' => min(96, 62 + (($year - 2019) * 3))],
                    ['label' => 'Publikasi Ilmiah', 'percent' => min(95, 55 + (($year - 2019) * 4))],
                    ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => min(97, 60 + (($year - 2019) * 3))],
                ],
            ]);

            foreach (DashboardProgramItem::defaults($year) as $item) {
                DashboardProgramItem::query()->create($item);
            }

            foreach (AnnualReportSection::defaultsForYear($year) as $index => $section) {
                AnnualReportSection::query()->create([
                    'year' => $year,
                    'section_key' => $section['section_key'],
                    'title' => $section['title'],
                    'summary' => $section['summary'],
                    'content' => "[Data Uji {$year}] {$section['title']}. Program Studi mencatat perkembangan positif pada indikator mutu, kolaborasi, serta tata kelola. Fokus lanjutan adalah penguatan riset terapan, internasionalisasi kurikulum, dan digitalisasi layanan akademik.",
                    'sort_order' => $section['sort_order'],
                ]);
            }
        }

        $documentCategories = [
            'Dosen & Penelitian' => 'Dokumentasi kegiatan dosen, penelitian, dan publikasi tahun ',
            'Prestasi Mahasiswa' => 'Dokumentasi prestasi dan kegiatan unggulan mahasiswa tahun ',
            'Rencana Strategis (Renstra)' => 'Dokumen arah pengembangan program studi tahun ',
            'Rencana Operasional (Renop)' => 'Turunan Renstra untuk target kerja tahun ',
            'Standar Mutu Prodi' => 'Acuan sistem penjaminan mutu prodi tahun ',
            'Notulen Rapat Evaluasi' => 'Notulen rapat evaluasi dan tindak lanjut tahun ',
        ];

        $docSort = 1;
        foreach ($documentCategories as $category => $descriptionPrefix) {
            foreach ($years as $year) {
                DocumentItem::query()->create([
                    'title' => $category . ' Tahun ' . $year,
                    'description' => $descriptionPrefix . $year . '.',
                    'category' => $category,
                    'category_slug' => DocumentItem::slugFromCategory($category),
                    'file_url' => '/dokumen/pdf/' . DocumentItem::slugFromCategory($category),
                    'file_name' => 'Dokumen-' . $year . '-' . $docSort . '.pdf',
                    'sort_order' => $docSort,
                ]);
                $docSort++;
            }
        }

        $profileSections = [
            [
                'slug' => 'sejarah-visi-misi',
                'title' => 'Sejarah & Visi Misi',
                'summary' => 'Ringkasan arah strategis Program Studi Rekayasa Perangkat Lunak.',
                'full_content' => 'Program Studi Rekayasa Perangkat Lunak berdiri untuk menjawab kebutuhan talenta digital. Visi kami adalah menjadi pusat pendidikan rekayasa perangkat lunak yang adaptif, kolaboratif, dan berdaya saing global.',
                'icon_key' => 'book',
                'color_class' => 'blue',
                'sort_order' => 1,
            ],
            [
                'slug' => 'struktur-organisasi',
                'title' => 'Struktur Organisasi',
                'summary' => 'Struktur kerja prodi untuk pengelolaan akademik dan mutu.',
                'full_content' => 'Struktur organisasi mencakup koordinator kurikulum, koordinator kemahasiswaan, laboratorium, dan unit penjaminan mutu internal yang berkolaborasi lintas fungsi.',
                'icon_key' => 'organization',
                'color_class' => 'violet',
                'sort_order' => 2,
            ],
            [
                'slug' => 'sdm-program-studi',
                'title' => 'SDM Program Studi',
                'summary' => 'Profil dosen dan tenaga kependidikan berorientasi inovasi.',
                'full_content' => 'SDM terdiri dari dosen tetap, dosen praktisi, dan tenaga kependidikan yang didorong untuk aktif dalam penelitian, sertifikasi profesi, dan penguatan kompetensi pedagogik digital.',
                'icon_key' => 'people',
                'color_class' => 'emerald',
                'sort_order' => 3,
            ],
            [
                'slug' => 'pencapaian',
                'title' => 'Pencapaian & Prestasi',
                'summary' => 'Capaian akademik, riset, dan kompetisi tingkat nasional.',
                'full_content' => 'Prestasi mahasiswa dan dosen mencakup publikasi terindeks, hibah kompetitif, serta juara kompetisi teknologi pada tingkat nasional dan internasional.',
                'icon_key' => 'award',
                'color_class' => 'amber',
                'sort_order' => 4,
            ],
        ];

        foreach ($profileSections as $section) {
            ProfileSection::query()->create($section);
        }

        $galleryCategories = [
            'Prestasi Mahasiswa',
            'Kegiatan Akademik',
            'Kegiatan Mahasiswa',
            'Pengabdian Masyarakat',
            'Kerjasama & MoU',
        ];

        $galleryItems = [];
        $imageNo = 11;
        foreach ($galleryCategories as $category) {
            for ($i = 1; $i <= 6; $i++) {
                $galleryItems[] = [
                    'title' => $category . ' #' . $i,
                    'category' => $category,
                    'category_slug' => HomePageSetting::slugFromCategory($category),
                    'image_url' => 'https://picsum.photos/seed/' . rawurlencode(strtolower(str_replace(' ', '-', $category))) . '-' . $i . '/900/600?image=' . $imageNo,
                ];
                $imageNo++;
            }
        }

        HomePageSetting::query()->create([
            'hero_background_url' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1800&q=80',
            'hero_items' => [
                ['image_url' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1800&q=80'],
                ['image_url' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1800&q=80'],
                ['image_url' => 'https://images.unsplash.com/photo-1517486808906-6ca8b3f8e1e0?auto=format&fit=crop&w=1800&q=80'],
            ],
            'quick_highlights' => [
                [
                    'title' => 'Dosen & Penelitian',
                    'description' => 'Dokumen DTPS, penelitian, dan publikasi dosen per tahun.',
                    'link' => '/dokumen/kategori/dosen-penelitian',
                    'link_label' => 'Lihat Dokumen',
                    'icon_key' => 'users',
                    'color_key' => 'emerald',
                ],
                [
                    'title' => 'Prestasi Mahasiswa',
                    'description' => 'Dokumentasi prestasi, kompetisi, dan kegiatan unggulan mahasiswa.',
                    'link' => '/galeri/kategori/prestasi-mahasiswa',
                    'link_label' => 'Lihat Galeri',
                    'icon_key' => 'award',
                    'color_key' => 'amber',
                ],
                [
                    'title' => 'Laporan Tahunan Lengkap',
                    'description' => 'Narasi laporan tahunan per tahun dengan section dinamis dari database.',
                    'link' => '/laporan',
                    'link_label' => 'Baca Laporan',
                    'icon_key' => 'document',
                    'color_key' => 'violet',
                ],
                [
                    'title' => 'Dashboard Statistik 2019-2028',
                    'description' => 'Ringkasan data KPI, tren, dan capaian untuk sepuluh tahun pengujian.',
                    'link' => '/statistik',
                    'link_label' => 'Buka Statistik',
                    'icon_key' => 'chart',
                    'color_key' => 'blue',
                ],
            ],
            'header_logo_url' => '',
            'header_logo_label' => 'Program Studi',
            'header_title_text' => 'Portal Laporan Tahunan Program Studi [Nama Prodi]',
            'contact_email' => 'sekretariat.gk@example.ac.id',
            'contact_phone' => '(021) 5551234',
            'contact_whatsapp' => '6281234567800',
            'contact_address' => 'Gedung Teknologi Informasi Lt. 3, Kampus Pusat',
            'contact_socials' => 'Instagram · YouTube · LinkedIn',
            'contact_social_links' => [
                ['label' => 'Instagram', 'url' => 'https://instagram.com/george.prodi'],
                ['label' => 'YouTube', 'url' => 'https://youtube.com/@georgeprodi'],
                ['label' => 'LinkedIn', 'url' => 'https://linkedin.com/company/george-prodi'],
            ],
            'contact_map_embed_url' => 'https://maps.google.com/maps?q=bandung&t=&z=13&ie=UTF8&iwloc=&output=embed',
            'kaprodi_name' => 'Drs. George Kurniawan SP.d',
            'kaprodi_title' => 'Kepala Program Studi',
            'kaprodi_quote' => 'Data uji ini disusun untuk simulasi portal laporan tahunan agar seluruh fitur dinamis dapat divalidasi end-to-end.',
            'kaprodi_photo_url' => 'https://images.unsplash.com/photo-1607746882042-944635dfe10e?auto=format&fit=crop&w=220&q=80',
            'gallery_items' => $galleryItems,
        ]);
    }
}
