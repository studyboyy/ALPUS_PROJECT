<?php

namespace App\Livewire\Pages;

use App\Livewire\Concerns\UsesActiveProdi;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Program dan Agenda')]
class AdminProgramAgendaPage extends Component
{
    use UsesActiveProdi;
    public int $tahunDipilih = 0;
    public array $programItems = [];
    #[Locked]
    public array $newItemIndexes = [];

    public function mount(): void
    {
        if ($this->tahunDipilih <= 0) {
            $this->tahunDipilih = (int) now()->format('Y');
        }

        DashboardYearStat::ensureDefaults();
        DashboardProgramItem::ensureDefaults();
        $tahunTerbaru = $this->prodiQuery(DashboardYearStat::class)->max('year');
        if (is_numeric($tahunTerbaru)) {
            $this->tahunDipilih = (int) $tahunTerbaru;
        }
        $this->ensureProgramYear($this->tahunDipilih);
        $this->loadProgramItems();
    }

    public function pilihTahun(int $tahun): void
    {
        $this->tahunDipilih = $tahun;
        $this->ensureProgramYear($tahun);
        $this->loadProgramItems();
    }

    public function tambahAgenda(): void
    {
        $nextSortOrder = (int) $this->prodiQuery(DashboardProgramItem::class)
            ->where('year', $this->tahunDipilih)
            ->max('sort_order') + 1;

        $payload = [
            'prodi_id' => $this->activeProdiId(),
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

        $payload['id'] = null;
        $this->programItems[] = $payload;
        $this->newItemIndexes[] = count($this->programItems) - 1;
        $this->flashStatus('Agenda baru siap diisi. Klik Simpan untuk mempublikasikannya.');
    }

    public function hapusAgenda(int $index): void
    {
        if (!auth()->user()?->canDelete() && data_get($this->programItems, $index . '.id')) { $this->flashStatus('Data lama hanya dapat dihapus oleh Admin.'); return; }
        $itemId = data_get($this->programItems, $index . '.id');
        if (!$itemId) {
            return;
        }

        $totalItems = $this->prodiQuery(DashboardProgramItem::class)->where('year', $this->tahunDipilih)->count('id');
        if ($totalItems <= 1) {
            $this->flashStatus('Minimal satu item program/agenda harus tetap tersedia.');
            return;
        }

        $this->prodiQuery(DashboardProgramItem::class)->whereKey($itemId)->delete();

        $this->prodiQuery(DashboardProgramItem::class)
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
        $isAdmin = auth()->user()?->isAdmin();
        if (! $isAdmin && ! collect($this->programItems)->contains(fn($item) => empty($item['id']))) {
            $this->flashStatus('Kaprodi hanya dapat menyimpan item baru.');
            return;
        }

        $hasExecutionStatusColumn = $this->hasExecutionStatusColumn();

        foreach ($this->programItems as $index => $item) {
            $rules = [
                "programItems.$index.id" => ['nullable', 'integer'],
                "programItems.$index.type" => ['required', 'in:Program,Agenda'],
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

            if (!empty($item['id'])) {
                if ($isAdmin) $this->prodiQuery(DashboardProgramItem::class)->whereKey($item['id'])->update($payload);
            } else {
                $this->prodiQuery(DashboardProgramItem::class)->create(['prodi_id' => $this->activeProdiId(), ...$payload]);
            }
        }

        $this->loadProgramItems();
        $this->flashStatus($isAdmin ? 'Program dan agenda berhasil disimpan.' : 'Item baru berhasil disimpan dan kini terkunci.');
    }

    public function naikItem(int $index): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->flashStatus('Mengurutkan agenda hanya dapat dilakukan oleh Admin.');
            return;
        }

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

        $this->prodiQuery(DashboardProgramItem::class)->whereKey($itemId)->update(['sort_order' => $index]);
        $this->prodiQuery(DashboardProgramItem::class)->whereKey($prevItemId)->update(['sort_order' => $index + 1]);

        $this->loadProgramItems();
        $this->flashStatus('Item berhasil dipindahkan ke atas.');
    }

    public function turunItem(int $index): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->flashStatus('Mengurutkan agenda hanya dapat dilakukan oleh Admin.');
            return;
        }

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

        $this->prodiQuery(DashboardProgramItem::class)->whereKey($itemId)->update(['sort_order' => $index + 2]);
        $this->prodiQuery(DashboardProgramItem::class)->whereKey($nextItemId)->update(['sort_order' => $index + 1]);

        $this->loadProgramItems();
        $this->flashStatus('Item berhasil dipindahkan ke bawah.');
    }

    private function loadProgramItems(): void
    {
        $columns = ['id', 'year', 'type', 'title', 'description', 'style_key'];
        if ($this->hasExecutionStatusColumn()) {
            $columns[] = 'execution_status';
        }

        $this->programItems = $this->prodiQuery(DashboardProgramItem::class)
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

    private function ensureProgramYear(int $year): void
    {
        if ($this->prodiQuery(DashboardProgramItem::class)->where('year', $year)->exists()) {
            return;
        }

        foreach (DashboardProgramItem::defaults($year) as $payload) {
            $this->prodiQuery(DashboardProgramItem::class)->create([
                ...$payload,
                'prodi_id' => $this->activeProdiId(),
            ]);
        }
    }

    private function flashStatus(string $message): void
    {
        session()->flash('status', $message);
        $this->dispatch('admin-toast', message: $message);
    }

    public function render()
    {
        return view('livewire.pages.admin-program-agenda-page', [
            'daftarTahun' => $this->prodiQuery(DashboardYearStat::class)->orderByDesc('year')->pluck('year')->all(),
            'styleOptions' => ['blue', 'violet', 'amber', 'rose'],
        ]);
    }
}
