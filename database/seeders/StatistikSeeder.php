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

/**
 * StatistikSeeder
 * ───────────────
 * Mengisi data realistis untuk tahun 2022–2026:
 *   - Mahasiswa aktif  : tumbuh dari 140 → 200 (naik bertahap tiap tahun)
 *   - IPK rata-rata    : bervariasi 3.15 – 3.62 (tiap tahun berbeda)
 *   - Dosen tetap      : tumbuh dari 8 → 15
 *   - Publikasi        : tumbuh dari 6 → 24
 *   - Data bulanan     : pola musiman akademik (tidak ada yang 0)
 *   - Program & agenda : 4 item per tahun
 *   - Dokumen & profil : lengkap
 */
class StatistikSeeder extends Seeder
{
    // ─── Data tahunan yang sudah ditetapkan (deterministik, bukan random) ───
    private const YEARLY_DATA = [
        2022 => [
            'mahasiswa' => 140,
            'ipk'       => 3.15,
            'dosen'     => 8,
            'publikasi' => 6,
            'capaian'   => [72, 65, 58, 68],
        ],
        2023 => [
            'mahasiswa' => 155,
            'ipk'       => 3.28,
            'dosen'     => 10,
            'publikasi' => 10,
            'capaian'   => [78, 70, 63, 73],
        ],
        2024 => [
            'mahasiswa' => 170,
            'ipk'       => 3.41,
            'dosen'     => 12,
            'publikasi' => 15,
            'capaian'   => [84, 76, 70, 79],
        ],
        2025 => [
            'mahasiswa' => 185,
            'ipk'       => 3.52,
            'dosen'     => 14,
            'publikasi' => 20,
            'capaian'   => [89, 82, 75, 85],
        ],
        2026 => [
            'mahasiswa' => 200,
            'ipk'       => 3.62,
            'dosen'     => 15,
            'publikasi' => 24,
            'capaian'   => [93, 87, 80, 90],
        ],
    ];

    // Pola musiman bulanan (indeks 0 = Jan … 11 = Des)
    // Kurva ditulis per-indikator agar hasilnya wajar dan tidak ada 0
    private const MAHASISWA_CURVE = [0.82, 0.88, 0.91, 0.90, 0.87, 0.84, 0.83, 0.88, 0.94, 0.96, 0.98, 1.00];
    private const IPK_OFFSET      = [-0.14, -0.11, -0.08, -0.05, -0.03, -0.01, -0.09, -0.07, -0.05, -0.03, -0.01, 0.00];
    private const DOSEN_CURVE     = [0.87, 0.89, 0.91, 0.92, 0.93, 0.94, 0.94, 0.95, 0.96, 0.97, 0.98, 1.00];
    // Bobot publikasi per bulan (total harus = 1.00), minimum menghasilkan ≥ 1
    private const PUB_WEIGHTS     = [0.05, 0.06, 0.09, 0.10, 0.10, 0.11, 0.07, 0.07, 0.09, 0.10, 0.09, 0.07];

    public function run(): void
    {
        // ── 1. Bersihkan tabel yang akan diisi ulang ──────────────────────
        DashboardMonthlyStat::query()->delete();
        DashboardYearStat::query()->delete();
        DashboardProgramItem::query()->delete();
        AnnualReportSection::query()->delete();
        DocumentItem::query()->delete();
        ProfileSection::query()->delete();
        HomePageSetting::query()->delete();

        // ── 2. Data tahunan + bulanan ─────────────────────────────────────
        foreach (self::YEARLY_DATA as $year => $data) {

            // Generate trend polyline dari data bulanan secara nyata
            $trend = $this->buildTrendPolylines($data);

            DashboardYearStat::query()->create([
                'year'    => $year,
                'kpi'     => [
                    ['label' => 'Mahasiswa Aktif', 'value' => $data['mahasiswa'], 'decimals' => 0],
                    ['label' => 'IPK Rata-rata',   'value' => $data['ipk'],       'decimals' => 2],
                    ['label' => 'Dosen Tetap',      'value' => $data['dosen'],     'decimals' => 0],
                    ['label' => 'Publikasi',        'value' => $data['publikasi'], 'decimals' => 0],
                ],
                'trend'   => $trend,
                'capaian' => [
                    ['label' => 'Mahasiswa Aktif',            'percent' => $data['capaian'][0]],
                    ['label' => 'Lulusan Tepat Waktu',        'percent' => $data['capaian'][1]],
                    ['label' => 'Publikasi Ilmiah',           'percent' => $data['capaian'][2]],
                    ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => $data['capaian'][3]],
                ],
            ]);

            // Data 12 bulan
            $this->seedMonthly($year, $data);

            // Program & agenda
            $this->seedPrograms($year);

            // Laporan tahunan
            foreach (AnnualReportSection::defaultsForYear($year) as $section) {
                AnnualReportSection::query()->create([
                    'year'        => $year,
                    'section_key' => $section['section_key'],
                    'title'       => $section['title'],
                    'summary'     => $section['summary'],
                    'content'     => 'Konten laporan tahunan ' . $year . ': ' . $section['title'] . '.',
                    'sort_order'  => $section['sort_order'],
                ]);
            }
        }

