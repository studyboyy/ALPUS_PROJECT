<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class DashboardProgramItem extends Model
{
    protected $fillable = [
        'year',
        'type',
        'title',
        'description',
        'style_key',
        'sort_order',
    ];

    protected $casts = [
        'year' => 'integer',
        'sort_order' => 'integer',
    ];

    public static function defaults(int $year = 2025): array
    {
        return [
            [
                'year' => $year,
                'type' => 'Program',
                'title' => 'Kelas Kolaboratif Industri',
                'description' => 'Perluasan project-based learning bersama mitra strategis nasional.',
                'style_key' => 'blue',
                'sort_order' => 1,
            ],
            [
                'year' => $year,
                'type' => 'Program',
                'title' => 'Pusat Riset Mahasiswa',
                'description' => 'Skema pembinaan riset dan publikasi mahasiswa tingkat akhir.',
                'style_key' => 'violet',
                'sort_order' => 2,
            ],
            [
                'year' => $year,
                'type' => 'Agenda',
                'title' => 'Forum Evaluasi Semester',
                'description' => 'Agenda tahunan ' . $year . ' untuk audit capaian indikator dan tindak lanjut mutu.',
                'style_key' => 'amber',
                'sort_order' => 3,
            ],
            [
                'year' => $year,
                'type' => 'Agenda',
                'title' => 'Seminar Internasional Prodi',
                'description' => 'Agenda strategis tahun ' . $year . ' untuk kolaborasi riset dosen dan mahasiswa.',
                'style_key' => 'rose',
                'sort_order' => 4,
            ],
        ];
    }

    public static function ensureDefaults(): void
    {
        if (!Schema::hasTable('dashboard_program_items')) {
            return;
        }

        if (Schema::hasTable('dashboard_year_stats')) {
            $years = DashboardYearStat::query()->pluck('year')->all();
            foreach ($years as $year) {
                static::ensureYear((int) $year);
            }

            return;
        }

        static::query()->insert(static::defaults());
    }

    public static function ensureYear(int $year): void
    {
        if (!Schema::hasTable('dashboard_program_items')) {
            return;
        }

        if (static::query()->where('year', $year)->exists()) {
            return;
        }

        static::query()->insert(static::defaults($year));
    }
}
