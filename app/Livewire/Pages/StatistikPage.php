<?php

namespace App\Livewire\Pages;

use App\Models\DashboardMonthlyStat;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Data dan Statistik')]
class StatistikPage extends Component
{
    private const CHART_X_START = 34.0;
    private const CHART_X_END = 310.0;
    private const CHART_Y_TOP = 20.0;
    private const CHART_Y_BOTTOM = 128.0;

    public int $tahunDipilih = 0;
    public string $trendMode = 'year';

    // Chart A: Mahasiswa Aktif & Dosen Tetap (angka absolut besar)
    // Chart B: IPK Rata-rata & Publikasi (skala kecil, berbeda satuan)

    public function mount(): void
    {
        if ($this->tahunDipilih <= 0) {
            $this->tahunDipilih = (int) now()->format('Y');
        }

        DashboardYearStat::ensureDefaults();
        DashboardProgramItem::ensureDefaults();

        $tahunTerbaru = DashboardYearStat::query()->max('year');
        if (is_numeric($tahunTerbaru)) {
            $this->tahunDipilih = (int) $tahunTerbaru;
        }
    }

    public function pilihTahun(int $tahun): void
    {
        if (DashboardYearStat::query()->where('year', $tahun)->exists()) {
            $this->tahunDipilih = $tahun;
        }
    }

    public function pilihTrendMode(string $mode): void
    {
        if (!in_array($mode, ['year', 'all'], true)) {
            return;
        }

        $this->trendMode = $mode;
    }

