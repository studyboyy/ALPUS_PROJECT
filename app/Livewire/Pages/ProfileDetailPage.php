<?php

namespace App\Livewire\Pages;

use App\Models\ProfileSection;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Detail Profil Program Studi')]
class ProfileDetailPage extends Component
{
    public ?string $slug = null;
    public ?array $section = null;

    public function mount(?string $slug = null): void
    {
        if (!$slug) {
            redirect()->route('profil');
        }

        if (!Schema::hasTable('profile_sections')) {
            redirect()->route('profil');
        }

        ProfileSection::ensureDefaults();
        $profileSection = ProfileSection::getBySlug($slug);

        if (!$profileSection) {
            redirect()->route('profil');
        }

        $this->slug = $slug;
        $this->section = [
            'slug' => $profileSection->slug,
            'title' => $profileSection->title,
            'summary' => $profileSection->summary,
            'full_content' => $profileSection->full_content,
            'icon_key' => $profileSection->icon_key,
            'color_class' => $profileSection->color_class,
        ];
    }

    public function render()
    {
        return view('livewire.pages.profile-detail-page');
    }
}
