<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class DocumentItem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'category',
        'category_slug',
        'file_url',
        'file_name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public static function defaults(): array
    {
        return [
            [
                'title' => 'Rencana Strategis (Renstra) 2025-2029',
                'description' => 'Dokumen arah pengembangan program studi jangka menengah-panjang.',
                'category' => 'Rencana Strategis (Renstra)',
                'category_slug' => 'rencana-strategis-renstra',
                'file_url' => '#',
                'file_name' => 'Renstra-2025-2029.pdf',
                'sort_order' => 1,
            ],
            [
                'title' => 'Rencana Operasional (Renop) 2025',
                'description' => 'Turunan tahunan dari Renstra beserta target unit kerja.',
                'category' => 'Rencana Operasional (Renop)',
                'category_slug' => 'rencana-operasional-renop',
                'file_url' => '#',
                'file_name' => 'Renop-2025.pdf',
                'sort_order' => 2,
            ],
            [
                'title' => 'Standar Mutu Program Studi',
                'description' => 'Acuan pelaksanaan sistem penjaminan mutu internal prodi.',
                'category' => 'Standar Mutu Prodi',
                'category_slug' => 'standar-mutu-prodi',
                'file_url' => '#',
                'file_name' => 'Standar-Mutu.pdf',
                'sort_order' => 3,
            ],
            [
                'title' => 'Notulen Rapat Evaluasi Tahunan',
                'description' => 'Dokumentasi rapat evaluasi tahunan untuk tindak lanjut peningkatan mutu.',
                'category' => 'Notulen Rapat Evaluasi',
                'category_slug' => 'notulen-rapat-evaluasi',
                'file_url' => '#',
                'file_name' => 'Notulen-Evaluasi.pdf',
                'sort_order' => 4,
            ],
        ];
    }

    public static function ensureDefaults(): void
    {
        if (!Schema::hasTable('document_items')) {
            return;
        }

        static::query()
            ->where(function ($query): void {
                $query->whereNull('category')->orWhere('category', '');
            })
            ->get()
            ->each(function (self $document): void {
                $document->category = static::inferCategory($document->title, $document->description);
                $document->category_slug = static::slugFromCategory($document->category);
                $document->save();
            });

        static::query()
            ->where(function ($query): void {
                $query->whereNull('category_slug')->orWhere('category_slug', '');
            })
            ->get()
            ->each(function (self $document): void {
                $document->category_slug = static::slugFromCategory($document->category ?: static::inferCategory($document->title, $document->description));
                $document->save();
            });

        foreach (static::defaults() as $payload) {
            static::query()->firstOrCreate(
                ['title' => $payload['title']],
                $payload,
            );
        }
    }

    public static function inferCategory(?string $title, ?string $description = null): string
    {
        $haystack = mb_strtolower(trim(($title ?? '') . ' ' . ($description ?? '')));

        return match (true) {
            str_contains($haystack, 'renstra') => 'Rencana Strategis (Renstra)',
            str_contains($haystack, 'renop') => 'Rencana Operasional (Renop)',
            str_contains($haystack, 'mutu') => 'Standar Mutu Prodi',
            str_contains($haystack, 'notulen'), str_contains($haystack, 'rapat evaluasi') => 'Notulen Rapat Evaluasi',
            default => 'Dokumen Pendukung',
        };
    }

    public static function slugFromCategory(?string $category): string
    {
        return Str::slug((string) $category) ?: 'dokumen-pendukung';
    }
}
