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
                'title' => 'Dokumen Profil DTPS 2025',
                'description' => 'Ringkasan kualifikasi dosen tetap dan beban kerja tahun berjalan.',
                'category' => 'Dosen & Penelitian',
                'category_slug' => 'dosen-penelitian',
                'file_url' => '#',
                'file_name' => 'Profil-DTPS-2025.pdf',
                'sort_order' => 1,
            ],
            [
                'title' => 'Laporan Penelitian & PkM 2025',
                'description' => 'Daftar judul riset, pengabdian masyarakat, dan sumber pendanaan.',
                'category' => 'Dosen & Penelitian',
                'category_slug' => 'dosen-penelitian',
                'file_url' => '#',
                'file_name' => 'Penelitian-PkM-2025.pdf',
                'sort_order' => 2,
            ],
            [
                'title' => 'Kumpulan Publikasi Dosen 2024-2025',
                'description' => 'Rekap jurnal, prosiding, dan buku ajar yang diterbitkan dosen.',
                'category' => 'Dosen & Penelitian',
                'category_slug' => 'dosen-penelitian',
                'file_url' => '#',
                'file_name' => 'Publikasi-Dosen-2024-2025.pdf',
                'sort_order' => 3,
            ],
            [
                'title' => 'Sertifikat Juara 1 Lomba Inovasi',
                'description' => 'Dokumentasi prestasi mahasiswa pada kompetisi tingkat nasional.',
                'category' => 'Prestasi Mahasiswa',
                'category_slug' => 'prestasi-mahasiswa',
                'file_url' => '#',
                'file_name' => 'Sertifikat-Juara-1.pdf',
                'sort_order' => 4,
            ],
            [
                'title' => 'Finalis PKM 2025',
                'description' => 'Dokumentasi presentasi dan ringkasan riset mahasiswa.',
                'category' => 'Prestasi Mahasiswa',
                'category_slug' => 'prestasi-mahasiswa',
                'file_url' => '#',
                'file_name' => 'Finalis-PKM-2025.pdf',
                'sort_order' => 5,
            ],
            [
                'title' => 'Program Pengabdian Desa',
                'description' => 'Laporan kegiatan dan dampak sosial yang digagas mahasiswa.',
                'category' => 'Prestasi Mahasiswa',
                'category_slug' => 'prestasi-mahasiswa',
                'file_url' => '#',
                'file_name' => 'Pengabdian-Desa.pdf',
                'sort_order' => 6,
            ],
            [
                'title' => 'Rencana Strategis (Renstra) 2025-2029',
                'description' => 'Dokumen arah pengembangan program studi jangka menengah-panjang.',
                'category' => 'Rencana Strategis (Renstra)',
                'category_slug' => 'rencana-strategis-renstra',
                'file_url' => '#',
                'file_name' => 'Renstra-2025-2029.pdf',
                'sort_order' => 7,
            ],
            [
                'title' => 'Rencana Operasional (Renop) 2025',
                'description' => 'Turunan tahunan dari Renstra beserta target unit kerja.',
                'category' => 'Rencana Operasional (Renop)',
                'category_slug' => 'rencana-operasional-renop',
                'file_url' => '#',
                'file_name' => 'Renop-2025.pdf',
                'sort_order' => 8,
            ],
            [
                'title' => 'Standar Mutu Program Studi',
                'description' => 'Acuan pelaksanaan sistem penjaminan mutu internal prodi.',
                'category' => 'Standar Mutu Prodi',
                'category_slug' => 'standar-mutu-prodi',
                'file_url' => '#',
                'file_name' => 'Standar-Mutu.pdf',
                'sort_order' => 9,
            ],
            [
                'title' => 'Notulen Rapat Evaluasi Tahunan',
                'description' => 'Dokumentasi rapat evaluasi tahunan untuk tindak lanjut peningkatan mutu.',
                'category' => 'Notulen Rapat Evaluasi',
                'category_slug' => 'notulen-rapat-evaluasi',
                'file_url' => '#',
                'file_name' => 'Notulen-Evaluasi.pdf',
                'sort_order' => 10,
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
            str_contains($haystack, 'prestasi'),
            str_contains($haystack, 'juara'),
            str_contains($haystack, 'finalis'),
            str_contains($haystack, 'kompetisi') => 'Prestasi Mahasiswa',
            str_contains($haystack, 'dtps'),
            str_contains($haystack, 'dosen'),
            str_contains($haystack, 'penelitian'),
            str_contains($haystack, 'publikasi'),
            str_contains($haystack, 'pkm') => 'Dosen & Penelitian',
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
