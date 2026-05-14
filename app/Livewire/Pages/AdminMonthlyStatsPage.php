<?php

namespace App\Livewire\Pages;

use App\Models\DashboardMonthlyStat;
use App\Models\DashboardYearStat;
use App\Models\DashboardProgramItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Bulanan Statistik')]
class AdminMonthlyStatsPage extends Component
{
    public int $tahunDipilih = 0;
    public array $bulanan = [];

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

        $this->ensureBulananYear();
        $this->loadBulananForm();
    }

    public function pilihTahun(int $tahun): void
    {
        $this->tahunDipilih = $tahun;
        $this->ensureBulananYear();
        $this->loadBulananForm();
    }

    private function ensureBulananYear(): void
    {
        $annualKpi = DashboardYearStat::query()->where('year', $this->tahunDipilih)->value('kpi');
        DashboardMonthlyStat::ensureYear($this->tahunDipilih, is_array($annualKpi) ? $annualKpi : []);
    }

    private function loadBulananForm(): void
    {
        $rows = DashboardMonthlyStat::query()
            ->where('year', $this->tahunDipilih)
            ->orderBy('month', 'asc')
            ->get();

        if ($rows->isEmpty()) {
            $this->bulanan = collect(range(1, 12))
                ->map(fn(int $month) => [
                    'month' => $month,
                    'month_label' => DashboardMonthlyStat::monthName($month),
                    'mahasiswa_aktif' => 0,
                    'ipk' => 0,
                    'dosen_tetap' => 0,
                    'publikasi' => 0,
                ])
                ->all();

            return;
        }

        $this->bulanan = $rows
            ->map(fn(DashboardMonthlyStat $row) => [
                'month' => (int) $row->month,
                'month_label' => DashboardMonthlyStat::monthName((int) $row->month),
                'mahasiswa_aktif' => (float) data_get($row->kpi, 'mahasiswa_aktif', 0),
                'ipk' => (float) data_get($row->kpi, 'ipk', 0),
                'dosen_tetap' => (float) data_get($row->kpi, 'dosen_tetap', 0),
                'publikasi' => (float) data_get($row->kpi, 'publikasi', 0),
            ])
            ->all();
    }

    public function simpanBulanan(): void
    {
        foreach ($this->bulanan as $index => $row) {
            $this->validate([
                "bulanan.$index.month" => ['required', 'integer', 'between:1,12'],
                "bulanan.$index.mahasiswa_aktif" => ['required', 'numeric', 'min:0'],
                "bulanan.$index.ipk" => ['required', 'numeric', 'between:0,4'],
                "bulanan.$index.dosen_tetap" => ['required', 'numeric', 'min:0'],
                "bulanan.$index.publikasi" => ['required', 'numeric', 'min:0'],
            ]);

            DashboardMonthlyStat::query()->updateOrCreate(
                [
                    'year' => $this->tahunDipilih,
                    'month' => (int) data_get($row, 'month'),
                ],
                [
                    'kpi' => [
                        'mahasiswa_aktif' => (float) data_get($row, 'mahasiswa_aktif', 0),
                        'ipk' => (float) data_get($row, 'ipk', 0),
                        'dosen_tetap' => (float) data_get($row, 'dosen_tetap', 0),
                        'publikasi' => (float) data_get($row, 'publikasi', 0),
                    ],
                ]
            );
        }

        $this->loadBulananForm();
        $this->dispatch('admin-toast', message: 'Data bulanan berhasil disimpan.');
    }

    public function render()
    {
        $years = DashboardYearStat::query()->orderByDesc('year')->pluck('year')->all();
        return view('livewire.pages.admin-monthly-stats-page', [
            'daftarTahun' => $years,
        ]);
    }
}
