<?php

namespace Database\Factories;

use App\Models\DashboardMonthlyStat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DashboardMonthlyStat>
 *
 * Digunakan untuk membuat data bulanan dengan pola musiman akademik.
 * Nilai setiap bulan dihitung dari target tahunan yang diteruskan lewat state.
 */
class DashboardMonthlyStatFactory extends Factory
{
    protected $model = DashboardMonthlyStat::class;

    // Kurva musiman — indeks 0 = Januari, 11 = Desember
    private const MAHASISWA_CURVE = [0.82, 0.88, 0.91, 0.90, 0.87, 0.84, 0.83, 0.88, 0.94, 0.96, 0.98, 1.00];
    private const IPK_OFFSET       = [-0.14, -0.11, -0.08, -0.05, -0.03, -0.01, -0.09, -0.07, -0.05, -0.03, -0.01, 0.00];
    private const DOSEN_CURVE      = [0.87, 0.89, 0.91, 0.92, 0.93, 0.94, 0.94, 0.95, 0.96, 0.97, 0.98, 1.00];
    private const PUB_WEIGHTS      = [0.05, 0.06, 0.09, 0.10, 0.10, 0.11, 0.07, 0.07, 0.09, 0.10, 0.09, 0.07];

    public function definition(): array
    {
        // Default fallback — overridden by forYear() state
        return [
            'year'  => (int) now()->format('Y'),
            'month' => 1,
            'kpi'   => [
                'mahasiswa_aktif' => 150,
                'ipk'             => 3.30,
                'dosen_tetap'     => 10,
                'publikasi'       => 1,
            ],
        ];
    }

    /**
     * State: build monthly KPI from annual targets.
     *
     * @param  int    $year
     * @param  int    $month      1–12
     * @param  int    $mahasiswa  annual target
     * @param  float  $ipk        annual target
     * @param  int    $dosen      annual target
     * @param  int    $publikasi  annual target
     */
    public function forMonth(int $year, int $month, int $mahasiswa, float $ipk, int $dosen, int $publikasi): static
    {
        $idx = $month - 1;

        // Same curves as StatistikSeeder and DashboardMonthlyStat::buildDefaultMonthlyKpi
        $mhsVal   = max(1, (int) round($mahasiswa * self::MAHASISWA_CURVE[$idx]));
        $ipkVal   = round(max(2.80, $ipk + self::IPK_OFFSET[$idx]), 2);
        $dosenVal = max(1, (int) round($dosen * self::DOSEN_CURVE[$idx]));
        $pubVal   = max(1, (int) round($publikasi * self::PUB_WEIGHTS[$idx]));

        return $this->state([
            'year'  => $year,
            'month' => $month,
            'kpi'   => [
                'mahasiswa_aktif' => $mhsVal,
                'ipk'             => $ipkVal,
                'dosen_tetap'     => $dosenVal,
                'publikasi'       => $pubVal,
            ],
        ]);
    }
}
