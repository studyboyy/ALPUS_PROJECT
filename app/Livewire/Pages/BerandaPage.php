<?php

namespace App\Livewire\Pages;

use App\Models\DashboardMonthlyStat;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use App\Models\ContactFeedback;
use App\Models\HomePageSetting;
use App\Models\ProfileSection;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Beranda')]
class BerandaPage extends Component
{
    private const CHART_X_START = 34.0;
    private const CHART_X_END = 310.0;
    private const CHART_Y_TOP = 20.0;
    private const CHART_Y_BOTTOM = 128.0;

    public int $tahunDipilih = 0;
    public string $trendMode = 'year';
    public string $feedbackName = '';
    public string $feedbackEmail = '';
    public string $feedbackSubject = '';
    public string $feedbackMessage = '';

    // Chart.js combined data for beranda, exposed for $wire JS access
    public array $chartJsAll = ['labels' => [], 'datasets' => []];

    private const KPI_CLASSES = [
        ['boxClass' => 'border-blue-100 bg-linear-to-br from-blue-50 to-indigo-50', 'valueClass' => 'text-blue-700'],
        ['boxClass' => 'border-violet-100 bg-linear-to-br from-violet-50 to-purple-50', 'valueClass' => 'text-violet-700'],
        ['boxClass' => 'border-emerald-100 bg-linear-to-br from-emerald-50 to-teal-50', 'valueClass' => 'text-emerald-700'],
        ['boxClass' => 'border-amber-100 bg-linear-to-br from-amber-50 to-orange-50', 'valueClass' => 'text-amber-700'],
    ];

    private const CAPAIAN_CLASSES = [
        ['trackClass' => 'bg-blue-100', 'barClass' => 'from-blue-500 to-indigo-600', 'textClass' => 'text-blue-700'],
        ['trackClass' => 'bg-violet-100', 'barClass' => 'from-violet-500 to-purple-600', 'textClass' => 'text-violet-700'],
        ['trackClass' => 'bg-emerald-100', 'barClass' => 'from-emerald-500 to-teal-600', 'textClass' => 'text-emerald-700'],
        ['trackClass' => 'bg-amber-100', 'barClass' => 'from-amber-500 to-orange-600', 'textClass' => 'text-amber-700'],
    ];

    private const PROGRAM_STYLE_MAP = [
        'blue' => ['boxClass' => 'border-blue-100 bg-linear-to-br from-blue-50 to-indigo-50', 'badgeClass' => 'bg-blue-100 text-blue-700'],
        'violet' => ['boxClass' => 'border-violet-100 bg-linear-to-br from-violet-50 to-purple-50', 'badgeClass' => 'bg-violet-100 text-violet-700'],
        'amber' => ['boxClass' => 'border-amber-100 bg-linear-to-br from-amber-50 to-orange-50', 'badgeClass' => 'bg-amber-100 text-amber-700'],
        'rose' => ['boxClass' => 'border-rose-100 bg-linear-to-br from-rose-50 to-pink-50', 'badgeClass' => 'bg-rose-100 text-rose-700'],
    ];

    private const EXECUTION_STATUS_MAP = [
        'terlaksana' => ['label' => 'Terlaksana', 'badgeClass' => 'bg-emerald-100 text-emerald-700'],
        'belum_terlaksana' => ['label' => 'Belum Terlaksana', 'badgeClass' => 'bg-rose-100 text-rose-700'],
    ];

    public function mount(): void
    {
        if ($this->tahunDipilih <= 0) {
            $this->tahunDipilih = (int) now()->format('Y');
        }

        if (!Schema::hasTable('dashboard_year_stats') || !Schema::hasTable('dashboard_program_items')) {
            return;
        }

        DashboardYearStat::ensureDefaults();
        DashboardProgramItem::ensureDefaults();
        HomePageSetting::ensureDefaults();
        if (Schema::hasTable('profile_sections')) {
            ProfileSection::ensureDefaults();
        }

        $tahunTerbaru = DashboardYearStat::query()->max('year');
        if (is_numeric($tahunTerbaru)) {
            $this->tahunDipilih = (int) $tahunTerbaru;
        }
    }

