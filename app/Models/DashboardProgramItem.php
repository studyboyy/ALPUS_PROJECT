<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use App\Models\Concerns\HasProdiScope;

class DashboardProgramItem extends Model
{
    use HasProdiScope;
    protected $fillable = [
        'year',
        'prodi_id',
        'type',
        'title',
        'description',
        'style_key',
        'execution_status',
        'sort_order',
    ];

    protected $casts = [
        'year' => 'integer',
        'sort_order' => 'integer',
    ];

    public static function hasExecutionStatusColumn(): bool
    {
        return Schema::hasTable('dashboard_program_items')
            && Schema::hasColumn('dashboard_program_items', 'execution_status');
    }

    public static function defaults(int $year = 2025): array
    {
        $defaults = [
            [
                'year' => $year,
                'type' => 'Program',
                'title' => 'Kelas Kolaboratif Industri',
                'description' => 'Perluasan project-based learning bersama mitra strategis nasional.',
                'style_key' => 'blue',
                'execution_status' => 'terlaksana',
                'sort_order' => 1,
            ],
            [
                'year' => $year,
                'type' => 'Program',
                'title' => 'Pusat Riset Mahasiswa',
                'description' => 'Skema pembinaan riset dan publikasi mahasiswa tingkat akhir.',
                'style_key' => 'violet',
                'execution_status' => 'terlaksana',
                'sort_order' => 2,
            ],
            [
                'year' => $year,
                'type' => 'Agenda',
                'title' => 'Forum Evaluasi Semester',
                'description' => 'Agenda tahunan ' . $year . ' untuk audit capaian indikator dan tindak lanjut mutu.',
                'style_key' => 'amber',
                'execution_status' => 'terlaksana',
                'sort_order' => 3,
            ],
            [
                'year' => $year,
                'type' => 'Agenda',
                'title' => 'Seminar Internasional Prodi',
                'description' => 'Agenda strategis tahun ' . $year . ' untuk kolaborasi riset dosen dan mahasiswa.',
                'style_key' => 'rose',
                'execution_status' => 'belum_terlaksana',
                'sort_order' => 4,
            ],
        ];

        if (!static::hasExecutionStatusColumn()) {
            return collect($defaults)
                ->map(function (array $item): array {
                    unset($item['execution_status']);

                    return $item;
                })
                ->all();
        }

        return $defaults;
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

        foreach (static::defaults() as $payload) {
            static::query()->create($payload);
        }
    }

    public static function ensureYear(int $year): void
    {
        if (!Schema::hasTable('dashboard_program_items')) {
            return;
        }

        if (static::query()->where('year', $year)->exists()) {
            return;
        }

        foreach (static::defaults($year) as $payload) {
            static::query()->create($payload);
        }
    }
}
