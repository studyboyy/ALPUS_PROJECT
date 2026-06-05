<?php

namespace Database\Factories;

use App\Models\DashboardYearStat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DashboardYearStat>
 */
class DashboardYearStatFactory extends Factory
{
    protected $model = DashboardYearStat::class;

    public function definition(): array
    {
        $mahasiswa = $this->faker->numberBetween(140, 200);
        $ipk       = $this->faker->randomFloat(2, 3.10, 3.75);
        $dosen     = $this->faker->numberBetween(8, 20);
        $publikasi = $this->faker->numberBetween(5, 30);

        return [
            'year' => (int) now()->format('Y'),
            'kpi' => [
                ['label' => 'Mahasiswa Aktif', 'value' => $mahasiswa, 'decimals' => 0],
                ['label' => 'IPK Rata-rata',   'value' => $ipk,       'decimals' => 2],
                ['label' => 'Dosen Tetap',      'value' => $dosen,     'decimals' => 0],
                ['label' => 'Publikasi',        'value' => $publikasi, 'decimals' => 0],
            ],
            'trend' => [
                'mahasiswa'      => '34,110 310,76',
                'ipk'            => '34,102 310,84',
                'publikasi'      => '34,118 310,72',
                'dosen'          => '34,124 310,88',
                'mahasiswaLastY' => 76,
                'ipkLastY'       => 84,
                'publikasiLastY' => 72,
                'dosenLastY'     => 88,
            ],
            'capaian' => [
                ['label' => 'Mahasiswa Aktif',         'percent' => $this->faker->numberBetween(65, 98)],
                ['label' => 'Lulusan Tepat Waktu',     'percent' => $this->faker->numberBetween(60, 95)],
                ['label' => 'Publikasi Ilmiah',        'percent' => $this->faker->numberBetween(58, 96)],
                ['label' => 'Kegiatan Dosen & Mahasiswa', 'percent' => $this->faker->numberBetween(62, 97)],
            ],
        ];
    }
}
