<?php

namespace Database\Seeders;

use App\Models\AnnualReportSection;
use App\Models\DashboardMonthlyStat;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use App\Models\DocumentItem;
use App\Models\HomePageSetting;
use App\Models\ProfileSection;
use App\Models\User;
use Illuminate\Database\Seeder;

class PortalFinalTestingSeeder extends Seeder
{
    public function run(): void
    {
        $years = range(2019, 2028);

        DashboardProgramItem::query()->delete();
        AnnualReportSection::query()->delete();
        DashboardMonthlyStat::query()->delete();
        DashboardYearStat::query()->delete();
        DocumentItem::query()->delete();
        ProfileSection::query()->delete();
        HomePageSetting::query()->delete();

        $mahasiswa = 1000;
        $ipk = 3.05;
        $dosen = 80;
        $publikasi = 120;

        foreach ($years as $year) {
            $mahasiswa += random_int(100, 200);
            $dosen += random_int(100, 200);
            $publikasi += random_int(100, 200);
            $ipk = min(3.95, $ipk + random_int(1, 4) / 100);

            $stat = DashboardYearStat::factory()->create([
                'year' => $year,
                'kpi' => [
                    ['label' => 'Mahasiswa Aktif', 'value' => $mahasiswa, 'decimals' => 0],
                    ['label' => 'IPK Rata-rata', 'value' => round($ipk, 2), 'decimals' => 2],
                    ['label' => 'Dosen Tetap', 'value' => $dosen, 'decimals' => 0],
                    ['label' => 'Publikasi', 'value' => $publikasi, 'decimals' => 0],
                ],
                'capaian' => [
                    ['label' => 'Mahasiswa Aktif', 'percent' => min(99, 55 + (($year - 2019) * 4))],
                    ['label' => 'Lulusan Tepat Waktu', 'percent' => min(98, 50 + (($year - 2019) * 4))],
                    ['label' => 'Publikasi Ilmiah', 'percent' => min(99, 52 + (($year - 2019) * 4))],
                    ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => min(98, 54 + (($year - 2019) * 4))],
                ],
            ]);

            DashboardMonthlyStat::ensureYear($year, $stat->kpi ?? []);

            $targetMahasiswa = (float) data_get($stat->kpi, '0.value', 0);
            $targetIpk = (float) data_get($stat->kpi, '1.value', 0);
            $targetDosen = (float) data_get($stat->kpi, '2.value', 0);
            $targetPublikasi = (float) data_get($stat->kpi, '3.value', 0);

            foreach (range(1, 12) as $month) {
                $ratio = $month / 12;
                DashboardMonthlyStat::query()->updateOrCreate(
                    ['year' => $year, 'month' => $month],
                    [
                        'kpi' => [
                            'mahasiswa_aktif' => (float) round($targetMahasiswa * (0.72 + (0.28 * $ratio))),
                            'ipk' => (float) round(max(2.7, $targetIpk - 0.18 + (0.18 * $ratio)), 2),
                            'dosen_tetap' => (float) round($targetDosen * (0.70 + (0.30 * $ratio))),
                            'publikasi' => (float) round(($targetPublikasi * $ratio) / max(1, $month)),
                        ],
                    ]
                );
            }

            foreach (DashboardProgramItem::defaults($year) as $item) {
                DashboardProgramItem::query()->create($item);
            }

            foreach (AnnualReportSection::defaultsForYear($year) as $section) {
                AnnualReportSection::query()->create([
                    'year' => $year,
                    'section_key' => $section['section_key'],
                    'title' => $section['title'],
                    'summary' => $section['summary'],
                    'content' => 'Konten uji final tahun ' . $year . ': ' . $section['title'] . '.',
                    'sort_order' => $section['sort_order'],
                ]);
            }
        }

        foreach ($years as $year) {
            $kategori = 'Dokumen Tahunan';
            DocumentItem::query()->create([
                'title' => 'Dokumen Tahunan ' . $year,
                'description' => 'Dokumen default final testing untuk tahun ' . $year . '.',
                'category' => $kategori,
                'category_slug' => DocumentItem::slugFromCategory($kategori),
                'file_url' => '/dokumen/pdf/' . DocumentItem::slugFromCategory($kategori),
                'file_name' => 'Dokumen-Tahunan-' . $year . '.pdf',
                'sort_order' => $year - 2018,
            ]);
        }

        foreach (ProfileSection::defaults() as $section) {
            ProfileSection::query()->create($section);
        }

        $galleryItems = [];
        $categories = ['Kegiatan Akademik', 'Kegiatan Mahasiswa', 'Pengabdian Masyarakat', 'Kerjasama & MoU'];
        $seed = 300;
        foreach ($categories as $category) {
            for ($i = 1; $i <= 5; $i++) {
                $galleryItems[] = [
                    'title' => $category . ' Uji #' . $i,
                    'category' => $category,
                    'category_slug' => HomePageSetting::slugFromCategory($category),
                    'image_url' => 'https://picsum.photos/seed/final-' . $seed . '/900/600',
                ];
                $seed++;
            }
        }

        HomePageSetting::query()->create([
            'hero_background_url' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1800&q=80',
            'hero_items' => [
                ['image_url' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1800&q=80'],
                ['image_url' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1800&q=80'],
                ['image_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1800&q=80'],
            ],
            'quick_highlights' => HomePageSetting::defaults()['quick_highlights'],
            'header_logo_url' => '',
            'header_logo_label' => 'Program Studi',
            'header_title_text' => 'Laporan Tahunan',
            'contact_email' => 'sekretariat@laporan.ac.id',
            'contact_phone' => '(021) 9998877',
            'contact_whatsapp' => '628111223344',
            'contact_address' => 'Gedung Pusat Data Akademik',
            'contact_socials' => 'Instagram · YouTube · LinkedIn',
            'contact_social_links' => [
                ['label' => 'Instagram', 'url' => 'https://instagram.com/laporan.tahunan'],
                ['label' => 'YouTube', 'url' => 'https://youtube.com/@laporantahunan'],
                ['label' => 'LinkedIn', 'url' => 'https://linkedin.com/company/laporan-tahunan'],
            ],
            'contact_map_embed_url' => 'https://maps.google.com/maps?q=jakarta&t=&z=13&ie=UTF8&iwloc=&output=embed',
            'kaprodi_name' => 'Dr. Asep George SPd. i, S. kom',
            'kaprodi_title' => 'Kepala Program Studi',
            'kaprodi_quote' => 'Data final testing disiapkan untuk simulasi evaluasi tahunan yang dinamis dan terukur.',
            'kaprodi_photo_url' => 'https://images.unsplash.com/photo-1607746882042-944635dfe10e?auto=format&fit=crop&w=220&q=80',
            'gallery_items' => $galleryItems,
        ]);

        User::query()->updateOrCreate(
            ['email' => 'admin@prodi.local'],
            [
                'name' => 'Admin Prodi',
                'password' => 'admin123',
            ]
        );
    }
}
