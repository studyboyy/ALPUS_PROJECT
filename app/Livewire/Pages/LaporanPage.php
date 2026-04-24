<?php

namespace App\Livewire\Pages;

use App\Models\AnnualReportSection;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Laporan Tahunan')]
class LaporanPage extends Component
{
    public int $tahunDipilih = 0;

    public function mount(Request $request): void
    {
        if ($this->tahunDipilih <= 0) {
            $this->tahunDipilih = (int) now()->format('Y');
        }

        DashboardYearStat::ensureDefaults();
        DashboardProgramItem::ensureDefaults();
        AnnualReportSection::ensureDefaults();

        $tahunTerbaru = DashboardYearStat::query()->max('year');
        if (is_numeric($tahunTerbaru)) {
            $this->tahunDipilih = (int) $tahunTerbaru;
        }

        $tahunRequest = (int) $request->integer('year');
        if ($tahunRequest > 0 && DashboardYearStat::query()->where('year', $tahunRequest)->exists()) {
            $this->tahunDipilih = $tahunRequest;
        }
    }

    public function pilihTahun(int $tahun): void
    {
        if (DashboardYearStat::query()->where('year', $tahun)->exists()) {
            $this->tahunDipilih = $tahun;
            AnnualReportSection::ensureYear($tahun);
        }
    }

    public function render()
    {
        $allStats = DashboardYearStat::query()->orderByDesc('year')->get();
        $laporanAktif = $allStats->firstWhere('year', $this->tahunDipilih) ?? $allStats->first();
        $sections = $laporanAktif ? AnnualReportSection::forYear((int) $laporanAktif->year) : collect();

        return view('livewire.pages.laporan-page', [
            'daftarTahun' => $allStats->pluck('year')->all(),
            'laporanAktif' => $laporanAktif,
            'sections' => $sections,
            'programItems' => DashboardProgramItem::query()->where('year', $laporanAktif?->year)->orderBy('sort_order')->get(['type', 'title', 'description'])->all(),
        ]);
    }
}
