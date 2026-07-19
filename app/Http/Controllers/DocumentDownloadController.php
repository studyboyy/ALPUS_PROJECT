<?php

namespace App\Http\Controllers;

use App\Models\DocumentItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentDownloadController extends Controller
{
    public function __invoke(DocumentItem $document): Response|BinaryFileResponse
    {
        $fileUrl = (string) $document->file_url;
        $urlPath = (string) parse_url($fileUrl, PHP_URL_PATH);

        if (str_starts_with($urlPath, '/storage/')) {
            $relativePath = Str::after($urlPath, '/storage/');

            if (Storage::disk('public')->exists($relativePath)) {
                return response()->download(
                    Storage::disk('public')->path($relativePath),
                    $document->file_name ?: basename($relativePath),
                    ['Content-Type' => Storage::disk('public')->mimeType($relativePath) ?: 'application/octet-stream'],
                );
            }
        }

        $fileName = Str::of($document->file_name ?: $document->title)
            ->replaceMatches('/\.[^.]+$/', '')
            ->slug('-')
            ->append('.pdf')
            ->value();

        return Pdf::loadView('pdf.document-category', [
            'documents' => collect([$document]),
            'categoryLabel' => $document->category ?: 'Dokumen Pendukung',
        ])->setPaper('a4', 'portrait')->download($fileName);
    }
}
