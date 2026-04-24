<?php

namespace App\Livewire\Pages;

use App\Models\DocumentItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Admin Dokumen')]
class AdminDocumentPage extends Component
{
    use WithFileUploads;

    public string $kategoriDipilih = 'Semua';
    public array $documents = [];
    public array $documentFiles = [];

    public function mount(): void
    {
        DocumentItem::ensureDefaults();
        $this->loadDocuments();
    }

    public function tambahDokumen(): void
    {
        $this->documents[] = [
            'id' => null,
            'title' => 'Dokumen Baru',
            'description' => '',
            'category' => 'Dokumen Pendukung',
            'category_slug' => DocumentItem::slugFromCategory('Dokumen Pendukung'),
            'file_url' => '',
            'file_name' => '',
        ];
    }

    public function hapusDokumen(int $index): void
    {
        $documentId = data_get($this->documents, $index . '.id');
        if ($documentId) {
            DocumentItem::query()->whereKey($documentId)->delete();
        }

        unset($this->documents[$index], $this->documentFiles[$index]);
        $this->documents = array_values($this->documents);
        $this->documentFiles = array_values($this->documentFiles);

        $this->flashStatus('Dokumen berhasil dihapus.');
    }

    public function pilihKategori(string $kategori): void
    {
        $this->kategoriDipilih = $kategori;
    }

    public function simpanDokumen(): void
    {
        $this->validate([
            'documents' => ['required', 'array', 'min:1'],
            'documents.*.title' => ['required', 'string', 'max:180'],
            'documents.*.description' => ['nullable', 'string', 'max:1000'],
            'documents.*.category' => ['required', 'string', 'max:120'],
            'documentFiles.*' => ['nullable', 'file', 'max:8192'],
        ]);

        foreach ($this->documents as $index => $document) {
            $fileUrl = (string) data_get($document, 'file_url', '');
            $fileName = (string) data_get($document, 'file_name', '');
            $uploadedFile = $this->documentFiles[$index] ?? null;

            if ($uploadedFile) {
                $path = $uploadedFile->store('documents', 'public');
                $fileUrl = asset('storage/' . $path);
                $fileName = $uploadedFile->getClientOriginalName();
            }

            if ($fileUrl === '') {
                $this->addError('documentFiles.' . $index, 'File dokumen wajib diupload.');
                return;
            }

            $saved = DocumentItem::query()->updateOrCreate(
                ['id' => data_get($document, 'id')],
                [
                    'title' => (string) data_get($document, 'title', ''),
                    'description' => (string) data_get($document, 'description', ''),
                    'category' => (string) data_get($document, 'category', 'Dokumen Pendukung'),
                    'category_slug' => DocumentItem::slugFromCategory((string) data_get($document, 'category', 'Dokumen Pendukung')),
                    'file_url' => $fileUrl,
                    'file_name' => $fileName,
                    'sort_order' => $index + 1,
                ]
            );

            $this->documents[$index]['id'] = $saved->id;
            $this->documents[$index]['file_url'] = $fileUrl;
            $this->documents[$index]['file_name'] = $fileName;
        }

        $this->documentFiles = [];
        $this->loadDocuments();
        $this->flashStatus('Dokumen berhasil dipublikasikan.');
    }

    private function loadDocuments(): void
    {
        $this->documents = DocumentItem::query()
            ->orderBy('sort_order')
            ->get(['id', 'title', 'description', 'category', 'category_slug', 'file_url', 'file_name'])
            ->toArray();
    }

    private function flashStatus(string $message): void
    {
        session()->flash('status', $message);
        $this->dispatch('admin-toast', message: $message);
    }

    public function render()
    {
        return view('livewire.pages.admin-document-page');
    }
}