    /**
     * Build two separate trend charts so each uses its own Y-axis scale:
     *   chartA → Mahasiswa Aktif & Dosen Tetap (large absolute numbers)
     *   chartB → IPK Rata-rata & Publikasi (small/different scale)
     */
    private function buildTrendFromHistory(\Illuminate\Support\Collection $stats, int $activeYear, string $mode = 'year'): array
    {
        $sortedStats = $stats->sortBy('year')->values();

        $window = $mode === 'all'
            ? $sortedStats
            : $sortedStats->filter(fn(DashboardYearStat $s) => $s->year === $activeYear)->values();

        // Need at least 2 data points for meaningful trend; duplicate single point so lines render
        if ($window->count() === 1) {
            $single = $window->first();
            $window = collect([$single, $single]);
        }

        if ($window->isEmpty()) {
            return $this->buildFallbackTrend($mode, $activeYear);
        }

        $years = $window->pluck('year')->map(fn($y) => (int) $y)->values()->all();

        // Collect raw values per series
        $mahasiswaValues = $window->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '0.value', 0))->values()->all();
        $ipkValues       = $window->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '1.value', 0))->values()->all();
        $dosenValues     = $window->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '2.value', 0))->values()->all();
        $publikasiValues = $window->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '3.value', 0))->values()->all();

        // ── Chart A: Mahasiswa & Dosen (shared Y scale for both so they're comparable) ──
        $aValues = array_merge($mahasiswaValues, $dosenValues);
        $aMin = min($aValues);
        $aMax = max($aValues);
        if ($aMin === $aMax) { $aMax = $aMin + 1; }

        [$mahasiswaPolyline, $mahasiswaLastY] = $this->buildPolylineFromValues($mahasiswaValues, $aMin, $aMax);
        [$dosenPolyline,     $dosenLastY]     = $this->buildPolylineFromValues($dosenValues,     $aMin, $aMax);
        $axisA = $this->buildTrendAxis($years, $aMin, $aMax);

        // ── Chart B: IPK & Publikasi (each gets its own axis, displayed with dual labels) ──
        $ipkMin = min($ipkValues);
        $ipkMax = max($ipkValues);
        if ($ipkMin === $ipkMax) { $ipkMax = $ipkMin + 0.1; }

        $pubMin = min($publikasiValues);
        $pubMax = max($publikasiValues);
        if ($pubMin === $pubMax) { $pubMax = $pubMin + 1; }

        [$ipkPolyline,       $ipkLastY]       = $this->buildPolylineFromValues($ipkValues,       $ipkMin, $ipkMax);
        [$publikasiPolyline, $publikasiLastY]  = $this->buildPolylineFromValues($publikasiValues, $pubMin, $pubMax);

        // Y-axis ticks for chart B: show IPK scale on left, Publikasi scale on right
        $axisB_ipk  = $this->buildValueTicks($ipkMin, $ipkMax, 2);
        $axisB_pub  = $this->buildValueTicks($pubMin, $pubMax, 0);
        $yearTicksB = $this->buildYearTicks($years);

        $firstYear = (int) ($years[0] ?? $activeYear);
        $lastYear  = (int) ($years[count($years) - 1] ?? $activeYear);
        $rangeLabel = $mode === 'all'
            ? 'Semua Tahun (' . $firstYear . ' – ' . $lastYear . ')'
            : 'Per Tahun ' . $lastYear;

        return [
            // Chart A — Mahasiswa & Dosen
            'chartA' => [
                'mahasiswa'      => $mahasiswaPolyline,
                'dosen'          => $dosenPolyline,
                'mahasiswaLastY' => $mahasiswaLastY,
                'dosenLastY'     => $dosenLastY,
                'lastX'          => self::CHART_X_END,
                'yearTicks'      => $axisA['yearTicks'],
                'yTicks'         => $axisA['yTicks'],
            ],
            // Chart B — IPK & Publikasi (dual scale)
            'chartB' => [
                'ipk'            => $ipkPolyline,
                'publikasi'      => $publikasiPolyline,
                'ipkLastY'       => $ipkLastY,
                'publikasiLastY' => $publikasiLastY,
                'lastX'          => self::CHART_X_END,
                'yearTicks'      => $yearTicksB,
                'yTicksIpk'      => $axisB_ipk,
                'yTicksPub'      => $axisB_pub,
            ],
            'trendMode'  => $mode,
            'rangeLabel' => $rangeLabel,
        ];
    }

    private function buildFallbackTrend(string $mode, int $activeYear): array
    {
        $midY = (self::CHART_Y_TOP + self::CHART_Y_BOTTOM) / 2;
        $flat = self::CHART_X_START . ',' . $midY . ' ' . self::CHART_X_END . ',' . $midY;
        $rangeLabel = $mode === 'all' ? 'Semua Tahun' : 'Per Tahun ' . $activeYear;

        return [
            'chartA' => [
                'mahasiswa' => $flat, 'dosen' => $flat,
                'mahasiswaLastY' => $midY, 'dosenLastY' => $midY,
                'lastX' => self::CHART_X_END, 'yearTicks' => [], 'yTicks' => [],
            ],
            'chartB' => [
                'ipk' => $flat, 'publikasi' => $flat,
                'ipkLastY' => $midY, 'publikasiLastY' => $midY,
                'lastX' => self::CHART_X_END, 'yearTicks' => [], 'yTicksIpk' => [], 'yTicksPub' => [],
            ],
            'trendMode'  => $mode,
            'rangeLabel' => $rangeLabel,
        ];
    }

    private function buildTrendAxis(array $years, float $minValue, float $maxValue): array
    {
        return [
            'yearTicks' => $this->buildYearTicks($years),
            'yTicks' => $this->buildValueTicks($minValue, $maxValue),
        ];
    }

    private function buildYearTicks(array $years): array
    {
        $years = array_values(array_map(static fn($y) => (int) $y, $years));
        if (count($years) === 0) {
            return [];
        }

        if (count($years) === 1) {
            return [[
                'x' => round((self::CHART_X_START + self::CHART_X_END) / 2, 1),
                'year' => $years[0],
            ]];
        }

        $total = count($years);
        $step = (self::CHART_X_END - self::CHART_X_START) / ($total - 1);
        $maxLabels = 6;

        $indexes = $total <= $maxLabels
            ? range(0, $total - 1)
            : collect(range(0, $maxLabels - 1))
            ->map(fn(int $i) => (int) round($i * ($total - 1) / ($maxLabels - 1)))
            ->unique()
            ->values()
            ->all();

        return collect($indexes)
            ->map(fn(int $idx) => [
                'x' => round(self::CHART_X_START + ($idx * $step), 1),
                'year' => $years[$idx],
            ])
            ->all();
    }

    private function buildValueTicks(float $minValue, float $maxValue, int $decimals = 0): array
    {
        if ($maxValue <= $minValue) {
            $maxValue = $minValue + ($decimals > 0 ? 0.1 : 1);
        }

        $steps = 4;
        $range = $maxValue - $minValue;
        $stepSize = $this->calculateNiceStep($range / $steps);

        // For small-range decimals (IPK), ensure a minimum step so labels differ
        if ($decimals > 0 && $stepSize < 0.01) {
            $stepSize = 0.01;
        }

        $niceMin = floor($minValue / $stepSize) * $stepSize;
        $niceMax = ceil($maxValue / $stepSize) * $stepSize;
        if ($niceMax <= $niceMin) {
            $niceMax = $niceMin + $stepSize;
        }

        $ticks = [];
        for ($i = 0; $i <= $steps; $i++) {
            $ratio = $i / $steps;
            $y = self::CHART_Y_TOP + ($ratio * (self::CHART_Y_BOTTOM - self::CHART_Y_TOP));
            $value = $niceMax - ($ratio * ($niceMax - $niceMin));
            $ticks[] = [
                'y' => round($y, 1),
                'label' => number_format(round($value, $decimals), $decimals, ',', '.'),
            ];
        }

        return $ticks;
    }

    private function calculateNiceStep(float $roughStep): float
    {
        if ($roughStep <= 0) {
            return 1.0;
        }

        $magnitude = pow(10, floor(log10($roughStep)));
        $normalized = $roughStep / $magnitude;

        if ($normalized <= 1) {
            $niceNormalized = 1;
        } elseif ($normalized <= 2) {
            $niceNormalized = 2;
        } elseif ($normalized <= 5) {
            $niceNormalized = 5;
        } else {
            $niceNormalized = 10;
        }

        return $niceNormalized * $magnitude;
    }

    private function buildPolylineFromValues(array $values, ?float $globalMin = null, ?float $globalMax = null): array
    {
        $values = array_values(array_map(static fn($value) => (float) $value, $values));
        $count = count($values);

        if ($count < 2) {
            return ['10,90 300,90', 90.0];
        }

        $xStart = self::CHART_X_START;
        $xEnd = self::CHART_X_END;
        $yTop = self::CHART_Y_TOP;
        $yBottom = self::CHART_Y_BOTTOM;

        $min = $globalMin ?? min($values);
        $max = $globalMax ?? max($values);
        $xStep = ($xEnd - $xStart) / ($count - 1);

        $points = [];
        $lastY = 90.0;

        foreach ($values as $idx => $value) {
            $x = round($xStart + ($idx * $xStep), 1);
            if ($max === $min) {
                $y = 90.0;
            } else {
                $ratio = ($value - $min) / ($max - $min);
                $y = $yBottom - ($ratio * ($yBottom - $yTop));
            }

            $y = max($yTop, min($yBottom, round($y, 1)));
            $lastY = $y;
            $points[] = $x . ',' . $y;
        }

        return [implode(' ', $points), $lastY];
    }

    public function render()
    {
        $allStats = DashboardYearStat::query()->orderByDesc('year')->get();
        $statAktif = $allStats->firstWhere('year', $this->tahunDipilih) ?? $allStats->first();

        if ($statAktif) {
            DashboardMonthlyStat::ensureYear((int) $statAktif->year, $statAktif->kpi ?? []);
        }

        $trendVisual = $this->buildTrendFromHistory(
            $allStats->sortBy('year')->values(),
            (int) ($statAktif?->year ?? $this->tahunDipilih),
            $this->trendMode,
        );

        $kinerjaTahunanBerjalan = DashboardMonthlyStat::summarizeYear((int) ($statAktif?->year ?? $this->tahunDipilih), $statAktif?->kpi ?? []);

        return view('livewire.pages.statistik-page', [
            'daftarTahun' => $allStats->pluck('year')->all(),
            'statAktif' => $statAktif,
            'trendVisual' => $trendVisual,
            'programCount' => DashboardProgramItem::query()->where('year', $statAktif?->year)->count(),
            'kinerjaTahunanBerjalan' => $kinerjaTahunanBerjalan,
        ]);
    }
}
