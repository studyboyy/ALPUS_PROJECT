<?php

namespace App\Livewire\Pages;

use App\Models\HomePageSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Galeri Kegiatan')]
class GaleriPage extends Component
{
    public string $kategoriDipilih = 'Semua';

    public function mount(?string $kategori = null): void
    {
        if (!$kategori) {
            return;
        }

        $matched = collect(HomePageSetting::current()['gallery_items'])
            ->map(fn($item) => ['category' => data_get($item, 'category'), 'category_slug' => data_get($item, 'category_slug')])
            ->filter()
            ->unique()
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
        $galleryItems = collect(HomePageSetting::current()['gallery_items'])
            ->reverse()
            ->values();

        $kategoriList = $galleryItems
            ->pluck('category')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($this->kategoriDipilih !== 'Semua') {
            $galleryItems = $galleryItems
                ->where('category', $this->kategoriDipilih)
                ->values();
        }

        return view('livewire.pages.galeri-page', [
            'galleryItems' => $galleryItems->all(),
            'kategoriList' => $kategoriList,
            'kategoriSlugMap' => collect(HomePageSetting::current()['gallery_items'])->pluck('category_slug', 'category')->all(),
        ]);
    }
}
