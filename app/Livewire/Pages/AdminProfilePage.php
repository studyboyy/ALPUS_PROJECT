<?php

namespace App\Livewire\Pages;

use App\Models\ProfileSection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Profil Program Studi')]
class AdminProfilePage extends Component
{
    public array $sections = [];
    public ?string $editingSlug = null;
    public string $editTitle = '';
    public string $editSummary = '';
    public string $editContent = '';
    public string $editColorClass = 'blue';
    public string $editIconKey = 'book';

    public function mount(): void
    {
        ProfileSection::ensureDefaults();
        $this->loadSections();
    }

    public function loadSections(): void
    {
        $this->sections = ProfileSection::allOrdered()
            ->map(fn($s) => [
                'slug' => $s->slug,
                'title' => $s->title,
                'summary' => $s->summary,
                'full_content' => $s->full_content,
                'icon_key' => $s->icon_key,
                'color_class' => $s->color_class,
            ])
            ->toArray();
    }

    public function editSection(string $slug): void
    {
        $section = ProfileSection::getBySlug($slug);
        if (!$section) {
            return;
        }

        $this->editingSlug = $slug;
        $this->editTitle = $section->title;
        $this->editSummary = $section->summary;
        $this->editContent = $section->full_content ?? '';
        $this->editColorClass = $section->color_class;
        $this->editIconKey = $section->icon_key;
    }

    public function cancelEdit(): void
    {
        $this->editingSlug = null;
        $this->resetEdit();
    }

    public function simpanSection(): void
    {
        $this->validate([
            'editTitle' => ['required', 'string', 'max:120'],
            'editSummary' => ['required', 'string', 'max:255'],
            'editContent' => ['required', 'string', 'max:5000'],
            'editColorClass' => ['required', 'in:blue,violet,emerald,amber'],
            'editIconKey' => ['required', 'in:book,organization,people,award'],
        ]);

        if (!$this->editingSlug) {
            return;
        }

        $section = ProfileSection::getBySlug($this->editingSlug);
        if (!$section) {
            return;
        }

        $section->fill([
            'title' => $this->editTitle,
            'summary' => $this->editSummary,
            'full_content' => $this->editContent,
            'color_class' => $this->editColorClass,
            'icon_key' => $this->editIconKey,
        ]);
        $section->save();

        $this->cancelEdit();
        $this->loadSections();
        $this->flashStatus('Profil section berhasil disimpan.');
    }

    private function resetEdit(): void
    {
        $this->editTitle = '';
        $this->editSummary = '';
        $this->editContent = '';
        $this->editColorClass = 'blue';
        $this->editIconKey = 'book';
    }

    private function flashStatus(string $message): void
    {
        session()->flash('status', $message);
        $this->dispatch('admin-toast', message: $message);
    }

    public function render()
    {
        return view('livewire.pages.admin-profile-page');
    }
}