    public function pilihTahun(int $tahun): void
    {
        if (!Schema::hasTable('dashboard_year_stats')) {
            return;
        }

        $exists = DashboardYearStat::query()->where('year', $tahun)->exists();

        if ($exists) {
            $this->tahunDipilih = $tahun;
            $this->dispatch('statistik-updated');
        }
    }

    public function pilihTrendMode(string $mode): void
    {
        if (!in_array($mode, ['year', 'all'], true)) {
            return;
        }

        $this->trendMode = $mode;
        $this->dispatch('statistik-updated');
    }

    public function kirimUmpanBalik(): void
    {
        if (!Schema::hasTable('contact_feedback')) {
            session()->flash('contact_status', 'Fitur umpan balik belum aktif. Silakan jalankan migrasi.');
            return;
        }

        $validated = $this->validate([
            'feedbackName' => ['required', 'string', 'max:120'],
            'feedbackEmail' => ['required', 'email', 'max:120'],
            'feedbackSubject' => ['required', 'string', 'max:160'],
            'feedbackMessage' => ['required', 'string', 'max:2000'],
        ]);

        ContactFeedback::query()->create([
            'name' => $validated['feedbackName'],
            'email' => $validated['feedbackEmail'],
            'subject' => $validated['feedbackSubject'],
            'message' => $validated['feedbackMessage'],
        ]);

        $this->reset('feedbackName', 'feedbackEmail', 'feedbackSubject', 'feedbackMessage');
        session()->flash('contact_status', 'Terima kasih. Umpan balik Anda sudah terkirim.');
    }

    private function formatStatistikAktif(DashboardYearStat $stat, ?array $trendOverride = null): array
    {
        $kpi = collect($stat->kpi)
            ->values()
            ->map(function (array $row, int $index): array {
                $decimals = (int) ($row['decimals'] ?? 0);
                $value = (float) ($row['value'] ?? 0);
                $classes = self::KPI_CLASSES[$index] ?? self::KPI_CLASSES[0];

                return [
                    'label' => (string) ($row['label'] ?? ''),
                    'countTarget' => $value,
                    'decimals' => $decimals,
                    'value' => number_format($value, $decimals, ',', '.'),
                    'boxClass' => $classes['boxClass'],
                    'valueClass' => $classes['valueClass'],
                ];
            })
            ->all();

        $capaian = collect($stat->capaian)
            ->values()
            ->map(function (array $row, int $index): array {
                $classes = self::CAPAIAN_CLASSES[$index] ?? self::CAPAIAN_CLASSES[0];

                return [
                    'label' => (string) ($row['label'] ?? ''),
                    'percent' => (float) ($row['percent'] ?? 0),
                    'trackClass' => $classes['trackClass'],
                    'barClass' => $classes['barClass'],
                    'textClass' => $classes['textClass'],
                ];
            })
            ->all();

        return [
            'kpi' => $kpi,
            'trend' => [
                'mahasiswa' => (string) data_get($trendOverride, 'mahasiswa', data_get($stat->trend, 'mahasiswa', '')),
                'ipk' => (string) data_get($trendOverride, 'ipk', data_get($stat->trend, 'ipk', '')),
                'publikasi' => (string) data_get($trendOverride, 'publikasi', data_get($stat->trend, 'publikasi', '')),
                'dosen' => (string) data_get($trendOverride, 'dosen', data_get($stat->trend, 'dosen', '')),
                'progressYtd' => (string) data_get($trendOverride, 'progressYtd', ''),
                'lastX' => (float) data_get($trendOverride, 'lastX', self::CHART_X_END),
                'mahasiswaLastY' => (float) data_get($trendOverride, 'mahasiswaLastY', data_get($stat->trend, 'mahasiswaLastY', 90)),
                'ipkLastY' => (float) data_get($trendOverride, 'ipkLastY', data_get($stat->trend, 'ipkLastY', 90)),
                'publikasiLastY' => (float) data_get($trendOverride, 'publikasiLastY', data_get($stat->trend, 'publikasiLastY', 90)),
                'dosenLastY' => (float) data_get($trendOverride, 'dosenLastY', data_get($stat->trend, 'dosenLastY', 90)),
                'progressLastY' => (float) data_get($trendOverride, 'progressLastY', 90),
                'trendMode' => (string) data_get($trendOverride, 'trendMode', $this->trendMode),
                'rangeLabel' => (string) data_get($trendOverride, 'rangeLabel', 'Tahun ' . $stat->year),
                'yearTicks' => data_get($trendOverride, 'yearTicks', []),
                'yTicks' => data_get($trendOverride, 'yTicks', []),
                'tooltipData' => data_get($trendOverride, 'tooltipData', []),
            ],
            'capaian' => $capaian,
        ];
    }

