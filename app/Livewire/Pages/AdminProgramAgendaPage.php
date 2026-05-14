<?php

namespace App\Livewire\Pages;

use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use Illuminate\Support\Facades\Schema;
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

        $payload = [
            'year' => $this->tahunDipilih,
            'type' => 'Agenda',
            'title' => 'Agenda Baru Tahun ' . $this->tahunDipilih,
            'description' => 'Isi deskripsi agenda terbaru untuk tahun ' . $this->tahunDipilih . '.',
            'style_key' => 'amber',
            'sort_order' => $nextSortOrder,
        ];

        if ($this->hasExecutionStatusColumn()) {
            $payload['execution_status'] = 'belum_terlaksana';
        }

        DashboardProgramItem::query()->create($payload);

        $this->loadProgramItems();
        $this->flashStatus('Agenda baru berhasil ditambahkan.');
    }

    public function hapusAgenda(int $index): void
    {
        $itemId = data_get($this->programItems, $index . '.id');
        if (!$itemId) {
            return;
        }

        $totalItems = DashboardProgramItem::query()->where('year', $this->tahunDipilih)->count('id');
        if ($totalItems <= 1) {
            $this->flashStatus('Minimal satu item program/agenda harus tetap tersedia.');
            return;
        }

        DashboardProgramItem::query()->whereKey($itemId)->delete();

        DashboardProgramItem::query()
            ->where('year', $this->tahunDipilih)
            ->orderBy('sort_order', 'asc')
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
        $hasExecutionStatusColumn = $this->hasExecutionStatusColumn();

        foreach ($this->programItems as $index => $item) {
            $rules = [
                "programItems.$index.id" => ['required', 'integer'],
                "programItems.$index.type" => ['required', 'string'],
                "programItems.$index.title" => ['required', 'string'],
                "programItems.$index.description" => ['required', 'string'],
                "programItems.$index.style_key" => ['required', 'string'],
            ];

            if ($hasExecutionStatusColumn) {
                $rules["programItems.$index.execution_status"] = ['required', 'in:terlaksana,belum_terlaksana'];
            }

            $this->validate($rules);

            $payload = [
                'year' => $this->tahunDipilih,
                'type' => $item['type'],
                'title' => $item['title'],
                'description' => $item['description'],
                'style_key' => $item['style_key'],
                'sort_order' => $index + 1,
            ];

            if ($hasExecutionStatusColumn) {
                $payload['execution_status'] = $item['execution_status'] ?? 'belum_terlaksana';
            }

            DashboardProgramItem::query()->whereKey($item['id'])->update($payload);
        }

        $this->loadProgramItems();
        $this->flashStatus('Program dan agenda berhasil disimpan.');
    }

    public function naikItem(int $index): void
    {
        if ($index <= 0 || $index >= count($this->programItems)) {
            return;
        }

        $item = data_get($this->programItems, $index);
        $itemId = data_get($item, 'id');
        if (!$itemId) {
            return;
        }

        $prevItem = data_get($this->programItems, $index - 1);
        $prevItemId = data_get($prevItem, 'id');
        if (!$prevItemId) {
            return;
        }

        DashboardProgramItem::query()->whereKey($itemId)->update(['sort_order' => $index]);
        DashboardProgramItem::query()->whereKey($prevItemId)->update(['sort_order' => $index + 1]);

        $this->loadProgramItems();
        $this->flashStatus('Item berhasil dipindahkan ke atas.');
    }

    public function turunItem(int $index): void
    {
        if ($index < 0 || $index >= count($this->programItems) - 1) {
            return;
        }

        $item = data_get($this->programItems, $index);
        $itemId = data_get($item, 'id');
        if (!$itemId) {
            return;
        }

        $nextItem = data_get($this->programItems, $index + 1);
        $nextItemId = data_get($nextItem, 'id');
        if (!$nextItemId) {
            return;
        }

        DashboardProgramItem::query()->whereKey($itemId)->update(['sort_order' => $index + 2]);
        DashboardProgramItem::query()->whereKey($nextItemId)->update(['sort_order' => $index + 1]);

        $this->loadProgramItems();
        $this->flashStatus('Item berhasil dipindahkan ke bawah.');
    }

    private function loadProgramItems(): void
    {
        $columns = ['id', 'year', 'type', 'title', 'description', 'style_key'];
        if ($this->hasExecutionStatusColumn()) {
            $columns[] = 'execution_status';
        }

        $this->programItems = DashboardProgramItem::query()
            ->where('year', $this->tahunDipilih)
            ->orderBy('sort_order', 'asc')
            ->get($columns)
            ->map(function (DashboardProgramItem $item): array {
                $payload = $item->toArray();
                $payload['execution_status'] = in_array(($payload['execution_status'] ?? ''), ['terlaksana', 'belum_terlaksana'], true)
                    ? $payload['execution_status']
                    : 'belum_terlaksana';

                return $payload;
            })
            ->toArray();
    }

    private function hasExecutionStatusColumn(): bool
    {
        return Schema::hasTable('dashboard_program_items')
            && Schema::hasColumn('dashboard_program_items', 'execution_status');
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
