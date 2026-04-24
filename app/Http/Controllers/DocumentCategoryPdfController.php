<?php

namespace App\Http\Controllers;

use App\Models\DocumentItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class DocumentCategoryPdfController extends Controller
{
    public function __invoke(?string $kategori = null): Response
    {
        DocumentItem::ensureDefaults();

        $documents = DocumentItem::query()->orderBy('sort_order')->get();
        $categoryLabel = 'Semua Dokumen';

        if ($kategori) {
            $documents = $documents->where('category_slug', $kategori)->values();
            $categoryLabel = (string) ($documents->first()?->category ?? 'Kategori Dokumen');
        }

        $pdf = Pdf::loadView('pdf.document-category', [
            'documents' => $documents,
            'categoryLabel' => $categoryLabel,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('dokumen-' . ($kategori ?: 'semua') . '.pdf');
    }
}