    private function buildTrendFromHistory(\Illuminate\Support\Collection $stats, int $activeYear, string $mode = 'year'): array
    {
        if ($mode === 'year') {
            return $this->buildTrendFromMonthly($stats, $activeYear);
        }

        return $this->buildTrendFromYearly($stats, $activeYear);
    }

    /**
     * Mode "Per Tahun": gunakan data 12 bulan dari DashboardMonthlyStat.
     * Hasilkan flat structure yang kompatibel dengan formatStatistikAktif (kunci lama).
     *
     * Setiap seri mendapat skala Y-sendiri sehingga pergerakan nilai yang kecil
     * (IPK 3.15–3.62, Dosen 7–15) tetap terlihat naik-turun dengan jelas.
     */
    private function buildTrendFromMonthly(\Illuminate\Support\Collection $stats, int $activeYear): array
    {
        $statAktif = $stats->firstWhere('year', $activeYear);
        $annualKpi = $statAktif?->kpi ?? [];

        DashboardMonthlyStat::ensureYear($activeYear, $annualKpi);

        $rows = DashboardMonthlyStat::query()
            ->where('year', $activeYear)
            ->orderBy('month')
            ->get();

        if ($rows->isEmpty()) {
            return $this->buildFallbackTrendFlat('year', $activeYear);
        }

        // Tahun historis → semua 12 bulan; tahun berjalan → s.d. bulan sekarang
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

        // Publikasi kumulatif — chart menggambar garis kumulatif
        $publikasiValues = [];
        $cum = 0.0;
        foreach ($pubPerMonth as $v) { $cum += $v; $publikasiValues[] = $cum; }

        // ── Skala per-seri agar setiap garis terlihat bergerak ──
        $scaleWithPad = function (array $vals, float $relPad = 0.10, float $absMin = 1.0): array {
            $min = (float) min($vals);
            $max = (float) max($vals);
            $pad = max($absMin, ($max - $min) * $relPad);
            return [$min - $pad, $max + $pad];
        };

        [$mhsMin, $mhsMax]  = $scaleWithPad($mahasiswaValues);
        [$ipkMin, $ipkMax]  = $scaleWithPad($ipkValues, 0.15, 0.05);
        [$dosMin, $dosMax]  = $scaleWithPad($dosenValues);
        [$pubMin, $pubMax]  = $scaleWithPad($publikasiValues);

        // Progres per bulan — skala 0–100
        $progressValues = $active->map(function ($r) use ($annualKpi): float {
            $kpi = $r->kpi ?? [];
            $progresses = [];
            foreach ([['mahasiswa_aktif','0'],['ipk','1'],['dosen_tetap','2']] as [$key, $idx]) {
                $target = (float) data_get($annualKpi, $idx . '.value', 0);
                if ($target > 0) {
                    $progresses[] = min(100, ((float) data_get($kpi, $key, 0) / $target) * 100);
                }
            }
            return count($progresses) > 0 ? round(array_sum($progresses) / count($progresses), 1) : 50.0;
        })->values()->all();

        [$mahasiswaPolyline, $mahasiswaLastY] = $this->buildPolylineFromValues($mahasiswaValues, $mhsMin, $mhsMax);
        [$ipkPolyline,       $ipkLastY]       = $this->buildPolylineFromValues($ipkValues,       $ipkMin, $ipkMax);
        [$dosenPolyline,     $dosenLastY]      = $this->buildPolylineFromValues($dosenValues,     $dosMin, $dosMax);
        [$publikasiPolyline, $publikasiLastY]  = $this->buildPolylineFromValues($publikasiValues, $pubMin, $pubMax);
        [$progressPolyline,  $progressLastY]   = $this->buildPolylineFromValues($progressValues,  0.0, 105.0);

        $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $xTicks = $this->buildMonthTicksFlat($months, $monthNames);
        // Y-ticks derived from mahasiswa scale (dominant series in the chart)
        $yTicks = $this->buildValueTicks($mhsMin, $mhsMax);

        return [
            'mahasiswa'      => $mahasiswaPolyline,
            'ipk'            => $ipkPolyline,
            'publikasi'      => $publikasiPolyline,
            'dosen'          => $dosenPolyline,
            'progressYtd'    => $progressPolyline,
            'lastX'          => self::CHART_X_END,
            'mahasiswaLastY' => $mahasiswaLastY,
            'ipkLastY'       => $ipkLastY,
            'publikasiLastY' => $publikasiLastY,
            'dosenLastY'     => $dosenLastY,
            'progressLastY'  => $progressLastY,
            'trendMode'      => 'year',
            'rangeLabel'     => 'Per Bulan — Tahun ' . $activeYear,
            'yearTicks'      => $xTicks,
            'yTicks'         => $yTicks,
            // Tooltip: publikasi = kumulatif (sesuai dengan garis yang digambar)
            'tooltipData'    => $this->buildTooltipData(
                $months, $mahasiswaValues, $ipkValues, $dosenValues,
                $publikasiValues,  // kumulatif — sinkron dengan garis
                $progressValues, true,
            ),
        ];
    }

