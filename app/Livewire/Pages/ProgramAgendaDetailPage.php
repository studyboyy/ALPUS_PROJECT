<?php

namespace App\Livewire\Pages;

use App\Models\DashboardProgramItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Detail Program dan Agenda')]
class ProgramAgendaDetailPage extends Component
{
    public ?array $item = null;

    public function mount(int $id, string $slug): void
    {
        if (!Schema::hasTable('dashboard_program_items')) {
            redirect()->route('home');
            return;
        }

        $programItem = DashboardProgramItem::query()->find($id);

        if (!$programItem) {
            redirect()->route('home');
            return;
        }

        $expectedSlug = Str::slug((string) $programItem->title);
        if ($slug !== $expectedSlug) {
            redirect()->route('program-agenda.detail', ['id' => $programItem->id, 'slug' => $expectedSlug]);
            return;
        }

        $statusKey = in_array(($programItem->execution_status ?? ''), ['terlaksana', 'belum_terlaksana'], true)
            ? $programItem->execution_status
            : 'belum_terlaksana';

        $statusMeta = [
            'terlaksana' => ['label' => 'Terlaksana', 'class' => 'bg-emerald-100 text-emerald-700'],
            'belum_terlaksana' => ['label' => 'Belum Terlaksana', 'class' => 'bg-rose-100 text-rose-700'],
        ];

        $this->item = [
            'id' => $programItem->id,
            'year' => (int) $programItem->year,
            'type' => (string) $programItem->type,
            'title' => (string) $programItem->title,
            'description' => (string) $programItem->description,
            'status_label' => $statusMeta[$statusKey]['label'],
            'status_class' => $statusMeta[$statusKey]['class'],
        ];
    }

    public function render()
    {
        return view('livewire.pages.program-agenda-detail-page');
    }
}
