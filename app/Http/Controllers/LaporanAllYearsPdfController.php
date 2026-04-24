<?php

namespace App\Http\Controllers;

use App\Models\AnnualReportSection;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class LaporanAllYearsPdfController extends Controller
{
    public function __invoke(): Response
    {
        DashboardYearStat::ensureDefaults();
        DashboardProgramItem::ensureDefaults();
        AnnualReportSection::ensureDefaults();

        $years = DashboardYearStat::query()->orderByDesc('year')->pluck('year')->all();

        $reportBundles = collect($years)
            ->map(function (int $year): array {
                $stat = DashboardYearStat::query()->where('year', $year)->first();

                return [
                    'year' => $year,
                    'kpi' => $stat?->kpi ?? [],
                    'capaian' => $stat?->capaian ?? [],
                    'programItems' => DashboardProgramItem::query()
                        ->where('year', $year)
                        ->orderBy('sort_order')
                        ->get(['type', 'title', 'description']),
                    'sections' => AnnualReportSection::forYear($year),
                ];
            })
            ->all();

        $pdf = Pdf::loadView('pdf.laporan-semua-tahun', [
            'reportBundles' => $reportBundles,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-tahunan-semua-tahun.pdf');
    }
}
