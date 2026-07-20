<?php

namespace App\Livewire\Pages;

use App\Models\DocumentItem;
use App\Models\Prodi;
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
        $category = $this->kategoriDipilih !== 'Semua'
            ? $this->kategoriDipilih
            : 'Dokumen Pendukung';

        $this->documents[] = [
            'id' => null,
            'title' => 'Dokumen Baru',
            'description' => '',
            'category' => $category,
            'category_slug' => DocumentItem::slugFromCategory($category),
            'file_url' => '',
            'file_name' => '',
        ];

        $this->resetValidation();
    }

    public function hapusDokumen(int $index): void
    {
        if (!auth()->user()?->canDelete()) { $this->flashStatus('Akses hapus hanya untuk Admin.'); return; }
        $documentId = data_get($this->documents, $index . '.id');
        if ($documentId) {
            $this->documentQuery()->whereKey($documentId)->delete();
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
        $this->resetValidation();

        $this->validate([
            'documents' => ['required', 'array', 'min:1'],
            'documents.*.title' => ['required', 'string', 'max:180'],
            'documents.*.description' => ['nullable', 'string', 'max:1000'],
            'documents.*.category' => ['required', 'string', 'max:120'],
            'documentFiles.*' => ['nullable', 'file', 'max:8192'],
        ]);

        foreach ($this->documents as $index => $document) {
            // Record lama read-only bagi kaprodi/sekprodi, termasuk saat
            // payload Livewire dimanipulasi dari browser.
            if (! auth()->user()?->isAdmin() && data_get($document, 'id')) {
                continue;
            }

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

            $saved = $this->documentQuery()->updateOrCreate(
                ['id' => data_get($document, 'id')],
                [
                    'prodi_id' => $this->activeProdiId(),
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
        $this->documents = $this->documentQuery()
            ->orderBy('sort_order')
            ->get(['id', 'title', 'description', 'category', 'category_slug', 'file_url', 'file_name'])
            ->toArray();
    }

    private function activeProdiId(): ?int
    {
        $user = auth()->user();
        if ($user?->isAdmin()) {
            return (int) (session('admin_prodi_id')
                ?: Prodi::query()->where('code', '!=', 'ADMIN')->where('is_active', true)->orderBy('name')->value('id'));
        }

        return $user?->prodi_id ? (int) $user->prodi_id : null;
    }

    private function documentQuery()
    {
        $query = DocumentItem::query();
        $prodiId = $this->activeProdiId();

        if ($prodiId) {
            $query->withoutGlobalScope('prodi')->where('document_items.prodi_id', $prodiId);
        }

        return $query;
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
