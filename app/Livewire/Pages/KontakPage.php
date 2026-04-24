<?php

namespace App\Livewire\Pages;

use App\Models\ContactFeedback;
use App\Models\HomePageSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Kontak dan Umpan Balik')]
class KontakPage extends Component
{
    public string $feedbackName = '';
    public string $feedbackEmail = '';
    public string $feedbackSubject = '';
    public string $feedbackMessage = '';

    public function kirimUmpanBalik(): void
    {
        $validated = $this->validate([
            'feedbackName' => ['required', 'string', 'max:120'],
            'feedbackEmail' => ['required', 'email', 'max:120'],
            'feedbackSubject' => ['required', 'string', 'max:160'],
            'feedbackMessage' => ['required', 'string', 'max:2000'],
        ]);

        ContactFeedback::query()->create([
            'name' => $validated['feedbackName'],
            'email' => $validated['feedbackEmail'],
            'subject' => $validated['feedbackSubject'],
            'message' => $validated['feedbackMessage'],
        ]);

        $this->reset('feedbackName', 'feedbackEmail', 'feedbackSubject', 'feedbackMessage');
        session()->flash('contact_status', 'Terima kasih. Umpan balik Anda sudah terkirim.');
    }

    public function render()
    {
        return view('livewire.pages.kontak-page', [
            'homeContent' => HomePageSetting::current(),
        ]);
    }
}
