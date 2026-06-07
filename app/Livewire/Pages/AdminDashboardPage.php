<?php

namespace App\Livewire\Pages;

use App\Models\AnnualReportSection;
use App\Models\ContactFeedback;
use App\Models\DashboardMonthlyStat;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use App\Models\DocumentItem;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard')]
class AdminDashboardPage extends Component
{
    public function render()
    {
        // ── Aggregate counts ──
        $totalTahun    = DashboardYearStat::query()->count();
        $tahunTerbaru  = DashboardYearStat::query()->max('year');
        $statTerbaru   = $tahunTerbaru
            ? DashboardYearStat::query()->where('year', $tahunTerbaru)->first()
            : null;

        $totalDokumen  = Schema::hasTable('document_items')    ? DocumentItem::query()->count()          : 0;
        $totalFeedback = Schema::hasTable('contact_feedback')  ? ContactFeedback::query()->count()       : 0;
        $totalProgram  = Schema::hasTable('dashboard_program_items')
            ? DashboardProgramItem::query()->where('year', $tahunTerbaru)->count()
            : 0;
        $totalLaporan  = Schema::hasTable('annual_report_sections')
            ? AnnualReportSection::query()->count()
            : 0;
        $unreadFeedback = Schema::hasTable('contact_feedback')
            ? ContactFeedback::query()->whereNull('read_at')->count()
            : 0;

        // ── KPI untuk tahun terbaru ──
        $kpiLatest = [
            'mahasiswa' => (float) data_get($statTerbaru?->kpi, '0.value', 0),
            'ipk'       => (float) data_get($statTerbaru?->kpi, '1.value', 0),
            'dosen'     => (float) data_get($statTerbaru?->kpi, '2.value', 0),
            'publikasi' => (float) data_get($statTerbaru?->kpi, '3.value', 0),
        ];

        // ── Tren 5 tahun terakhir (untuk line chart) ──
        $allStats = DashboardYearStat::query()->orderBy('year')->get();
        $trendData = $allStats->map(fn($s) => [
            'year'      => $s->year,
            'mahasiswa' => (float) data_get($s->kpi, '0.value', 0),
            'ipk'       => (float) data_get($s->kpi, '1.value', 0),
            'dosen'     => (float) data_get($s->kpi, '2.value', 0),
            'publikasi' => (float) data_get($s->kpi, '3.value', 0),
        ])->values()->all();

        // ── Polylines untuk mini line chart ──
        $charts = $this->buildMiniCharts($trendData);

        // ── Capaian terbaru ──
        $capaian = collect($statTerbaru?->capaian ?? [])->map(fn($c) => [
            'label'   => data_get($c, 'label', '-'),
            'percent' => (float) data_get($c, 'percent', 0),
        ])->all();

        // ── Feedback terbaru (5 terakhir) ──
        $recentFeedback = Schema::hasTable('contact_feedback')
            ? ContactFeedback::query()->orderByDesc('created_at')->limit(5)->get()
            : collect();

        return view('livewire.pages.admin-dashboard-page', [
            'tahunTerbaru'   => $tahunTerbaru,
            'totalTahun'     => $totalTahun,
            'totalDokumen'   => $totalDokumen,
            'totalFeedback'  => $totalFeedback,
            'unreadFeedback' => $unreadFeedback,
            'totalProgram'   => $totalProgram,
            'totalLaporan'   => $totalLaporan,
            'kpiLatest'      => $kpiLatest,
            'trendData'      => $trendData,
            'charts'         => $charts,
            'capaian'        => $capaian,
            'recentFeedback' => $recentFeedback,
        ]);
    }

    private function buildMiniCharts(array $trendData): array
    {
        if (count($trendData) < 2) {
            $flat = ['34,74 310,74'];
            return [
                'mahasiswa' => ['points' => $flat[0], 'lastY' => 74],
                'ipk'       => ['points' => $flat[0], 'lastY' => 74],
                'dosen'     => ['points' => $flat[0], 'lastY' => 74],
                'publikasi' => ['points' => $flat[0], 'lastY' => 74],
            ];
        }

        $build = function (array $vals): array {
            $min = min($vals); $max = max($vals);
            if ($max === $min) { $max = $min + 1; }
            $pad = max(0.5, ($max - $min) * 0.12);
            $min -= $pad; $max += $pad;
            $xStart = 34; $xEnd = 310; $yTop = 12; $yBot = 88;
            $step = ($xEnd - $xStart) / (count($vals) - 1);
            $pts = []; $lastY = 50.0;
            foreach ($vals as $i => $v) {
                $x = round($xStart + $i * $step, 1);
                $y = round($yBot - (($v - $min) / ($max - $min)) * ($yBot - $yTop), 1);
                $y = max($yTop, min($yBot, $y));
                $lastY = $y;
                $pts[] = "$x,$y";
            }
            return ['points' => implode(' ', $pts), 'lastY' => $lastY];
        };

        return [
            'mahasiswa' => $build(array_column($trendData, 'mahasiswa')),
            'ipk'       => $build(array_column($trendData, 'ipk')),
            'dosen'     => $build(array_column($trendData, 'dosen')),
            'publikasi' => $build(array_column($trendData, 'publikasi')),
        ];
    }
}
