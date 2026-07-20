<?php

namespace App\Livewire\Pages;

use App\Models\AnnualReportSection;
use App\Models\DashboardYearStat;
use App\Livewire\Concerns\UsesActiveProdi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Laporan Tahunan')]
class AdminAnnualReportPage extends Component
{
    use UsesActiveProdi;
    public int $tahunDipilih = 0;
    public array $sections = [];

    public function mount(): void
    {
        if ($this->tahunDipilih <= 0) {
            $this->tahunDipilih = (int) now()->format('Y');
        }

        DashboardYearStat::ensureDefaults();
        AnnualReportSection::ensureDefaults();

        $tahunTerbaru = $this->prodiQuery(DashboardYearStat::class)->max('year');
        if (is_numeric($tahunTerbaru)) {
            $this->tahunDipilih = (int) $tahunTerbaru;
        }

        $this->loadSections();
    }

    public function pilihTahun(int $tahun): void
    {
        $this->tahunDipilih = $tahun;
        $this->ensureSectionsForYear($tahun);
        $this->loadSections();
    }

    public function simpan(): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->flashStatus('Mengubah laporan tahunan hanya dapat dilakukan oleh Admin.');
            return;
        }

        $this->validate([
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.id' => ['nullable', 'integer'],
            'sections.*.title' => ['required', 'string', 'max:180'],
            'sections.*.summary' => ['nullable', 'string', 'max:500'],
            'sections.*.content' => ['nullable', 'string'],
        ]);

        foreach ($this->sections as $index => $section) {
            $this->prodiQuery(AnnualReportSection::class)->updateOrCreate(
                ['id' => data_get($section, 'id')],
                [
                    'prodi_id' => $this->activeProdiId(),
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
        $this->sections = $this->prodiQuery(AnnualReportSection::class)
            ->where('year', $this->tahunDipilih)
            ->orderBy('sort_order')
            ->get()
            ->map(fn(AnnualReportSection $section) => [
                'id' => $section->id,
                'section_key' => $section->section_key,
                'title' => $section->title,
                'summary' => $section->summary,
                'content' => $section->content,
            ])
            ->all();
    }

    private function ensureSectionsForYear(int $year): void
    {
        foreach (AnnualReportSection::defaultsForYear($year) as $payload) {
            $this->prodiQuery(AnnualReportSection::class)->firstOrCreate(
                ['year' => $year, 'section_key' => $payload['section_key']],
                [...$payload, 'prodi_id' => $this->activeProdiId()],
            );
        }
    }

    private function flashStatus(string $message): void
    {
        session()->flash('status', $message);
        $this->dispatch('admin-toast', message: $message);
    }

    public function render()
    {
        return view('livewire.pages.admin-annual-report-page', [
            'daftarTahun' => $this->prodiQuery(DashboardYearStat::class)->orderByDesc('year')->pluck('year')->all(),
        ]);
    }
}
