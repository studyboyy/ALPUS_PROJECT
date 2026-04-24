<?php

namespace App\Http\Controllers;

use App\Models\HomePageSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class GalleryCategoryPdfController extends Controller
{
    public function __invoke(?string $kategori = null): Response
    {
        $galleryItems = collect(HomePageSetting::current()['gallery_items']);
        $categoryLabel = 'Semua Kategori Galeri';

        if ($kategori) {
            $galleryItems = $galleryItems->where('category_slug', $kategori)->values();
            $categoryLabel = (string) data_get($galleryItems->first(), 'category', 'Kategori Galeri');
        }

        $pdf = Pdf::loadView('pdf.gallery-category', [
            'galleryItems' => $galleryItems,
            'categoryLabel' => $categoryLabel,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('galeri-' . ($kategori ?: 'semua') . '.pdf');
    }
}
