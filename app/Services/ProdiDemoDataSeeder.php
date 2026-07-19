<?php

namespace App\Services;

use App\Models\AnnualReportSection;
use App\Models\DashboardMonthlyStat;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use App\Models\DocumentItem;
use App\Models\HomePageSetting;
use App\Models\Prodi;
use App\Models\ProfileSection;

class ProdiDemoDataSeeder
{
    private const YEARLY_DATA = [
        2022 => ['mahasiswa' => 140, 'ipk' => 3.15, 'dosen' => 8,  'publikasi' => 6,  'capaian' => [72, 65, 58, 68]],
        2023 => ['mahasiswa' => 155, 'ipk' => 3.28, 'dosen' => 10, 'publikasi' => 10, 'capaian' => [78, 70, 63, 73]],
        2024 => ['mahasiswa' => 170, 'ipk' => 3.41, 'dosen' => 12, 'publikasi' => 15, 'capaian' => [84, 76, 70, 79]],
        2025 => ['mahasiswa' => 185, 'ipk' => 3.52, 'dosen' => 14, 'publikasi' => 20, 'capaian' => [89, 82, 75, 85]],
        2026 => ['mahasiswa' => 200, 'ipk' => 3.62, 'dosen' => 15, 'publikasi' => 24, 'capaian' => [93, 87, 80, 90]],
    ];

    private const MAHASISWA_CURVE = [0.82, 0.88, 0.91, 0.90, 0.87, 0.84, 0.83, 0.88, 0.94, 0.96, 0.98, 1.00];
    private const IPK_OFFSET = [-0.14, -0.11, -0.08, -0.05, -0.03, -0.01, -0.09, -0.07, -0.05, -0.03, -0.01, 0.00];
    private const DOSEN_CURVE = [0.87, 0.89, 0.91, 0.92, 0.93, 0.94, 0.94, 0.95, 0.96, 0.97, 0.98, 1.00];
    private const PUB_WEIGHTS = [0.05, 0.06, 0.09, 0.10, 0.10, 0.11, 0.07, 0.07, 0.09, 0.10, 0.09, 0.07];

    public function seed(Prodi $prodi, ?string $kaprodiName = null, bool $replace = true): void
    {
        if ($replace) {
            $this->clearProdiData($prodi);
        }

        $profile = $this->profileFor($prodi, $kaprodiName);

        foreach (self::YEARLY_DATA as $year => $baseData) {
            $yearData = $this->buildYearData($baseData, $profile);

            DashboardYearStat::query()->updateOrCreate(
                ['prodi_id' => $prodi->id, 'year' => $year],
                [
                    'kpi' => [
                        ['label' => 'Mahasiswa Aktif', 'value' => $yearData['mahasiswa'], 'decimals' => 0],
                        ['label' => 'IPK Rata-rata', 'value' => $yearData['ipk'], 'decimals' => 2],
                        ['label' => 'Dosen Tetap', 'value' => $yearData['dosen'], 'decimals' => 0],
                        ['label' => 'Publikasi', 'value' => $yearData['publikasi'], 'decimals' => 0],
                    ],
                    'trend' => $this->buildTrendPolylines($yearData),
                    'capaian' => [
                        ['label' => 'Mahasiswa Aktif', 'percent' => $yearData['capaian'][0]],
                        ['label' => 'Lulusan Tepat Waktu', 'percent' => $yearData['capaian'][1]],
                        ['label' => 'Publikasi Ilmiah', 'percent' => $yearData['capaian'][2]],
                        ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => $yearData['capaian'][3]],
                    ],
                ]
            );

            $this->seedMonthly($prodi->id, $year, $yearData);
            $this->seedPrograms($prodi->id, $year, $prodi, $profile);
            $this->seedAnnualReport($prodi->id, $year, $prodi, $profile);
        }

