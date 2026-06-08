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

    // Chart.js data exposed as public properties for $wire JS access
    public array $chartJsA = ['labels' => [], 'datasets' => []];
    public array $chartJsB = ['labels' => [], 'datasets' => []];

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
     *
     * Mode "year"  → 12 titik data bulanan (Jan–Des) dari DashboardMonthlyStat
     * Mode "all"   → 1 titik per tahun dari DashboardYearStat
     */
    private function buildTrendFromHistory(\Illuminate\Support\Collection $stats, int $activeYear, string $mode = 'year'): array
    {
        if ($mode === 'year') {
            return $this->buildTrendFromMonthly($stats, $activeYear);
        }

        return $this->buildTrendFromYearly($stats, $activeYear);
    }

    /**
     * Mode "Per Tahun": gunakan data bulanan (12 bulan) untuk tahun yang dipilih.
     * X-axis = Jan … Des, setiap seri punya skala Y sendiri agar naik-turun terlihat.
     */
    private function buildTrendFromMonthly(\Illuminate\Support\Collection $stats, int $activeYear): array
    {
        $statAktif = $stats->firstWhere('year', $activeYear);
        $annualKpi = $statAktif?->kpi ?? [];

        // Pastikan 12 baris bulanan tersedia
        DashboardMonthlyStat::ensureYear($activeYear, $annualKpi);

        $rows = DashboardMonthlyStat::query()
            ->where('year', $activeYear)
            ->orderBy('month')
            ->get();

        if ($rows->isEmpty()) {
            return $this->buildFallbackTrend('year', $activeYear);
        }

        // Ambil hanya bulan yang sudah ada datanya (≤ bulan sekarang untuk tahun berjalan)
        $cutoff = $activeYear === (int) now()->format('Y') ? (int) now()->format('n') : 12;
        $active = $rows->filter(fn($r) => $r->month <= $cutoff)->values();
        if ($active->isEmpty()) {
            $active = $rows->take(1)->values();
        }

        $mahasiswaValues = $active->map(fn($r) => (float) data_get($r->kpi, 'mahasiswa_aktif', 0))->values()->all();
        $ipkValues       = $active->map(fn($r) => (float) data_get($r->kpi, 'ipk', 0))->values()->all();
        $dosenValues     = $active->map(fn($r) => (float) data_get($r->kpi, 'dosen_tetap', 0))->values()->all();
        $pubPerMonth     = $active->map(fn($r) => (float) data_get($r->kpi, 'publikasi', 0))->values()->all();
        $months          = $active->map(fn($r) => (int) $r->month)->values()->all();

        // Publikasi kumulatif per bulan
        $pubCumulative = [];
        $cum = 0.0;
        foreach ($pubPerMonth as $v) { $cum += $v; $pubCumulative[] = $cum; }

        [$chartA, $chartB] = $this->buildChartPair(
            $mahasiswaValues, $dosenValues,
            $ipkValues, $pubCumulative,
            $months, true,
        );

        // Tooltip data per bulan (raw per-month publikasi, bukan kumulatif)
        $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $tooltipData = [];
        foreach ($months as $i => $m) {
            $tooltipData[] = [
                'label'     => $monthNames[$m - 1] ?? '-',
                'mahasiswa' => $mahasiswaValues[$i] ?? 0,
                'ipk'       => $ipkValues[$i] ?? 0,
                'dosen'     => $dosenValues[$i] ?? 0,
                'publikasi' => $pubCumulative[$i] ?? 0,
            ];
        }

        return [
            'chartA'      => $chartA,
            'chartB'      => $chartB,
            'tooltipData' => $tooltipData,
            'trendMode'   => 'year',
            'rangeLabel'  => 'Per Bulan — Tahun ' . $activeYear,
        ];
    }

    /**
     * Mode "Semua Tahun": 1 titik agregat per tahun.
     */
    private function buildTrendFromYearly(\Illuminate\Support\Collection $stats, int $activeYear): array
    {
        $sorted = $stats->sortBy('year')->values();

        if ($sorted->isEmpty()) {
            return $this->buildFallbackTrend('all', $activeYear);
        }

        // Duplikasi jika hanya 1 tahun agar tetap ada garis
        if ($sorted->count() === 1) {
            $sorted = collect([$sorted->first(), $sorted->first()]);
        }

        $years           = $sorted->pluck('year')->map(fn($y) => (int) $y)->values()->all();
        $mahasiswaValues = $sorted->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '0.value', 0))->values()->all();
        $ipkValues       = $sorted->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '1.value', 0))->values()->all();
        $dosenValues     = $sorted->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '2.value', 0))->values()->all();
        $publikasiValues = $sorted->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '3.value', 0))->values()->all();

        [$chartA, $chartB] = $this->buildChartPair(
            $mahasiswaValues, $dosenValues,
            $ipkValues, $publikasiValues,
            $years, false,
        );

        $firstYear  = (int) ($years[0] ?? $activeYear);
        $lastYear   = (int) ($years[count($years) - 1] ?? $activeYear);
        $rangeLabel = count($years) > 1
            ? 'Semua Tahun (' . $firstYear . ' – ' . $lastYear . ')'
            : 'Per Tahun ' . $firstYear;

        // Tooltip data per tahun
        $tooltipData = [];
        foreach ($years as $i => $yr) {
            $tooltipData[] = [
                'label'     => (string) $yr,
                'mahasiswa' => $mahasiswaValues[$i] ?? 0,
                'ipk'       => $ipkValues[$i] ?? 0,
                'dosen'     => $dosenValues[$i] ?? 0,
                'publikasi' => $publikasiValues[$i] ?? 0,
            ];
        }

        return [
            'chartA'      => $chartA,
            'chartB'      => $chartB,
            'tooltipData' => $tooltipData,
            'trendMode'   => 'all',
            'rangeLabel'  => $rangeLabel,
        ];
    }

    /**
     * Shared chart-pair builder.
     * $xLabels  : array of months (int 1–12) when $isMonthly=true, else years
     * $isMonthly: if true, X-axis labels are month abbreviations; else years
     */
    private function buildChartPair(
        array $mahasiswaValues,
        array $dosenValues,
        array $ipkValues,
        array $publikasiValues,
        array $xLabels,
        bool  $isMonthly,
    ): array {
        // ── Chart A: Mahasiswa & Dosen — shared Y scale ──
        $aAll = array_merge($mahasiswaValues, $dosenValues);
        $aMin = (float) min($aAll);
        $aMax = (float) max($aAll);
        // Tambahkan padding 5% agar garis tidak mentok tepi
        $aPad = max(1.0, ($aMax - $aMin) * 0.10);
        $aMin = $aMin - $aPad;
        $aMax = $aMax + $aPad;

        [$mahasiswaPolyline, $mahasiswaLastY] = $this->buildPolylineFromValues($mahasiswaValues, $aMin, $aMax);
        [$dosenPolyline,     $dosenLastY]     = $this->buildPolylineFromValues($dosenValues,     $aMin, $aMax);
        $xTicksA = $isMonthly
            ? $this->buildMonthTicks($xLabels)
            : $this->buildYearTicks($xLabels);

        $chartA = [
            'mahasiswa'      => $mahasiswaPolyline,
            'dosen'          => $dosenPolyline,
            'mahasiswaLastY' => $mahasiswaLastY,
            'dosenLastY'     => $dosenLastY,
            'lastX'          => self::CHART_X_END,
            'xTicks'         => $xTicksA,
            'yTicks'         => $this->buildValueTicks($aMin, $aMax, 0),
            'isMonthly'      => $isMonthly,
        ];

        // ── Chart B: IPK & Publikasi — masing-masing skala sendiri ──
        $ipkMin = (float) min($ipkValues);
        $ipkMax = (float) max($ipkValues);
        $ipkPad = max(0.05, ($ipkMax - $ipkMin) * 0.15);
        $ipkMin = max(0, $ipkMin - $ipkPad);
        $ipkMax = $ipkMax + $ipkPad;

        $pubMin = (float) min($publikasiValues);
        $pubMax = (float) max($publikasiValues);
        $pubPad = max(0.5, ($pubMax - $pubMin) * 0.10);
        $pubMin = max(0, $pubMin - $pubPad);
        $pubMax = $pubMax + $pubPad;

        [$ipkPolyline,       $ipkLastY]       = $this->buildPolylineFromValues($ipkValues,       $ipkMin, $ipkMax);
        [$publikasiPolyline, $publikasiLastY]  = $this->buildPolylineFromValues($publikasiValues, $pubMin, $pubMax);

        $xTicksB = $isMonthly
            ? $this->buildMonthTicks($xLabels)
            : $this->buildYearTicks($xLabels);

        $chartB = [
            'ipk'            => $ipkPolyline,
            'publikasi'      => $publikasiPolyline,
            'ipkLastY'       => $ipkLastY,
            'publikasiLastY' => $publikasiLastY,
            'lastX'          => self::CHART_X_END,
            'xTicks'         => $xTicksB,
            'yTicksIpk'      => $this->buildValueTicks($ipkMin, $ipkMax, 2),
            'yTicksPub'      => $this->buildValueTicks($pubMin, $pubMax, 0),
            'isMonthly'      => $isMonthly,
        ];

        return [$chartA, $chartB];
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
                'lastX' => self::CHART_X_END, 'xTicks' => [], 'yTicks' => [], 'isMonthly' => false,
            ],
            'chartB' => [
                'ipk' => $flat, 'publikasi' => $flat,
                'ipkLastY' => $midY, 'publikasiLastY' => $midY,
                'lastX' => self::CHART_X_END, 'xTicks' => [], 'yTicksIpk' => [], 'yTicksPub' => [], 'isMonthly' => false,
            ],
            'tooltipData' => [],
            'trendMode'  => $mode,
            'rangeLabel' => $rangeLabel,
        ];
    }

    /** X-axis ticks for yearly mode */
    private function buildYearTicks(array $years): array
    {
        $years = array_values(array_map(static fn($y) => (int) $y, $years));
        if (count($years) === 0) {
            return [];
        }

        if (count($years) === 1) {
            return [[
                'x' => round((self::CHART_X_START + self::CHART_X_END) / 2, 1),
                'label' => (string) $years[0],
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
                'x'     => round(self::CHART_X_START + ($idx * $step), 1),
                'label' => (string) $years[$idx],
            ])
            ->all();
    }

    /** X-axis ticks for monthly mode — months is array of int (1–12) */
    private function buildMonthTicks(array $months): array
    {
        $names = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $total = count($months);

        if ($total === 0) {
            return [];
        }

        if ($total === 1) {
            return [[
                'x'     => round((self::CHART_X_START + self::CHART_X_END) / 2, 1),
                'label' => $names[($months[0] - 1)] ?? '-',
            ]];
        }

        $step = (self::CHART_X_END - self::CHART_X_START) / ($total - 1);

        // Show every label when ≤ 6, else thin out
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
                'x'     => round(self::CHART_X_START + ($idx * $step), 1),
                'label' => $names[($months[$idx] - 1)] ?? '-',
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

        $activeYear = (int) ($statAktif?->year ?? $this->tahunDipilih);

        $trendVisual = $this->buildTrendFromHistory(
            $allStats->sortBy('year')->values(),
            $activeYear,
            $this->trendMode,
        );

        // Chart.js-ready data (used by the new chart component)
        $chartJsData = $this->buildChartJsData(
            $allStats->sortBy('year')->values(),
            $activeYear,
            $this->trendMode,
        );

        // Expose as public properties (kept for backward compat with beranda page)
        $this->chartJsA = data_get($chartJsData, 'chartA', ['labels'=>[],'datasets'=>[]]);
        $this->chartJsB = data_get($chartJsData, 'chartB', ['labels'=>[],'datasets'=>[]]);

        $kinerjaTahunanBerjalan = DashboardMonthlyStat::summarizeYear($activeYear, $statAktif?->kpi ?? []);

        return view('livewire.pages.statistik-page', [
            'daftarTahun'             => $allStats->pluck('year')->all(),
            'statAktif'               => $statAktif,
            'trendVisual'             => $trendVisual,
            'chartJsData'             => $chartJsData,
            'programCount'            => DashboardProgramItem::query()->where('year', $statAktif?->year)->count(),
            'kinerjaTahunanBerjalan'  => $kinerjaTahunanBerjalan,
        ]);
    }

    // ──────────────────────────────────────────────────
    // Chart.js data builder
    // ──────────────────────────────────────────────────

    private function buildChartJsData(\Illuminate\Support\Collection $stats, int $activeYear, string $mode): array
    {
        $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        if ($mode === 'year') {
            $annualKpi = $stats->firstWhere('year', $activeYear)?->kpi ?? [];
            DashboardMonthlyStat::ensureYear($activeYear, $annualKpi);

            $cutoff = $activeYear === (int) now()->format('Y') ? (int) now()->format('n') : 12;
            $rows = DashboardMonthlyStat::query()
                ->where('year', $activeYear)->orderBy('month')->get()
                ->filter(fn($r) => $r->month <= $cutoff)->values();

            if ($rows->isEmpty()) {
                return $this->emptyChartJs('Per Bulan — ' . $activeYear);
            }

            $labels = $rows->map(fn($r) => $monthNames[(int)$r->month - 1])->all();
            $mhs    = $rows->map(fn($r) => (float) data_get($r->kpi, 'mahasiswa_aktif', 0))->all();
            $ipk    = $rows->map(fn($r) => (float) data_get($r->kpi, 'ipk', 0))->all();
            $dos    = $rows->map(fn($r) => (float) data_get($r->kpi, 'dosen_tetap', 0))->all();
            $pub    = []; $cum = 0.0;
            foreach ($rows as $r) { $cum += (float) data_get($r->kpi,'publikasi',0); $pub[] = round($cum,1); }

            return [
                'rangeLabel' => 'Per Bulan — Tahun ' . $activeYear,
                'trendMode'  => 'year',
                // Two separate charts (statistik page)
                'chartA' => ['labels'=>$labels,'datasets'=>[
                    ['label'=>'Mahasiswa Aktif','data'=>$mhs,'color'=>'#2563eb','fill'=>true],
                    ['label'=>'Dosen Tetap',    'data'=>$dos,'color'=>'#0f766e','fill'=>false],
                ]],
                'chartB' => ['labels'=>$labels,'datasets'=>[
                    ['label'=>'IPK Rata-rata',        'data'=>$ipk,'color'=>'#0f766e','fill'=>true,'yAxis'=>'y'],
                    ['label'=>'Publikasi (kumulatif)','data'=>$pub,'color'=>'#f97316','fill'=>false,'dash'=>[6,4],'yAxis'=>'y2'],
                ]],
                // One combined chart (beranda page) — mahasiswa+dosen left axis, IPK right axis
                'chartAll' => ['labels'=>$labels,'datasets'=>[
                    ['label'=>'Mahasiswa Aktif','data'=>$mhs,'color'=>'#2563eb','fill'=>false,'yAxis'=>'y'],
                    ['label'=>'Dosen Tetap',    'data'=>$dos,'color'=>'#0f766e','fill'=>false,'yAxis'=>'y'],
                    ['label'=>'Publikasi (kum.)','data'=>$pub,'color'=>'#f97316','fill'=>false,'dash'=>[5,3],'yAxis'=>'y'],
                    ['label'=>'IPK Rata-rata',  'data'=>$ipk,'color'=>'#7c3aed','fill'=>false,'dash'=>[3,2],'yAxis'=>'y2'],
                ]],
            ];
        }

        // Yearly aggregates
        $sorted = $stats->sortBy('year')->values();
        if ($sorted->isEmpty()) { return $this->emptyChartJs('Semua Tahun'); }

        $years = $sorted->pluck('year')->map(fn($y) => (string)$y)->all();
        $mhs   = $sorted->map(fn($s) => (float) data_get($s->kpi,'0.value',0))->all();
        $ipk   = $sorted->map(fn($s) => (float) data_get($s->kpi,'1.value',0))->all();
        $dos   = $sorted->map(fn($s) => (float) data_get($s->kpi,'2.value',0))->all();
        $pub   = $sorted->map(fn($s) => (float) data_get($s->kpi,'3.value',0))->all();
        $first = $years[0] ?? $activeYear;
        $last  = end($years) ?: $activeYear;

        return [
            'rangeLabel' => 'Semua Tahun (' . $first . ' – ' . $last . ')',
            'trendMode'  => 'all',
            'chartA' => ['labels'=>$years,'datasets'=>[
                ['label'=>'Mahasiswa Aktif','data'=>$mhs,'color'=>'#2563eb','fill'=>true],
                ['label'=>'Dosen Tetap',    'data'=>$dos,'color'=>'#0f766e','fill'=>false],
            ]],
            'chartB' => ['labels'=>$years,'datasets'=>[
                ['label'=>'IPK Rata-rata','data'=>$ipk,'color'=>'#0f766e','fill'=>true,'yAxis'=>'y'],
                ['label'=>'Publikasi',    'data'=>$pub,'color'=>'#f97316','fill'=>false,'dash'=>[6,4],'yAxis'=>'y2'],
            ]],
            'chartAll' => ['labels'=>$years,'datasets'=>[
                ['label'=>'Mahasiswa Aktif','data'=>$mhs,'color'=>'#2563eb','fill'=>false,'yAxis'=>'y'],
                ['label'=>'Dosen Tetap',    'data'=>$dos,'color'=>'#0f766e','fill'=>false,'yAxis'=>'y'],
                ['label'=>'Publikasi',      'data'=>$pub,'color'=>'#f97316','fill'=>false,'dash'=>[5,3],'yAxis'=>'y'],
                ['label'=>'IPK Rata-rata',  'data'=>$ipk,'color'=>'#7c3aed','fill'=>false,'dash'=>[3,2],'yAxis'=>'y2'],
            ]],
        ];
    }

    private function emptyChartJs(string $label): array
    {
        $empty = ['labels'=>[],'datasets'=>[]];
        return ['rangeLabel'=>$label,'trendMode'=>'year','chartA'=>$empty,'chartB'=>$empty,'chartAll'=>$empty];
    }
}