        // ── 3. Dokumen ────────────────────────────────────────────────────
        $this->seedDocuments();

        // ── 4. Profil program studi ───────────────────────────────────────
        foreach (ProfileSection::defaults() as $section) {
            ProfileSection::query()->create($section);
        }

        // ── 5. Pengaturan halaman beranda ─────────────────────────────────
        $this->seedHomePage();

        // ── 6. Admin user ─────────────────────────────────────────────────
        User::query()->updateOrCreate(
            ['email' => (string) env('ADMIN_EMAIL', 'admin@prodi.local')],
            [
                'name'     => (string) env('ADMIN_NAME', 'Admin Prodi'),
                'password' => (string) env('ADMIN_PASSWORD', 'admin123'),
            ]
        );

        $this->command->info('✓ StatistikSeeder selesai — ' . count(self::YEARLY_DATA) . ' tahun (2022–2026).');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────

    private function seedMonthly(int $year, array $data): void
    {
        $targetMhs  = $data['mahasiswa'];
        $targetIpk  = $data['ipk'];
        $targetDosen = $data['dosen'];
        $targetPub  = $data['publikasi'];

        for ($month = 1; $month <= 12; $month++) {
            $idx = $month - 1;

            // Mahasiswa: kurva musiman, minimum 1
            $mhs = max(1, (int) round($targetMhs * self::MAHASISWA_CURVE[$idx]));

            // IPK: offset per semester, floor 2.80
            $ipk = round(max(2.80, $targetIpk + self::IPK_OFFSET[$idx]), 2);

            // Dosen: tumbuh stabil, minimum 1
            $dosen = max(1, (int) round($targetDosen * self::DOSEN_CURVE[$idx]));

            // Publikasi per bulan: bobot × total, minimum 1
            $pub = max(1, (int) round($targetPub * self::PUB_WEIGHTS[$idx]));

            DashboardMonthlyStat::query()->create([
                    'year'  => $year,
                    'month' => $month,
                    'kpi'   => [
                        'mahasiswa_aktif' => $mhs,
                        'ipk'             => $ipk,
                        'dosen_tetap'     => $dosen,
                        'publikasi'       => $pub,
                    ],
                ]);
        }
    }

    private function seedPrograms(int $year): void
    {
        $programs = [
            [
                'type'             => 'Program',
                'title'            => 'Kelas Kolaboratif Industri',
                'description'      => 'Perluasan project-based learning bersama mitra strategis nasional tahun ' . $year . '.',
                'style_key'        => 'blue',
                'execution_status' => 'terlaksana',
                'sort_order'       => 1,
            ],
            [
                'type'             => 'Program',
                'title'            => 'Pusat Riset Mahasiswa',
                'description'      => 'Skema pembinaan riset dan publikasi mahasiswa tingkat akhir tahun ' . $year . '.',
                'style_key'        => 'violet',
                'execution_status' => 'terlaksana',
                'sort_order'       => 2,
            ],
            [
                'type'             => 'Agenda',
                'title'            => 'Forum Evaluasi Semester ' . $year,
                'description'      => 'Audit capaian indikator kinerja dan tindak lanjut mutu akademik.',
                'style_key'        => 'amber',
                'execution_status' => $year < 2026 ? 'terlaksana' : 'belum_terlaksana',
                'sort_order'       => 3,
            ],
            [
                'type'             => 'Agenda',
                'title'            => 'Seminar Internasional Prodi ' . $year,
                'description'      => 'Kolaborasi riset dosen dan mahasiswa dengan institusi luar negeri.',
                'style_key'        => 'rose',
                'execution_status' => $year < 2025 ? 'terlaksana' : 'belum_terlaksana',
                'sort_order'       => 4,
            ],
        ];

        foreach ($programs as $item) {
            DashboardProgramItem::query()->create(array_merge(['year' => $year], $item));
        }
    }

