<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class DashboardYearStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'kpi',
        'trend',
        'capaian',
    ];

    protected $casts = [
        'kpi' => 'array',
        'trend' => 'array',
        'capaian' => 'array',
        'year' => 'integer',
    ];

    public static function defaults(): array
    {
        return [
            2025 => [
                'kpi' => [
                    ['label' => 'Mahasiswa Aktif', 'value' => 1380, 'decimals' => 0],
                    ['label' => 'IPK Rata-rata', 'value' => 3.47, 'decimals' => 2],
                    ['label' => 'Dosen Tetap', 'value' => 57, 'decimals' => 0],
                    ['label' => 'Publikasi', 'value' => 58, 'decimals' => 0],
                ],
                'trend' => [
                    'mahasiswa' => '10,130 80,110 150,90 220,65 300,45',
                    'ipk' => '10,140 80,120 150,105 220,92 300,88',
                    'mahasiswaLastY' => 45,
                    'ipkLastY' => 88,
                ],
                'capaian' => [
                    ['label' => 'Mahasiswa Aktif', 'percent' => 92],
                    ['label' => 'Lulusan Tepat Waktu', 'percent' => 81],
                    ['label' => 'Publikasi Ilmiah', 'percent' => 74],
                    ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => 88],
                ],
            ],
            2024 => [
                'kpi' => [
                    ['label' => 'Mahasiswa Aktif', 'value' => 1245, 'decimals' => 0],
                    ['label' => 'IPK Rata-rata', 'value' => 3.42, 'decimals' => 2],
                    ['label' => 'Dosen Tetap', 'value' => 54, 'decimals' => 0],
                    ['label' => 'Publikasi', 'value' => 49, 'decimals' => 0],
                ],
                'trend' => [
                    'mahasiswa' => '10,138 80,124 150,103 220,80 300,60',
                    'ipk' => '10,142 80,130 150,118 220,104 300,95',
                    'mahasiswaLastY' => 60,
                    'ipkLastY' => 95,
                ],
                'capaian' => [
                    ['label' => 'Mahasiswa Aktif', 'percent' => 86],
                    ['label' => 'Lulusan Tepat Waktu', 'percent' => 77],
                    ['label' => 'Publikasi Ilmiah', 'percent' => 68],
                    ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => 80],
                ],
            ],
            2023 => [
                'kpi' => [
                    ['label' => 'Mahasiswa Aktif', 'value' => 1110, 'decimals' => 0],
                    ['label' => 'IPK Rata-rata', 'value' => 3.36, 'decimals' => 2],
                    ['label' => 'Dosen Tetap', 'value' => 51, 'decimals' => 0],
                    ['label' => 'Publikasi', 'value' => 42, 'decimals' => 0],
                ],
                'trend' => [
                    'mahasiswa' => '10,146 80,133 150,117 220,99 300,72',
                    'ipk' => '10,145 80,137 150,127 220,116 300,104',
                    'mahasiswaLastY' => 72,
                    'ipkLastY' => 104,
                ],
                'capaian' => [
                    ['label' => 'Mahasiswa Aktif', 'percent' => 79],
                    ['label' => 'Lulusan Tepat Waktu', 'percent' => 71],
                    ['label' => 'Publikasi Ilmiah', 'percent' => 62],
                    ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => 73],
                ],
            ],
        ];
    }

    public static function ensureDefaults(): void
    {
        if (!Schema::hasTable('dashboard_year_stats')) {
            return;
        }

        if (static::query()->exists()) {
            return;
        }

        foreach (static::defaults() as $year => $payload) {
            static::query()->create([
                'year' => $year,
                'kpi' => $payload['kpi'],
                'trend' => $payload['trend'],
                'capaian' => $payload['capaian'],
            ]);
        }
    }
}
