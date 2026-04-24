<?php

namespace App\Livewire\Pages;

use App\Models\AnnualReportSection;
use App\Models\DashboardYearStat;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Laporan Tahunan')]
class AdminAnnualReportPage extends Component
{
    public int $tahunDipilih = 0;
    public array $sections = [];

    public function mount(): void
    {
        if ($this->tahunDipilih <= 0) {
            $this->tahunDipilih = (int) now()->format('Y');
        }

        DashboardYearStat::ensureDefaults();
        AnnualReportSection::ensureDefaults();

        $tahunTerbaru = DashboardYearStat::query()->max('year');
        if (is_numeric($tahunTerbaru)) {
            $this->tahunDipilih = (int) $tahunTerbaru;
        }

        $this->loadSections();
    }

    public function pilihTahun(int $tahun): void
    {
        $this->tahunDipilih = $tahun;
        AnnualReportSection::ensureYear($tahun);
        $this->loadSections();
    }

    public function simpan(): void
    {
        $this->validate([
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.id' => ['nullable', 'integer'],
            'sections.*.title' => ['required', 'string', 'max:180'],
            'sections.*.summary' => ['nullable', 'string', 'max:500'],
            'sections.*.content' => ['nullable', 'string'],
        ]);

        foreach ($this->sections as $index => $section) {
            AnnualReportSection::query()->updateOrCreate(
                ['id' => data_get($section, 'id')],
                [
                    'year' => $this->tahunDipilih,
                    'section_key' => (string) data_get($section, 'section_key'),
                    'title' => (string) data_get($section, 'title'),
                    'summary' => (string) data_get($section, 'summary'),
                    'content' => (string) data_get($section, 'content'),
                    'sort_order' => $index + 1,
                ]
            );
        }

        $this->loadSections();
        $this->flashStatus('Konten laporan tahunan berhasil diperbarui.');
    }

    private function loadSections(): void
    {
        $this->sections = AnnualReportSection::forYear($this->tahunDipilih)
            ->map(fn(AnnualReportSection $section) => [
                'id' => $section->id,
                'section_key' => $section->section_key,
                'title' => $section->title,
                'summary' => $section->summary,
                'content' => $section->content,
            ])
            ->all();
    }

    private function flashStatus(string $message): void
    {
        session()->flash('status', $message);
        $this->dispatch('admin-toast', message: $message);
    }

    public function render()
    {
        return view('livewire.pages.admin-annual-report-page', [
            'daftarTahun' => DashboardYearStat::query()->orderByDesc('year')->pluck('year')->all(),
        ]);
    }
}
