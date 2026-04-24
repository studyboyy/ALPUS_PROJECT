<?php

namespace App\Http\Controllers;

use App\Models\AnnualReportSection;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LaporanPdfController extends Controller
{
    public function __invoke(Request $request): Response
    {
        DashboardYearStat::ensureDefaults();
        DashboardProgramItem::ensureDefaults();
        AnnualReportSection::ensureDefaults();

        $tahunAktif = (int) $request->integer('year', (int) DashboardYearStat::query()->max('year'));
        if (!DashboardYearStat::query()->where('year', $tahunAktif)->exists()) {
            $tahunAktif = (int) DashboardYearStat::query()->max('year');
        }

        $stat = DashboardYearStat::query()->where('year', $tahunAktif)->firstOrFail();

        $programItems = DashboardProgramItem::query()
            ->where('year', $tahunAktif)
            ->orderBy('sort_order')
            ->get(['type', 'title', 'description']);
        $sections = AnnualReportSection::forYear($tahunAktif);

        $pdf = Pdf::loadView('pdf.laporan-tahunan', [
            'tahun' => $tahunAktif,
            'kpi' => $stat->kpi,
            'capaian' => $stat->capaian,
            'sections' => $sections,
            'programItems' => $programItems,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-tahunan-prodi-' . $tahunAktif . '.pdf');
    }
}
