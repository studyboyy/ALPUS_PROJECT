<?php

namespace App\Livewire\Pages;

use App\Models\ContactFeedback;
use App\Livewire\Concerns\UsesActiveProdi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Umpan Balik')]
class AdminFeedbackPage extends Component
{
    use UsesActiveProdi;
    public function tandaiDibaca(int $id): void
    {
        if (! auth()->user()?->isAdmin()) {
            $this->dispatch('admin-toast', message: 'Menandai umpan balik hanya dapat dilakukan oleh Admin.');
            return;
        }

        $this->prodiQuery(ContactFeedback::class)->whereKey($id)->whereNull('read_at')->update([
            'read_at' => now(),
        ]);
    }

    public function hapusFeedback(int $id): void
    {
        if (!auth()->user()?->canDelete()) { return; }
        $this->prodiQuery(ContactFeedback::class)->whereKey($id)->delete();
    }

    public function render()
    {
        return view('livewire.pages.admin-feedback-page', [
            'feedbackItems' => $this->prodiQuery(ContactFeedback::class)->latest()->get(),
            'belumDibaca' => (int) $this->prodiQuery(ContactFeedback::class)->whereNull('read_at')->count(),
        ]);
    }
}
