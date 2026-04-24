<?php

namespace App\Livewire\Pages;

use App\Models\DocumentItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Dokumen Pendukung')]
class DokumenPage extends Component
{
    public string $kategoriDipilih = 'Semua';

    public function mount(?string $kategori = null): void
    {
        if (!$kategori) {
            return;
        }

        DocumentItem::ensureDefaults();
        $matched = DocumentItem::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn($item) => ['category' => $item->category, 'category_slug' => $item->category_slug])
            ->filter()
            ->first(fn($item) => data_get($item, 'category_slug') === $kategori);

        if ($matched) {
            $this->kategoriDipilih = (string) data_get($matched, 'category');
        }
    }

    public function pilihKategori(string $kategori): void
    {
        $this->kategoriDipilih = $kategori;
    }

    public function render()
    {
        DocumentItem::ensureDefaults();

        $documents = DocumentItem::query()->orderBy('sort_order')->get();
        $kategoriList = $documents
            ->pluck('category')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($this->kategoriDipilih !== 'Semua') {
            $documents = $documents->where('category', $this->kategoriDipilih)->values();
        }

        return view('livewire.pages.dokumen-page', [
            'documents' => $documents,
            'kategoriList' => $kategoriList,
            'kategoriSlugMap' => DocumentItem::query()->orderBy('sort_order')->get()->pluck('category_slug', 'category')->all(),
        ]);
    }
}
