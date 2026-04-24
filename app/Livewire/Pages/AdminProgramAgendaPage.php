<?php

namespace App\Livewire\Pages;

use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Program dan Agenda')]
class AdminProgramAgendaPage extends Component
{
    public int $tahunDipilih = 0;
    public array $programItems = [];

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
        DashboardProgramItem::ensureYear($this->tahunDipilih);
        $this->loadProgramItems();
    }

    public function pilihTahun(int $tahun): void
    {
        $this->tahunDipilih = $tahun;
        DashboardProgramItem::ensureYear($tahun);
        $this->loadProgramItems();
    }

    public function tambahAgenda(): void
    {
        $nextSortOrder = (int) DashboardProgramItem::query()
            ->where('year', $this->tahunDipilih)
            ->max('sort_order') + 1;

        DashboardProgramItem::query()->create([
            'year' => $this->tahunDipilih,
            'type' => 'Agenda',
            'title' => 'Agenda Baru Tahun ' . $this->tahunDipilih,
            'description' => 'Isi deskripsi agenda terbaru untuk tahun ' . $this->tahunDipilih . '.',
            'style_key' => 'amber',
            'sort_order' => $nextSortOrder,
        ]);

        $this->loadProgramItems();
        $this->flashStatus('Agenda baru berhasil ditambahkan.');
    }

    public function hapusAgenda(int $index): void
    {
        $itemId = data_get($this->programItems, $index . '.id');
        if (!$itemId) {
            return;
        }

        $totalItems = DashboardProgramItem::query()->where('year', $this->tahunDipilih)->count();
        if ($totalItems <= 1) {
            $this->flashStatus('Minimal satu item program/agenda harus tetap tersedia.');
            return;
        }

        DashboardProgramItem::query()->whereKey($itemId)->delete();

        DashboardProgramItem::query()
            ->where('year', $this->tahunDipilih)
            ->orderBy('sort_order')
            ->get()
            ->each(function (DashboardProgramItem $item, int $order): void {
                $item->sort_order = $order + 1;
                $item->save();
            });

        $this->loadProgramItems();
        $this->flashStatus('Agenda berhasil dihapus.');
    }

    public function simpanProgram(): void
    {
        foreach ($this->programItems as $index => $item) {
            $this->validate([
                "programItems.$index.id" => ['required', 'integer'],
                "programItems.$index.type" => ['required', 'string'],
                "programItems.$index.title" => ['required', 'string'],
                "programItems.$index.description" => ['required', 'string'],
                "programItems.$index.style_key" => ['required', 'string'],
            ]);

            DashboardProgramItem::query()->whereKey($item['id'])->update([
                'year' => $this->tahunDipilih,
                'type' => $item['type'],
                'title' => $item['title'],
                'description' => $item['description'],
                'style_key' => $item['style_key'],
                'sort_order' => $index + 1,
            ]);
        }

        $this->loadProgramItems();
        $this->flashStatus('Program dan agenda berhasil disimpan.');
    }

    private function loadProgramItems(): void
    {
        $this->programItems = DashboardProgramItem::query()
            ->where('year', $this->tahunDipilih)
            ->orderBy('sort_order')
            ->get(['id', 'year', 'type', 'title', 'description', 'style_key'])
            ->toArray();
    }

    private function flashStatus(string $message): void
    {
        session()->flash('status', $message);
        $this->dispatch('admin-toast', message: $message);
    }

    public function render()
    {
        return view('livewire.pages.admin-program-agenda-page', [
            'daftarTahun' => DashboardYearStat::query()->orderByDesc('year')->pluck('year')->all(),
            'styleOptions' => ['blue', 'violet', 'amber', 'rose'],
        ]);
    }
}