        $this->seedDocuments($prodi->id, $prodi, $profile);
        $this->seedProfileSections($prodi->id, $prodi, $profile);
        $this->seedHomePage($prodi->id, $prodi, $profile);
    }

    private function clearProdiData(Prodi $prodi): void
    {
        DashboardMonthlyStat::query()->where('prodi_id', $prodi->id)->delete();
        DashboardYearStat::query()->where('prodi_id', $prodi->id)->delete();
        DashboardProgramItem::query()->where('prodi_id', $prodi->id)->delete();
        AnnualReportSection::query()->where('prodi_id', $prodi->id)->delete();
        DocumentItem::query()->where('prodi_id', $prodi->id)->delete();
        ProfileSection::query()->where('prodi_id', $prodi->id)->delete();
        HomePageSetting::query()->where('prodi_id', $prodi->id)->delete();
    }

    private function profileFor(Prodi $prodi, ?string $kaprodiName): array
    {
        $code = strtoupper($prodi->code);

        $profiles = [
            'SI' => [
                'mahasiswa_offset' => 45, 'ipk_offset' => -0.01, 'dosen_offset' => 2, 'publikasi_offset' => 6,
                'capaian_offset' => [3, 4, 2, 5],
                'kaprodi' => 'Rina Kusumawati',
                'focus' => ['Transformasi Sistem Bisnis', 'Laboratorium Analitik Data', 'Audit Tata Kelola Digital', 'Forum Enterprise Architecture'],
                'theme' => 'sistem informasi, tata kelola digital, analitik data, dan proses bisnis',
            ],
            'IF' => [
                'mahasiswa_offset' => 95, 'ipk_offset' => -0.08, 'dosen_offset' => 11, 'publikasi_offset' => 28,
                'capaian_offset' => [7, 1, 14, 11],
                'kaprodi' => 'Andi Pratama',
                'focus' => ['Riset Kecerdasan Artifisial', 'Laboratorium Keamanan Siber', 'Evaluasi Kurikulum Komputasi', 'Forum Inovasi Perangkat Lunak'],
                'theme' => 'rekayasa perangkat lunak, kecerdasan artifisial, keamanan siber, dan komputasi',
            ],
            'EKONOMI' => [
                'mahasiswa_offset' => 18, 'ipk_offset' => 0.10, 'dosen_offset' => 0, 'publikasi_offset' => 38,
                'capaian_offset' => [1, 11, 16, 4],
                'kaprodi' => 'Maya Sari',
                'focus' => ['Inkubasi Wirausaha Mahasiswa', 'Klinik Akuntansi dan Pajak', 'Evaluasi Kurikulum Ekonomi Digital', 'Forum UMKM dan Koperasi'],
                'theme' => 'ekonomi digital, kewirausahaan, akuntansi, perpajakan, dan koperasi',
            ],
            'MJ' => [
                'mahasiswa_offset' => 72, 'ipk_offset' => 0.04, 'dosen_offset' => 6, 'publikasi_offset' => 18,
                'capaian_offset' => [5, 8, 9, 13],
                'kaprodi' => 'Dewi Lestari',
                'focus' => ['Business Leadership Camp', 'Laboratorium Riset Pemasaran', 'Sertifikasi Manajemen Proyek', 'Forum Industri dan Kewirausahaan'],
                'theme' => 'manajemen bisnis, kepemimpinan, pemasaran, kewirausahaan, dan pengembangan organisasi',
            ],
        ];

        if (! isset($profiles[$code])) {
            $signature = abs(crc32($code));
            $profiles[$code] = [
                'mahasiswa_offset' => 25 + ($signature % 70),
                'ipk_offset' => (($signature % 17) - 8) / 100,
                'dosen_offset' => 1 + ($signature % 8),
                'publikasi_offset' => 5 + ($signature % 24),
                'capaian_offset' => [$signature % 6, ($signature >> 2) % 8, ($signature >> 4) % 10, ($signature >> 6) % 7],
                'kaprodi' => trim((string) $kaprodiName) ?: 'Kepala Prodi '.$prodi->name,
                'focus' => ['Inovasi '.$prodi->name, 'Pusat Kajian '.$prodi->name, 'Evaluasi Mutu '.$prodi->name, 'Forum Kolaborasi '.$prodi->name],
                'theme' => 'pengembangan akademik, riset terapan, layanan mahasiswa, dan kolaborasi mitra',
            ];
        }

        $profiles[$code]['kaprodi'] = trim((string) $kaprodiName) ?: $profiles[$code]['kaprodi'];

        return $profiles[$code];
    }

    private function buildYearData(array $baseData, array $profile): array
    {
        return [
            'mahasiswa' => $baseData['mahasiswa'] + $profile['mahasiswa_offset'],
            'ipk' => round(min(3.95, max(2.75, $baseData['ipk'] + $profile['ipk_offset'])), 2),
            'dosen' => $baseData['dosen'] + $profile['dosen_offset'],
            'publikasi' => $baseData['publikasi'] + $profile['publikasi_offset'],
            'capaian' => collect($baseData['capaian'])
                ->map(fn(int $value, int $index): int => min(98, $value + $profile['capaian_offset'][$index]))
                ->all(),
        ];
    }

    private function seedMonthly(int $prodiId, int $year, array $data): void
    {
        for ($month = 1; $month <= 12; $month++) {
            $idx = $month - 1;

            DashboardMonthlyStat::query()->updateOrCreate(
                ['prodi_id' => $prodiId, 'year' => $year, 'month' => $month],
                [
                    'kpi' => [
                        'mahasiswa_aktif' => max(1, (int) round($data['mahasiswa'] * self::MAHASISWA_CURVE[$idx])),
                        'ipk' => round(max(2.80, $data['ipk'] + self::IPK_OFFSET[$idx]), 2),
                        'dosen_tetap' => max(1, (int) round($data['dosen'] * self::DOSEN_CURVE[$idx])),
                        'publikasi' => max(1, (int) round($data['publikasi'] * self::PUB_WEIGHTS[$idx])),
                    ],
                ]
            );
        }
    }

    private function seedPrograms(int $prodiId, int $year, Prodi $prodi, array $profile): void
    {
        $programs = [
            ['type' => 'Program', 'title' => $profile['focus'][0].' '.$year, 'description' => 'Program unggulan '.$prodi->name.' untuk penguatan '.$profile['theme'].' tahun '.$year.'.', 'style_key' => 'blue', 'execution_status' => 'terlaksana', 'sort_order' => 1],
            ['type' => 'Program', 'title' => $profile['focus'][1].' '.$year, 'description' => 'Kegiatan riset, praktik, dan pendampingan mahasiswa pada bidang '.$profile['theme'].'.', 'style_key' => 'violet', 'execution_status' => 'terlaksana', 'sort_order' => 2],
            ['type' => 'Agenda', 'title' => $profile['focus'][2].' '.$year, 'description' => 'Agenda evaluasi capaian akademik dan tindak lanjut mutu internal '.$prodi->name.'.', 'style_key' => 'amber', 'execution_status' => $year < 2026 ? 'terlaksana' : 'belum_terlaksana', 'sort_order' => 3],
            ['type' => 'Agenda', 'title' => $profile['focus'][3].' '.$year, 'description' => 'Forum kolaborasi dosen, mahasiswa, alumni, dan mitra eksternal '.$prodi->name.'.', 'style_key' => 'rose', 'execution_status' => $year < 2025 ? 'terlaksana' : 'belum_terlaksana', 'sort_order' => 4],
        ];

        foreach ($programs as $item) {
            DashboardProgramItem::query()->create(array_merge(['prodi_id' => $prodiId, 'year' => $year], $item));
        }
    }

    private function seedAnnualReport(int $prodiId, int $year, Prodi $prodi, array $profile): void
    {
        foreach (AnnualReportSection::defaultsForYear($year) as $section) {
            AnnualReportSection::query()->updateOrCreate(
                ['prodi_id' => $prodiId, 'year' => $year, 'section_key' => $section['section_key']],
                [
                    'title' => $section['title'],
                    'summary' => $section['summary'],
                    'content' => $section['title'].' '.$prodi->name.' tahun '.$year.' menekankan '.$profile['theme'].'. Data ini disiapkan sebagai contoh awal yang bisa disesuaikan Kaprodi.',
                    'sort_order' => $section['sort_order'],
                ]
            );
        }
    }

    private function seedDocuments(int $prodiId, Prodi $prodi, array $profile): void
    {
        $categories = [
            'Dosen & Penelitian' => 'Dokumen DTPS, penelitian, dan publikasi',
            'Prestasi Mahasiswa' => 'Dokumentasi prestasi dan kegiatan unggulan mahasiswa',
            'Dokumen Tahunan' => 'Laporan tahunan program studi',
        ];

        $sort = 1;
        foreach ($categories as $category => $description) {
            foreach (array_keys(self::YEARLY_DATA) as $year) {
                DocumentItem::query()->create([
                    'prodi_id' => $prodiId,
                    'title' => $category.' '.$prodi->code.' '.$year,
                    'description' => $description.' '.$prodi->name.' bidang '.$profile['theme'].' tahun '.$year.'.',
                    'category' => $category,
                    'category_slug' => DocumentItem::slugFromCategory($category),
                    'file_url' => '/dokumen/pdf/'.strtolower($prodi->code).'-'.DocumentItem::slugFromCategory($category).'-'.$year,
                    'file_name' => $prodi->code.'-'.str_replace(' ', '-', $category).'-'.$year.'.pdf',
                    'sort_order' => $sort++,
                ]);
            }
        }
    }

    private function seedProfileSections(int $prodiId, Prodi $prodi, array $profile): void
    {
        foreach (ProfileSection::defaults() as $section) {
            ProfileSection::query()->updateOrCreate(
                ['prodi_id' => $prodiId, 'slug' => $section['slug']],
                array_merge($section, [
                    'summary' => $section['summary'].' '.$prodi->name.'.',
                    'full_content' => $section['full_content'].' Profil '.$prodi->name.' berfokus pada '.$profile['theme'].'.',
                ])
            );
        }
    }

    private function seedHomePage(int $prodiId, Prodi $prodi, array $profile): void
    {
        $codeSlug = strtolower($prodi->code);
        $galleryItems = [];
        $categories = [
            'Prestasi Mahasiswa' => ['Capaian kompetisi mahasiswa', 'Finalis karya ilmiah', 'Penghargaan akademik', 'Expo karya mahasiswa'],
            'Kegiatan Akademik' => ['Kuliah umum pakar', 'Workshop kurikulum', 'Seminar dosen mahasiswa', 'Diskusi riset terapan'],
            'Pengabdian Masyarakat' => ['Pendampingan komunitas', 'Pelatihan mitra lokal', 'Literasi dan edukasi publik', 'Program bina desa'],
            'Kerjasama & MoU' => ['Penandatanganan MoU', 'Kunjungan industri', 'Forum alumni dan mitra', 'Program magang terstruktur'],
        ];

        $seed = abs(crc32($prodi->code)) % 900;
        foreach ($categories as $category => $titles) {
            foreach ($titles as $index => $title) {
                $galleryItems[] = [
                    'title' => $title.' '.$prodi->code,
                    'description' => 'Dokumentasi '.$prodi->name.' terkait '.$profile['theme'].'.',
                    'category' => $category,
                    'category_slug' => HomePageSetting::slugFromCategory($category),
                    'image_url' => 'https://picsum.photos/seed/'.$codeSlug.'-gallery-'.($seed + $index).'/900/600',
                ];
            }
        }

        HomePageSetting::query()->updateOrCreate(
            ['prodi_id' => $prodiId],
            [
                'hero_background_url' => 'https://picsum.photos/seed/'.$codeSlug.'-hero-main/1800/900',
                'hero_items' => [
                    ['image_url' => 'https://picsum.photos/seed/'.$codeSlug.'-hero-1/1800/900'],
                    ['image_url' => 'https://picsum.photos/seed/'.$codeSlug.'-hero-2/1800/900'],
                    ['image_url' => 'https://picsum.photos/seed/'.$codeSlug.'-hero-3/1800/900'],
                ],
                'quick_highlights' => collect(HomePageSetting::defaults()['quick_highlights'])->map(function (array $item) use ($prodi): array {
                    $item['description'] .= ' Data khusus '.$prodi->name.'.';

                    return $item;
                })->all(),
                'header_logo_url' => $this->logoUrl($prodi),
                'header_logo_label' => $prodi->name,
                'header_title_text' => 'Laporan Tahunan Program Studi '.$prodi->name,
                'contact_email' => 'sekretariat.'.strtolower($prodi->code).'@unwari.ac.id',
                'contact_phone' => '(022) 700'.str_pad((string) ($seed % 9999), 4, '0', STR_PAD_LEFT),
                'contact_whatsapp' => '62812000'.str_pad((string) ($seed % 9999), 4, '0', STR_PAD_LEFT),
                'contact_address' => 'Ruang '.$prodi->code.', Gedung Program Studi Universitas Winaya Mukti',
                'contact_socials' => 'Instagram - YouTube - LinkedIn',
                'contact_social_links' => [
                    ['label' => 'Instagram', 'url' => 'https://instagram.com/'.$codeSlug.'.unwari'],
                    ['label' => 'YouTube', 'url' => 'https://youtube.com/@'.$codeSlug.'unwari'],
                    ['label' => 'LinkedIn', 'url' => 'https://linkedin.com/company/'.$codeSlug.'-unwari'],
                ],
                'contact_map_embed_url' => 'https://maps.google.com/maps?q=Universitas+Winaya+Mukti+'.$prodi->code.'&output=embed',
                'kaprodi_name' => $profile['kaprodi'],
                'kaprodi_title' => 'Kepala Program Studi',
                'kaprodi_quote' => 'Program Studi '.$prodi->name.' berkomitmen menguatkan '.$profile['theme'].' melalui budaya mutu dan kolaborasi berkelanjutan.',
                'kaprodi_photo_url' => 'https://picsum.photos/seed/'.$codeSlug.'-kaprodi/400/400',
                'gallery_items' => $galleryItems,
            ]
        );
    }

    private function buildTrendPolylines(array $data): array
    {
        $buildLine = function (array $vals): array {
            $min = min($vals);
            $max = max($vals);
            if ($max === $min) {
                $max = $min + 1;
            }

            $points = [];
            foreach ($vals as $i => $v) {
                $x = round(34 + ($i * ((310 - 34) / 11)), 1);
                $y = round(128 - (($v - $min) / ($max - $min)) * (128 - 20), 1);
                $points[] = $x.','.$y;
            }

            return [$points, (float) explode(',', end($points))[1]];
        };

        $mhs = $ipk = $dosen = $pub = [];
        for ($month = 1; $month <= 12; $month++) {
            $idx = $month - 1;
            $mhs[] = max(1, (int) round($data['mahasiswa'] * self::MAHASISWA_CURVE[$idx]));
            $ipk[] = round(max(2.80, $data['ipk'] + self::IPK_OFFSET[$idx]), 2);
            $dosen[] = max(1, (int) round($data['dosen'] * self::DOSEN_CURVE[$idx]));
            $pub[] = max(1, (int) round($data['publikasi'] * self::PUB_WEIGHTS[$idx]));
        }

        [$mhsPoints, $mhsLastY] = $buildLine($mhs);
        [$ipkPoints, $ipkLastY] = $buildLine($ipk);
        [$dosenPoints, $dosenLastY] = $buildLine($dosen);
        [$pubPoints, $pubLastY] = $buildLine($pub);

        return [
            'mahasiswa' => implode(' ', $mhsPoints),
            'ipk' => implode(' ', $ipkPoints),
            'dosen' => implode(' ', $dosenPoints),
            'publikasi' => implode(' ', $pubPoints),
            'mahasiswaLastY' => $mhsLastY,
            'ipkLastY' => $ipkLastY,
            'dosenLastY' => $dosenLastY,
            'publikasiLastY' => $pubLastY,
        ];
    }

    private function logoUrl(Prodi $prodi): string
    {
        return match (strtoupper($prodi->code)) {
            'SI' => '/logos/prodi-si.svg',
            'IF' => '/logos/prodi-if.svg',
            'EKONOMI' => '/logos/prodi-ekonomi.svg',
            'MJ' => '/logos/prodi-mj.svg',
            default => '/logos/prodi-default.svg',
        };
    }
}
