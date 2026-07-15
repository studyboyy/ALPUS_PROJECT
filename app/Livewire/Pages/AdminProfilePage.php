<?php

namespace App\Livewire\Pages;

use App\Models\ProfileSection;
use App\Models\Prodi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Profil Program Studi')]
class AdminProfilePage extends Component
{
    public array $sections = [];
    public ?int $selectedProdiId = null;
    public ?string $editingSlug = null;
    public string $editTitle = '';
    public string $editSummary = '';
    public string $editContent = '';
    public string $editColorClass = 'blue';
    public string $editIconKey = 'book';

    public function mount(): void
    {
        $user = auth()->user();
        $this->selectedProdiId = $user?->isAdmin()
            ? Prodi::query()->where('code', '!=', 'ADMIN')->where('is_active', true)->orderBy('name')->value('id')
            : $user?->prodi_id;

        $this->ensureDefaultsForSelectedProdi();
        $this->loadSections();
    }

    public function pilihProdi(int $prodiId): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        if (! Prodi::query()->whereKey($prodiId)->where('code', '!=', 'ADMIN')->where('is_active', true)->exists()) {
            return;
        }

        $this->selectedProdiId = $prodiId;
        $this->cancelEdit();
        $this->ensureDefaultsForSelectedProdi();
        $this->loadSections();
    }

    public function loadSections(): void
    {
        $this->sections = $this->profileQuery()
            ->orderBy('sort_order')
            ->get()
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
        $section = $this->profileQuery()->where('slug', $slug)->first();
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

        $section = $this->profileQuery()->where('slug', $this->editingSlug)->first();
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

    private function profileQuery()
    {
        $query = ProfileSection::query();

        // Admin dapat berpindah tab lintas prodi; global scope publik harus dilewati.
        if (auth()->user()?->isAdmin()) {
            $query->withoutGlobalScope('prodi');
        }

        return $query->where('prodi_id', $this->selectedProdiId);
    }

    private function ensureDefaultsForSelectedProdi(): void
    {
        if (! $this->selectedProdiId) {
            return;
        }

        foreach (ProfileSection::defaults() as $section) {
            $this->profileQuery()->firstOrCreate(
                ['slug' => $section['slug']],
                [...$section, 'prodi_id' => $this->selectedProdiId]
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
        return view('livewire.pages.admin-profile-page', [
            'prodis' => Prodi::query()->where('code', '!=', 'ADMIN')->where('is_active', true)->orderBy('name')->get(),
            'selectedProdi' => $this->selectedProdiId ? Prodi::find($this->selectedProdiId) : null,
        ]);
    }
}
