<?php

namespace App\Livewire\Pages;

use App\Models\ContactFeedback;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Umpan Balik')]
class AdminFeedbackPage extends Component
{
    public function tandaiDibaca(int $id): void
    {
        ContactFeedback::query()->whereKey($id)->whereNull('read_at')->update([
            'read_at' => now(),
        ]);
    }

    public function render()
    {
        return view('livewire.pages.admin-feedback-page', [
            'feedbackItems' => ContactFeedback::query()->latest()->get(),
            'belumDibaca' => ContactFeedback::query()->whereNull('read_at')->count(),
        ]);
    }
}
