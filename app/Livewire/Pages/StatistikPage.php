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

    private function buildTrendFromHistory(\Illuminate\Support\Collection $stats, int $activeYear, string $mode = 'year'): array
    {
        $sortedStats = $stats->sortBy('year')->values();

        $window = $mode === 'all'
            ? $sortedStats
            : $sortedStats
            ->filter(fn(DashboardYearStat $s) => $s->year === $activeYear)
            ->values();

        $years = $window->pluck('year')->map(fn($year) => (int) $year)->values()->all();

        if ($window->count() === 1) {
            $single = $window->first();
            $singleYear = (int) data_get($single, 'year', $activeYear);

            $mahasiswa = (float) data_get($single, 'kpi.0.value', 0);
            $ipk = (float) data_get($single, 'kpi.1.value', 0);
            $publikasi = (float) data_get($single, 'kpi.3.value', 0);
            $dosen = (float) data_get($single, 'kpi.2.value', 0);
            $ringkasan = DashboardMonthlyStat::summarizeYear($singleYear, $single->kpi ?? []);
            $progressYtd = (float) collect(data_get($ringkasan, 'items', []))->avg('progress');

            $allValues = [$mahasiswa, $ipk, $publikasi, $dosen, $progressYtd];
            $globalMin = min($allValues);
            $globalMax = max($allValues);
            if ($globalMin === $globalMax) {
                $globalMax = $globalMin + 1;
            }

            [$mahasiswaPolyline, $mahasiswaLastY] = $this->buildPolylineFromValues([$mahasiswa, $mahasiswa], $globalMin, $globalMax);
            [$ipkPolyline, $ipkLastY] = $this->buildPolylineFromValues([$ipk, $ipk], $globalMin, $globalMax);
            [$publikasiPolyline, $publikasiLastY] = $this->buildPolylineFromValues([$publikasi, $publikasi], $globalMin, $globalMax);
            [$dosenPolyline, $dosenLastY] = $this->buildPolylineFromValues([$dosen, $dosen], $globalMin, $globalMax);
            [$progressPolyline, $progressLastY] = $this->buildPolylineFromValues([$progressYtd, $progressYtd], $globalMin, $globalMax);
            $axis = $this->buildTrendAxis([$singleYear], $globalMin, $globalMax);

            return [
                'mahasiswa' => $mahasiswaPolyline,
                'ipk' => $ipkPolyline,
                'publikasi' => $publikasiPolyline,
                'dosen' => $dosenPolyline,
                'progressYtd' => $progressPolyline,
                'lastX' => self::CHART_X_END,
                'mahasiswaLastY' => $mahasiswaLastY,
                'ipkLastY' => $ipkLastY,
                'publikasiLastY' => $publikasiLastY,
                'dosenLastY' => $dosenLastY,
                'progressLastY' => $progressLastY,
                'trendMode' => $mode,
                'rangeLabel' => 'Per Tahun ' . $singleYear,
                'yearTicks' => $axis['yearTicks'],
                'yTicks' => $axis['yTicks'],
            ];
        }

        if ($window->count() < 2) {
            $fallback = [self::CHART_X_START, 103, 172, 241, self::CHART_X_END];
            return [
                'mahasiswa' => collect($fallback)->map(fn($x) => $x . ',74')->implode(' '),
                'ipk' => collect($fallback)->map(fn($x) => $x . ',88')->implode(' '),
                'publikasi' => collect($fallback)->map(fn($x) => $x . ',102')->implode(' '),
                'dosen' => collect($fallback)->map(fn($x) => $x . ',114')->implode(' '),
                'progressYtd' => collect($fallback)->map(fn($x) => $x . ',96')->implode(' '),
                'lastX' => self::CHART_X_END,
                'mahasiswaLastY' => 74,
                'ipkLastY' => 88,
                'publikasiLastY' => 102,
                'dosenLastY' => 114,
                'progressLastY' => 96,
                'trendMode' => $mode,
                'rangeLabel' => $mode === 'all' ? 'Semua Tahun' : 'Per Tahun ' . $activeYear,
                'yearTicks' => $this->buildYearTicks([]),
                'yTicks' => $this->buildValueTicks(0, 120),
            ];
        }

        $mahasiswaValues = $window->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '0.value', 0))->all();
        $ipkValues = $window->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '1.value', 0))->all();
        $publikasiValues = $window->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '3.value', 0))->all();
        $dosenValues = $window->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '2.value', 0))->all();
        $progressValues = $window
            ->map(function (DashboardYearStat $s): float {
                DashboardMonthlyStat::ensureYear((int) $s->year, $s->kpi ?? []);
                $ringkasan = DashboardMonthlyStat::summarizeYear((int) $s->year, $s->kpi ?? []);
                return (float) collect(data_get($ringkasan, 'items', []))->avg('progress');
            })
            ->all();

        $allValues = array_merge($mahasiswaValues, $ipkValues, $publikasiValues, $dosenValues, $progressValues);
        $globalMin = min($allValues);
        $globalMax = max($allValues);

        [$mahasiswaPolyline, $mahasiswaLastY] = $this->buildPolylineFromValues($mahasiswaValues, $globalMin, $globalMax);
        [$ipkPolyline, $ipkLastY] = $this->buildPolylineFromValues($ipkValues, $globalMin, $globalMax);
        [$publikasiPolyline, $publikasiLastY] = $this->buildPolylineFromValues($publikasiValues, $globalMin, $globalMax);
        [$dosenPolyline, $dosenLastY] = $this->buildPolylineFromValues($dosenValues, $globalMin, $globalMax);
        [$progressPolyline, $progressLastY] = $this->buildPolylineFromValues($progressValues, $globalMin, $globalMax);
        $axis = $this->buildTrendAxis($years, $globalMin, $globalMax);

        $firstYear = (int) ($years[0] ?? $activeYear);
        $lastYear = (int) ($years[count($years) - 1] ?? $activeYear);

        return [
            'mahasiswa' => $mahasiswaPolyline,
            'ipk' => $ipkPolyline,
            'publikasi' => $publikasiPolyline,
            'dosen' => $dosenPolyline,
            'progressYtd' => $progressPolyline,
            'lastX' => self::CHART_X_END,
            'mahasiswaLastY' => $mahasiswaLastY,
            'ipkLastY' => $ipkLastY,
            'publikasiLastY' => $publikasiLastY,
            'dosenLastY' => $dosenLastY,
            'progressLastY' => $progressLastY,
            'trendMode' => $mode,
            'rangeLabel' => $mode === 'all' ? ('Semua Tahun (' . $firstYear . ' - ' . $lastYear . ')') : ('Per Tahun ' . $lastYear),
            'yearTicks' => $axis['yearTicks'],
            'yTicks' => $axis['yTicks'],
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

    private function buildValueTicks(float $minValue, float $maxValue): array
    {
        if ($maxValue <= $minValue) {
            $maxValue = $minValue + 1;
        }

        $steps = 4;
        $range = $maxValue - $minValue;
        $stepSize = $this->calculateNiceStep($range / $steps);
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
                'label' => number_format($value, 0, ',', '.'),
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