    /**
     * Build actual SVG polyline strings from seasonal monthly data
     * so the trend column reflects real values, not static dummy.
     */
    private function buildTrendPolylines(array $data): array
    {
        $xStart = 34.0;
        $xEnd   = 310.0;
        $yTop   = 20.0;
        $yBot   = 128.0;

        $mhsVals  = [];
        $ipkVals  = [];
        $dosenVals = [];
        $pubVals  = [];

        for ($m = 1; $m <= 12; $m++) {
            $idx = $m - 1;
            $mhsVals[]  = max(1, (int) round($data['mahasiswa'] * self::MAHASISWA_CURVE[$idx]));
            $ipkVals[]  = round(max(2.80, $data['ipk'] + self::IPK_OFFSET[$idx]), 2);
            $dosenVals[] = max(1, (int) round($data['dosen'] * self::DOSEN_CURVE[$idx]));
            $pubVals[]  = max(1, (int) round($data['publikasi'] * self::PUB_WEIGHTS[$idx]));
        }

        $buildLine = function (array $vals) use ($xStart, $xEnd, $yTop, $yBot): array {
            $min = min($vals);
            $max = max($vals);
            if ($max === $min) { $max = $min + 1; }
            $step = ($xEnd - $xStart) / 11;
            $pts  = [];
            foreach ($vals as $i => $v) {
                $x = round($xStart + ($i * $step), 1);
                $y = round($yBot - (($v - $min) / ($max - $min)) * ($yBot - $yTop), 1);
                $pts[] = $x . ',' . $y;
            }
            return [$pts, (float) end($pts) !== false ? (float) explode(',', end($pts))[1] : 74.0];
        };

        [$mhsPts,   $mhsLastY]   = $buildLine($mhsVals);
        [$ipkPts,   $ipkLastY]   = $buildLine($ipkVals);
        [$dosenPts, $dosenLastY] = $buildLine($dosenVals);
        [$pubPts,   $pubLastY]   = $buildLine($pubVals);

        return [
            'mahasiswa'      => implode(' ', $mhsPts),
            'ipk'            => implode(' ', $ipkPts),
            'dosen'          => implode(' ', $dosenPts),
            'publikasi'      => implode(' ', $pubPts),
            'mahasiswaLastY' => $mhsLastY,
            'ipkLastY'       => $ipkLastY,
            'dosenLastY'     => $dosenLastY,
            'publikasiLastY' => $pubLastY,
        ];
    }

    private function seedDocuments(): void
    {
        $categories = [
            'Dosen & Penelitian'  => 'Dokumen DTPS, penelitian, dan publikasi',
            'Prestasi Mahasiswa'  => 'Dokumentasi prestasi dan kegiatan unggulan mahasiswa',
            'Dokumen Tahunan'     => 'Laporan tahunan program studi',
        ];

        $sort = 1;
        foreach ($categories as $kategori => $desc) {
            foreach (array_keys(self::YEARLY_DATA) as $year) {
                DocumentItem::query()->create([
                    'title'         => $kategori . ' ' . $year,
                    'description'   => $desc . ' tahun ' . $year . '.',
                    'category'      => $kategori,
                    'category_slug' => DocumentItem::slugFromCategory($kategori),
                    'file_url'      => '/dokumen/pdf/' . DocumentItem::slugFromCategory($kategori) . '-' . $year,
                    'file_name'     => str_replace(' ', '-', $kategori) . '-' . $year . '.pdf',
                    'sort_order'    => $sort++,
                ]);
            }
        }
    }

