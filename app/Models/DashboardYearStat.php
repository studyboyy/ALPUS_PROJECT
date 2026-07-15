<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use App\Models\Concerns\HasProdiScope;

class DashboardYearStat extends Model
{
    use HasFactory;
    use HasProdiScope;

    protected $fillable = [
        'year',
        'prodi_id',
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
            2026 => [
                'kpi' => [
                    ['label' => 'Mahasiswa Aktif', 'value' => 200, 'decimals' => 0],
                    ['label' => 'IPK Rata-rata',   'value' => 3.62, 'decimals' => 2],
                    ['label' => 'Dosen Tetap',      'value' => 15,   'decimals' => 0],
                    ['label' => 'Publikasi',        'value' => 24,   'decimals' => 0],
                ],
                'trend' => [
                    'mahasiswa' => '34,110 310,76', 'ipk' => '34,102 310,84',
                    'dosen' => '34,124 310,90', 'publikasi' => '34,118 310,72',
                    'mahasiswaLastY' => 76, 'ipkLastY' => 84, 'dosenLastY' => 90, 'publikasiLastY' => 72,
                ],
                'capaian' => [
                    ['label' => 'Mahasiswa Aktif',            'percent' => 93],
                    ['label' => 'Lulusan Tepat Waktu',        'percent' => 87],
                    ['label' => 'Publikasi Ilmiah',           'percent' => 80],
                    ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => 90],
                ],
            ],
            2025 => [
                'kpi' => [
                    ['label' => 'Mahasiswa Aktif', 'value' => 185, 'decimals' => 0],
                    ['label' => 'IPK Rata-rata',   'value' => 3.52, 'decimals' => 2],
                    ['label' => 'Dosen Tetap',      'value' => 14,   'decimals' => 0],
                    ['label' => 'Publikasi',        'value' => 20,   'decimals' => 0],
                ],
                'trend' => [
                    'mahasiswa' => '34,114 310,80', 'ipk' => '34,105 310,87',
                    'dosen' => '34,126 310,92', 'publikasi' => '34,120 310,76',
                    'mahasiswaLastY' => 80, 'ipkLastY' => 87, 'dosenLastY' => 92, 'publikasiLastY' => 76,
                ],
                'capaian' => [
                    ['label' => 'Mahasiswa Aktif',            'percent' => 89],
                    ['label' => 'Lulusan Tepat Waktu',        'percent' => 82],
                    ['label' => 'Publikasi Ilmiah',           'percent' => 75],
                    ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => 85],
                ],
            ],
            2024 => [
                'kpi' => [
                    ['label' => 'Mahasiswa Aktif', 'value' => 170, 'decimals' => 0],
                    ['label' => 'IPK Rata-rata',   'value' => 3.41, 'decimals' => 2],
                    ['label' => 'Dosen Tetap',      'value' => 12,   'decimals' => 0],
                    ['label' => 'Publikasi',        'value' => 15,   'decimals' => 0],
                ],
                'trend' => [
                    'mahasiswa' => '34,118 310,84', 'ipk' => '34,108 310,90',
                    'dosen' => '34,128 310,94', 'publikasi' => '34,122 310,80',
                    'mahasiswaLastY' => 84, 'ipkLastY' => 90, 'dosenLastY' => 94, 'publikasiLastY' => 80,
                ],
                'capaian' => [
                    ['label' => 'Mahasiswa Aktif',            'percent' => 84],
                    ['label' => 'Lulusan Tepat Waktu',        'percent' => 76],
                    ['label' => 'Publikasi Ilmiah',           'percent' => 70],
                    ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => 79],
                ],
            ],
        ];
    }

    public static function ensureDefaults(): void
    {
        if (!Schema::hasTable('dashboard_year_stats')) {
            return;
        }

        foreach (static::defaults() as $year => $payload) {
            static::query()->firstOrCreate(
                ['year' => $year],
                [
                    'kpi'     => $payload['kpi'],
                    'trend'   => $payload['trend'],
                    'capaian' => $payload['capaian'],
                ]
            );
        }
    }
}