    /**
     * Mode "Semua Tahun": 1 titik per tahun (flat structure lama).
     */
    private function buildTrendFromYearly(\Illuminate\Support\Collection $stats, int $activeYear): array
    {
        $sorted = $stats->sortBy('year')->values();

        if ($sorted->isEmpty()) {
            return $this->buildFallbackTrendFlat('all', $activeYear);
        }

        if ($sorted->count() === 1) {
            $sorted = collect([$sorted->first(), $sorted->first()]);
        }

        $years           = $sorted->pluck('year')->map(fn($y) => (int) $y)->values()->all();
        $mahasiswaValues = $sorted->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '0.value', 0))->values()->all();
        $ipkValues       = $sorted->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '1.value', 0))->values()->all();
        $dosenValues     = $sorted->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '2.value', 0))->values()->all();
        $publikasiValues = $sorted->map(fn(DashboardYearStat $s) => (float) data_get($s->kpi, '3.value', 0))->values()->all();
        $progressValues  = $sorted->map(function (DashboardYearStat $s): float {
            $ringkasan = $this->buildKinerjaTahunanBerjalan((int) $s->year, $s->kpi ?? []);
            return (float) collect(data_get($ringkasan, 'items', []))->avg('progress');
        })->values()->all();

        // Per-series scale so small-range indicators (IPK, Dosen) still show movement
        $scaleWithPad = function (array $vals, float $relPad = 0.10, float $absMin = 1.0): array {
            $min = (float) min($vals);
            $max = (float) max($vals);
            $pad = max($absMin, ($max - $min) * $relPad);
            return [$min - $pad, $max + $pad];
        };

        [$mhsMin, $mhsMax]  = $scaleWithPad($mahasiswaValues);
        [$ipkMin, $ipkMax]  = $scaleWithPad($ipkValues, 0.15, 0.05);
        [$dosMin, $dosMax]  = $scaleWithPad($dosenValues);
        [$pubMin, $pubMax]  = $scaleWithPad($publikasiValues);

        [$mahasiswaPolyline, $mahasiswaLastY] = $this->buildPolylineFromValues($mahasiswaValues, $mhsMin, $mhsMax);
        [$ipkPolyline,       $ipkLastY]       = $this->buildPolylineFromValues($ipkValues,       $ipkMin, $ipkMax);
        [$dosenPolyline,     $dosenLastY]      = $this->buildPolylineFromValues($dosenValues,     $dosMin, $dosMax);
        [$publikasiPolyline, $publikasiLastY]  = $this->buildPolylineFromValues($publikasiValues, $pubMin, $pubMax);
        [$progressPolyline,  $progressLastY]   = $this->buildPolylineFromValues($progressValues,  0.0, 105.0);

        $firstYear = (int) ($years[0] ?? $activeYear);
        $lastYear  = (int) ($years[count($years) - 1] ?? $activeYear);
        $axis = $this->buildTrendAxis($years, $mhsMin, $mhsMax);

        return [
            'mahasiswa'      => $mahasiswaPolyline,
            'ipk'            => $ipkPolyline,
            'publikasi'      => $publikasiPolyline,
            'dosen'          => $dosenPolyline,
            'progressYtd'    => $progressPolyline,
            'lastX'          => self::CHART_X_END,
            'mahasiswaLastY' => $mahasiswaLastY,
            'ipkLastY'       => $ipkLastY,
            'publikasiLastY' => $publikasiLastY,
            'dosenLastY'     => $dosenLastY,
            'progressLastY'  => $progressLastY,
            'trendMode'      => 'all',
            'rangeLabel'     => count($years) > 1 ? 'Semua Tahun (' . $firstYear . ' – ' . $lastYear . ')' : 'Per Tahun ' . $firstYear,
            'yearTicks'      => $axis['yearTicks'],
            'yTicks'         => $axis['yTicks'],
            'tooltipData'    => $this->buildTooltipData($years, $mahasiswaValues, $ipkValues, $dosenValues, $publikasiValues, $progressValues, false),
        ];
    }

    private function buildFallbackTrendFlat(string $mode, int $activeYear): array
    {
        $midY = (self::CHART_Y_TOP + self::CHART_Y_BOTTOM) / 2;
        $flat = self::CHART_X_START . ',' . $midY . ' ' . self::CHART_X_END . ',' . $midY;
        return [
            'mahasiswa' => $flat, 'ipk' => $flat, 'publikasi' => $flat, 'dosen' => $flat, 'progressYtd' => $flat,
            'lastX' => self::CHART_X_END,
            'mahasiswaLastY' => $midY, 'ipkLastY' => $midY, 'publikasiLastY' => $midY,
            'dosenLastY' => $midY, 'progressLastY' => $midY,
            'trendMode' => $mode,
            'rangeLabel' => $mode === 'all' ? 'Semua Tahun' : 'Per Tahun ' . $activeYear,
            'yearTicks' => [], 'yTicks' => [], 'tooltipData' => [],
        ];
    }

    /** Build tooltip data as JSON-safe array of point info */
    private function buildTooltipData(
        array $xLabels,
        array $mahasiswaValues,
        array $ipkValues,
        array $dosenValues,
        array $publikasiValues,
        array $progressValues,
        bool  $isMonthly,
    ): array {
        $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $total = count($xLabels);
        $data = [];

        for ($i = 0; $i < $total; $i++) {
            $label = $isMonthly
                ? ($monthNames[($xLabels[$i] - 1)] ?? '-')
                : (string) $xLabels[$i];

            $data[] = [
                'label'     => $label,
                'mahasiswa' => $mahasiswaValues[$i] ?? 0,
                'ipk'       => $ipkValues[$i] ?? 0,
                'dosen'     => $dosenValues[$i] ?? 0,
                'publikasi' => $publikasiValues[$i] ?? 0,
                'progress'  => round($progressValues[$i] ?? 0, 1),
            ];
        }

        return $data;
    }

    private function buildMonthTicksFlat(array $months, array $names): array
    {
        $total = count($months);
        if ($total === 0) return [];

        $step = $total > 1 ? (self::CHART_X_END - self::CHART_X_START) / ($total - 1) : 0;
        $maxLabels = 6;
        $indexes = $total <= $maxLabels
            ? range(0, $total - 1)
            : collect(range(0, $maxLabels - 1))
                ->map(fn(int $i) => (int) round($i * ($total - 1) / ($maxLabels - 1)))
                ->unique()->values()->all();

        return collect($indexes)->map(fn(int $idx) => [
            'x'     => round(self::CHART_X_START + ($idx * $step), 1),
            'label' => $names[($months[$idx] - 1)] ?? '-',
            'year'  => $names[($months[$idx] - 1)] ?? '-', // backward-compat alias
        ])->all();
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

    private function loadProgramAgenda(): array
    {
        $items = DashboardProgramItem::query()
            ->where('year', $this->tahunDipilih)
            ->orderBy('sort_order', 'asc')
            ->get();

        if ($items->isEmpty()) {
            $items = DashboardProgramItem::query()->orderByDesc('year')->orderBy('sort_order', 'asc')->get();
        }

        return $items
            ->map(function (DashboardProgramItem $item): array {
                $classes = self::PROGRAM_STYLE_MAP[$item->style_key] ?? self::PROGRAM_STYLE_MAP['blue'];

                return [
                    'detail_url' => route('program-agenda.detail', [
                        'id' => $item->id,
                        'slug' => \Illuminate\Support\Str::slug($item->title),
                    ]),
                    'tipe' => $item->type,
                    'title' => $item->title,
                    'description' => $item->description,
                    'boxClass' => $classes['boxClass'],
                    'badgeClass' => $classes['badgeClass'],
                    'status_key' => in_array($item->execution_status, ['terlaksana', 'belum_terlaksana'], true) ? $item->execution_status : 'belum_terlaksana',
                    'status_label' => self::EXECUTION_STATUS_MAP[in_array($item->execution_status, ['terlaksana', 'belum_terlaksana'], true) ? $item->execution_status : 'belum_terlaksana']['label'],
                    'statusBadgeClass' => self::EXECUTION_STATUS_MAP[in_array($item->execution_status, ['terlaksana', 'belum_terlaksana'], true) ? $item->execution_status : 'belum_terlaksana']['badgeClass'],
                ];
            })
            ->all();
    }

    private function buildMitraDanKegiatanStats(array $homeContent, array $programAgendaItems): array
    {
        $mitraAktif = collect($homeContent['gallery_items'] ?? [])
            ->where('category', 'Kerjasama & MoU')
            ->count();

        $kegiatanEksternal = collect($programAgendaItems)
            ->where('tipe', 'Agenda')
            ->count();

        return [
            'mitraAktif' => $mitraAktif,
            'kegiatanEksternal' => $kegiatanEksternal,
            'tahun' => $this->tahunDipilih,
        ];
    }

    private function buildKinerjaTahunanBerjalan(int $year, array $annualKpi): array
    {
        DashboardMonthlyStat::ensureYear($year, $annualKpi);
        return DashboardMonthlyStat::summarizeYear($year, $annualKpi);
    }

    /**
     * Identical logic to StatistikPage::buildChartJsData — kept in sync.
     * Returns Chart.js-ready datasets for beranda trend chart.
     */
    private function buildChartJsData(\Illuminate\Support\Collection $stats, int $activeYear, string $mode): array
    {
        $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $empty = ['labels'=>[],'datasets'=>[]];

        if ($mode === 'year') {
            $annualKpi = $stats->firstWhere('year', $activeYear)?->kpi ?? [];
            DashboardMonthlyStat::ensureYear($activeYear, $annualKpi);

            $cutoff = $activeYear === (int) now()->format('Y') ? (int) now()->format('n') : 12;
            $rows = DashboardMonthlyStat::query()
                ->where('year', $activeYear)->orderBy('month')->get()
                ->filter(fn($r) => $r->month <= $cutoff)->values();

            if ($rows->isEmpty()) {
                return ['rangeLabel'=>'Per Bulan — '.$activeYear,'trendMode'=>'year',
                        'chartA'=>$empty,'chartB'=>$empty,'chartAll'=>$empty];
            }

            $labels = $rows->map(fn($r) => $monthNames[(int)$r->month - 1])->all();
            $mhs    = $rows->map(fn($r) => (float) data_get($r->kpi,'mahasiswa_aktif',0))->all();
            $ipk    = $rows->map(fn($r) => (float) data_get($r->kpi,'ipk',0))->all();
            $dos    = $rows->map(fn($r) => (float) data_get($r->kpi,'dosen_tetap',0))->all();
            $pub    = []; $cum = 0.0;
            foreach ($rows as $r) { $cum += (float) data_get($r->kpi,'publikasi',0); $pub[] = round($cum,1); }

            return [
                'rangeLabel' => 'Per Bulan — Tahun '.$activeYear,
                'trendMode'  => 'year',
                'chartA' => ['labels'=>$labels,'datasets'=>[
                    ['label'=>'Mahasiswa Aktif','data'=>$mhs,'color'=>'#2563eb','fill'=>true],
                    ['label'=>'Dosen Tetap',    'data'=>$dos,'color'=>'#0f766e','fill'=>false],
                ]],
                'chartB' => ['labels'=>$labels,'datasets'=>[
                    ['label'=>'IPK Rata-rata',        'data'=>$ipk,'color'=>'#0f766e','fill'=>true,'yAxis'=>'y'],
                    ['label'=>'Publikasi (kumulatif)','data'=>$pub,'color'=>'#f97316','fill'=>false,'dash'=>[6,4],'yAxis'=>'y2'],
                ]],
                // Single combined chart for beranda — Mhs/Dos/Pub left axis, IPK right axis
                'chartAll' => ['labels'=>$labels,'datasets'=>[
                    ['label'=>'Mahasiswa Aktif',  'data'=>$mhs,'color'=>'#2563eb','fill'=>false,'yAxis'=>'y'],
                    ['label'=>'Dosen Tetap',       'data'=>$dos,'color'=>'#0f766e','fill'=>false,'yAxis'=>'y'],
                    ['label'=>'Pub. (kumulatif)',  'data'=>$pub,'color'=>'#f97316','fill'=>false,'dash'=>[5,3],'yAxis'=>'y'],
                    ['label'=>'IPK Rata-rata',     'data'=>$ipk,'color'=>'#7c3aed','fill'=>false,'dash'=>[3,2],'yAxis'=>'y2'],
                ]],
            ];
        }

        $sorted = $stats->sortBy('year')->values();
        if ($sorted->isEmpty()) {
            return ['rangeLabel'=>'Semua Tahun','trendMode'=>'all',
                    'chartA'=>$empty,'chartB'=>$empty,'chartAll'=>$empty];
        }

        $years = $sorted->pluck('year')->map(fn($y) => (string)$y)->all();
        $mhs   = $sorted->map(fn($s) => (float) data_get($s->kpi,'0.value',0))->all();
        $ipk   = $sorted->map(fn($s) => (float) data_get($s->kpi,'1.value',0))->all();
        $dos   = $sorted->map(fn($s) => (float) data_get($s->kpi,'2.value',0))->all();
        $pub   = $sorted->map(fn($s) => (float) data_get($s->kpi,'3.value',0))->all();
        $first = $years[0] ?? $activeYear;
        $last  = end($years) ?: $activeYear;

        return [
            'rangeLabel' => 'Semua Tahun ('.$first.' – '.$last.')',
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

    public function render()
    {
        $homeContent = HomePageSetting::current();

        if (!Schema::hasTable('dashboard_year_stats') || !Schema::hasTable('dashboard_program_items')) {
            $defaults = DashboardYearStat::defaults();
            $years = array_keys($defaults);
            rsort($years);
            $activeYear = $this->tahunDipilih;
            if (!array_key_exists($activeYear, $defaults)) {
                $activeYear = $years[0];
            }

            $fallbackStat = new DashboardYearStat([
                'year' => $activeYear,
                'kpi' => $defaults[$activeYear]['kpi'],
                'trend' => $defaults[$activeYear]['trend'],
                'capaian' => $defaults[$activeYear]['capaian'],
            ]);

            $fallbackPrograms = collect(DashboardProgramItem::defaults())
                ->map(function (array $item): array {
                    $classes = self::PROGRAM_STYLE_MAP[$item['style_key']] ?? self::PROGRAM_STYLE_MAP['blue'];

                    return [
                        'detail_url' => '#',
                        'tipe' => $item['type'],
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'boxClass' => $classes['boxClass'],
                        'badgeClass' => $classes['badgeClass'],
                        'status_key' => in_array(($item['execution_status'] ?? ''), ['terlaksana', 'belum_terlaksana'], true) ? $item['execution_status'] : 'belum_terlaksana',
                        'status_label' => self::EXECUTION_STATUS_MAP[$item['execution_status'] ?? 'belum_terlaksana']['label'] ?? self::EXECUTION_STATUS_MAP['belum_terlaksana']['label'],
                        'statusBadgeClass' => self::EXECUTION_STATUS_MAP[$item['execution_status'] ?? 'belum_terlaksana']['badgeClass'] ?? self::EXECUTION_STATUS_MAP['belum_terlaksana']['badgeClass'],
                    ];
                })
                ->all();

            $profileSections = [];
            if (Schema::hasTable('profile_sections')) {
                $profileSections = ProfileSection::allOrdered()
                    ->map(fn($s) => [
                        'slug' => $s->slug,
                        'title' => $s->title,
                        'summary' => $s->summary,
                        'color_class' => $s->color_class,
                        'icon_key' => $s->icon_key,
                    ])
                    ->toArray();
            }

            $mitraDanKegiatanStats = $this->buildMitraDanKegiatanStats($homeContent, $fallbackPrograms);
            $kinerjaTahunanBerjalan = $this->buildKinerjaTahunanBerjalan($activeYear, $fallbackStat->kpi ?? []);

            return view('livewire.pages.beranda-page', [
                'daftarTahun' => $years,
                'statistikAktif' => $this->formatStatistikAktif($fallbackStat),
                'programAgendaItems' => $fallbackPrograms,
                'homeContent' => $homeContent,
                'profileSections' => $profileSections,
                'mitraDanKegiatanStats' => $mitraDanKegiatanStats,
                'kinerjaTahunanBerjalan' => $kinerjaTahunanBerjalan,
            ]);
        }

        $allStats = DashboardYearStat::query()->orderByDesc('year')->get();
        $statistikAktif = $allStats->firstWhere('year', $this->tahunDipilih) ?? $allStats->first();

        $profileSections = [];
        if (Schema::hasTable('profile_sections')) {
            $profileSections = ProfileSection::allOrdered()
                ->map(fn($s) => [
                    'slug' => $s->slug,
                    'title' => $s->title,
                    'summary' => $s->summary,
                    'color_class' => $s->color_class,
                    'icon_key' => $s->icon_key,
                ])
                ->toArray();
        }

        $programAgendaItems = $this->loadProgramAgenda();
        $mitraDanKegiatanStats = $this->buildMitraDanKegiatanStats($homeContent, $programAgendaItems);
        $kinerjaTahunanBerjalan = $this->buildKinerjaTahunanBerjalan((int) ($statistikAktif?->year ?? $this->tahunDipilih), $statistikAktif?->kpi ?? []);

        $activeYear = (int) ($statistikAktif?->year ?? $this->tahunDipilih);

        $trendData = $this->buildTrendFromHistory(
            $allStats->sortBy('year')->values(),
            $activeYear,
            $this->trendMode,
        );

        // Build Chart.js data using the same logic as StatistikPage
        $chartJsData = $this->buildChartJsData(
            $allStats->sortBy('year')->values(),
            $activeYear,
            $this->trendMode,
        );

        // chartJsAll passed directly to view (no longer needs $wire exposure)
        $this->chartJsAll = data_get($chartJsData, 'chartAll', ['labels'=>[],'datasets'=>[]]);

        return view('livewire.pages.beranda-page', [
            'daftarTahun'          => $allStats->pluck('year')->all(),
            'statistikAktif'       => $this->formatStatistikAktif($statistikAktif, $trendData),
            'chartJsData'          => $chartJsData,
            'programAgendaItems'   => $programAgendaItems,
            'homeContent'          => $homeContent,
            'profileSections'      => $profileSections,
            'mitraDanKegiatanStats' => $mitraDanKegiatanStats,
            'kinerjaTahunanBerjalan' => $kinerjaTahunanBerjalan,
        ]);
    }
}