    private function seedHomePage(): void
    {
        $galleryItems = [];

        // Per-category descriptions for realistic seeded data
        $categoryDescriptions = [
            'Prestasi Mahasiswa' => [
                'Tim mahasiswa meraih juara pertama dalam kompetisi inovasi nasional.',
                'Delegasi prodi berhasil menjadi finalis PKM tingkat nasional.',
                'Mahasiswa berprestasi menerima penghargaan akademik terbaik semester ini.',
                'Juara lomba karya ilmiah tingkat provinsi diraih tim mahasiswa.',
            ],
            'Kegiatan Akademik' => [
                'Seminar internasional menghadirkan narasumber dari berbagai universitas terkemuka.',
                'Workshop pengembangan kurikulum bersama praktisi industri dan akademisi.',
                'Kuliah umum dengan pakar bidang teknologi informasi dari luar negeri.',
                'Diskusi panel dosen dan mahasiswa tentang tren riset terkini.',
            ],
            'Kegiatan Mahasiswa' => [
                'Kegiatan pengembangan soft skill dan kepemimpinan mahasiswa baru.',
                'Expo karya mahasiswa menampilkan proyek akhir semester terbaik.',
                'Kegiatan sosial dan bakti masyarakat di lingkungan sekitar kampus.',
                'Orientasi studi dan pengenalan kampus bagi mahasiswa angkatan baru.',
            ],
            'Pengabdian Masyarakat' => [
                'Program pelatihan teknologi digital untuk pelaku UMKM di daerah.',
                'Kegiatan literasi digital bagi masyarakat umum bersama dosen dan mahasiswa.',
                'Penyuluhan kesehatan dan lingkungan di komunitas sekitar kampus.',
                'Pendampingan usaha mikro oleh tim dosen dan mahasiswa relawan.',
            ],
            'Kerjasama & MoU' => [
                'Penandatanganan MoU dengan perusahaan teknologi nasional untuk magang mahasiswa.',
                'Kunjungan delegasi industri dalam rangka studi banding dan kolaborasi.',
                'Forum diskusi kemitraan strategis bersama alumni dan mitra industri.',
                'Peluncuran program dual-degree dengan universitas mitra luar negeri.',
            ],
        ];

        $seed = 400;
        foreach ($categoryDescriptions as $category => $descriptions) {
            foreach ($descriptions as $i => $desc) {
                $num = $i + 1;
                $galleryItems[] = [
                    'title'         => $category . ' #' . $num,
                    'description'   => $desc,
                    'category'      => $category,
                    'category_slug' => HomePageSetting::slugFromCategory($category),
                    'image_url'     => 'https://picsum.photos/seed/s' . $seed . '/900/600',
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
            'quick_highlights'     => HomePageSetting::defaults()['quick_highlights'],
            'header_logo_url'      => '',
            'header_logo_label'    => 'Program Studi',
            'header_title_text'    => 'Laporan Tahunan',
            'contact_email'        => 'sekretariat@prodi.ac.id',
            'contact_phone'        => '(021) 1234567',
            'contact_whatsapp'     => '6281234567890',
            'contact_address'      => 'Gedung Program Studi, Kampus Utama',
            'contact_socials'      => 'Instagram · YouTube · LinkedIn',
            'contact_social_links' => [
                ['label' => 'Instagram', 'url' => 'https://instagram.com/prodi'],
                ['label' => 'YouTube',   'url' => 'https://youtube.com/@prodi'],
                ['label' => 'LinkedIn',  'url' => 'https://linkedin.com/company/prodi'],
            ],
            'contact_map_embed_url' => 'https://maps.google.com/maps?q=jakarta&t=&z=13&ie=UTF8&iwloc=&output=embed',
            'kaprodi_name'          => 'Dr. Asep George, S.Pd.I, S.Kom',
            'kaprodi_title'         => 'Kepala Program Studi',
            'kaprodi_quote'         => 'Kami berkomitmen menghasilkan lulusan yang kompeten, berkarakter, dan berdaya saing global.',
            'kaprodi_photo_url'     => 'https://images.unsplash.com/photo-1607746882042-944635dfe10e?auto=format&fit=crop&w=220&q=80',
            'gallery_items'         => $galleryItems,
        ]);
    }
}
