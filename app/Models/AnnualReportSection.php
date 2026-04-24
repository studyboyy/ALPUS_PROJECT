<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AnnualReportSection extends Model
{
    protected $fillable = [
        'year',
        'section_key',
        'title',
        'summary',
        'content',
        'sort_order',
    ];

    protected $casts = [
        'year' => 'integer',
        'sort_order' => 'integer',
    ];

    public static function sectionBlueprints(): array
    {
        return [
            [
                'section_key' => 'ringkasan-eksekutif',
                'title' => 'Ringkasan Eksekutif',
                'summary' => 'Sorotan utama capaian tahunan program studi.',
                'content' => 'Ringkasan eksekutif tahun :year dapat diisi dengan gambaran umum capaian, tantangan, dan arah tindak lanjut Program Studi.',
                'sort_order' => 1,
            ],
            [
                'section_key' => 'capaian-kinerja-akademik',
                'title' => 'Capaian Kinerja Akademik',
                'summary' => 'Mutu proses pembelajaran, lulusan, dan indikator akademik.',
                'content' => 'Bagian ini memuat capaian kinerja akademik tahun :year, termasuk proses pembelajaran, mutu lulusan, dan evaluasi akademik.',
                'sort_order' => 2,
            ],
            [
                'section_key' => 'penelitian-pkm',
                'title' => 'Penelitian & PkM',
                'summary' => 'Riset, publikasi, dan pengabdian kepada masyarakat.',
                'content' => 'Bagian ini memuat capaian penelitian, publikasi ilmiah, dan kegiatan pengabdian kepada masyarakat pada tahun :year.',
                'sort_order' => 3,
            ],
            [
                'section_key' => 'prestasi-mahasiswa',
                'title' => 'Prestasi Mahasiswa',
                'summary' => 'Prestasi akademik dan non-akademik mahasiswa.',
                'content' => 'Bagian ini memuat prestasi mahasiswa pada tahun :year, baik akademik maupun non-akademik pada tingkat lokal, nasional, maupun internasional.',
                'sort_order' => 4,
            ],
            [
                'section_key' => 'kerjasama-kegiatan-eksternal',
                'title' => 'Kerjasama & Kegiatan Eksternal',
                'summary' => 'Kemitraan, MoU, dan kegiatan kolaboratif eksternal.',
                'content' => 'Bagian ini memuat aktivitas kerjasama dan kegiatan eksternal Program Studi pada tahun :year, termasuk mitra, implementasi, dan dampaknya.',
                'sort_order' => 5,
            ],
            [
                'section_key' => 'keuangan-anggaran',
                'title' => 'Keuangan & Anggaran',
                'summary' => 'Ringkasan penggunaan anggaran dan efisiensi program.',
                'content' => 'Bagian ini memuat gambaran umum pengelolaan keuangan dan anggaran Program Studi pada tahun :year.',
                'sort_order' => 6,
            ],
        ];
    }

    public static function defaultsForYear(int $year): array
    {
        return collect(static::sectionBlueprints())
            ->map(fn(array $item) => [
                'year' => $year,
                'section_key' => $item['section_key'],
                'title' => $item['title'],
                'summary' => str_replace(':year', (string) $year, $item['summary']),
                'content' => str_replace(':year', (string) $year, $item['content']),
                'sort_order' => $item['sort_order'],
            ])
            ->all();
    }

    public static function ensureDefaults(): void
    {
        if (!Schema::hasTable('annual_report_sections') || !Schema::hasTable('dashboard_year_stats')) {
            return;
        }

        $years = DashboardYearStat::query()->orderBy('year')->pluck('year')->all();
        foreach ($years as $year) {
            static::ensureYear((int) $year);
        }
    }

    public static function ensureYear(int $year): void
    {
        if (!Schema::hasTable('annual_report_sections')) {
            return;
        }

        foreach (static::defaultsForYear($year) as $payload) {
            static::query()->firstOrCreate(
                [
                    'year' => $year,
                    'section_key' => $payload['section_key'],
                ],
                $payload,
            );
        }
    }

    public static function forYear(int $year): Collection
    {
        return static::query()
            ->where('year', $year)
            ->orderBy('sort_order')
            ->get();
    }
}
