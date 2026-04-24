<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ProfileSection extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'summary',
        'full_content',
        'icon_key',
        'color_class',
        'sort_order',
    ];

    public static function defaults(): array
    {
        return [
            [
                'slug' => 'sejarah-visi-misi',
                'title' => 'Sejarah & Visi Misi',
                'summary' => 'Perjalanan dan misi strategis prodi.',
                'full_content' => 'Konten lengkap tentang sejarah, visi, dan misi Program Studi dapat ditambahkan di sini.',
                'icon_key' => 'book',
                'color_class' => 'blue',
                'sort_order' => 1,
            ],
            [
                'slug' => 'struktur-organisasi',
                'title' => 'Struktur Organisasi',
                'summary' => 'Organisasi dan kepemimpinan prodi.',
                'full_content' => 'Konten lengkap tentang struktur organisasi dapat ditampilkan di sini.',
                'icon_key' => 'organization',
                'color_class' => 'violet',
                'sort_order' => 2,
            ],
            [
                'slug' => 'sdm-program-studi',
                'title' => 'SDM Program Studi',
                'summary' => 'Dosen dan tenaga pendidik kami.',
                'full_content' => 'Konten lengkap tentang SDM dapat ditampilkan di sini.',
                'icon_key' => 'people',
                'color_class' => 'emerald',
                'sort_order' => 3,
            ],
            [
                'slug' => 'pencapaian',
                'title' => 'Pencapaian & Prestasi',
                'summary' => 'Prestasi dan penghargaan prodi.',
                'full_content' => 'Konten lengkap tentang pencapaian dapat ditampilkan di sini.',
                'icon_key' => 'award',
                'color_class' => 'amber',
                'sort_order' => 4,
            ],
        ];
    }

    public static function ensureDefaults(): void
    {
        if (!Schema::hasTable('profile_sections')) {
            return;
        }

        foreach (static::defaults() as $section) {
            static::query()->firstOrCreate(
                ['slug' => $section['slug']],
                $section
            );
        }
    }

    public static function getBySlug(string $slug): ?self
    {
        return static::query()
            ->where('slug', $slug)
            ->first();
    }

    public static function allOrdered(): \Illuminate\Database\Eloquent\Collection
    {
        return static::query()
            ->orderBy('sort_order')
            ->get();
    }

    public function getColorMap(): array
    {
        $map = [
            'blue' => ['bg' => 'bg-linear-to-br from-blue-500 to-indigo-600', 'border' => 'border-blue-100', 'bg-light' => 'bg-blue-50', 'text' => 'text-blue-700', 'shadow' => 'shadow-blue-200', 'top-border' => 'border-blue-500'],
            'violet' => ['bg' => 'bg-linear-to-br from-violet-500 to-purple-600', 'border' => 'border-violet-100', 'bg-light' => 'bg-violet-50', 'text' => 'text-violet-700', 'shadow' => 'shadow-violet-200', 'top-border' => 'border-violet-500'],
            'emerald' => ['bg' => 'bg-linear-to-br from-emerald-500 to-teal-600', 'border' => 'border-emerald-100', 'bg-light' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'shadow' => 'shadow-emerald-200', 'top-border' => 'border-emerald-500'],
            'amber' => ['bg' => 'bg-linear-to-br from-amber-500 to-orange-600', 'border' => 'border-amber-100', 'bg-light' => 'bg-amber-50', 'text' => 'text-amber-700', 'shadow' => 'shadow-amber-200', 'top-border' => 'border-amber-500'],
        ];

        return $map[$this->color_class] ?? $map['blue'];
    }

    public function getIcon(): string
    {
        $icons = [
            'book' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z" />',
            'organization' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
            'people' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
            'award' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />',
        ];

        return $icons[$this->icon_key] ?? $icons['book'];
    }

    public static function getIconPath(string $iconKey = 'book'): string
    {
        $icons = [
            'book' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z" />',
            'organization' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
            'people' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />',
            'award' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />',
        ];

        return $icons[$iconKey] ?? $icons['book'];
    }

    public static function getColorClasses(string $colorClass = 'blue'): array
    {
        $map = [
            'blue' => ['bg' => 'bg-linear-to-br from-blue-500 to-indigo-600', 'border' => 'border-blue-100', 'bg-light' => 'bg-blue-50', 'text' => 'text-blue-700', 'shadow' => 'shadow-blue-200', 'top-border' => 'border-blue-500'],
            'violet' => ['bg' => 'bg-linear-to-br from-violet-500 to-purple-600', 'border' => 'border-violet-100', 'bg-light' => 'bg-violet-50', 'text' => 'text-violet-700', 'shadow' => 'shadow-violet-200', 'top-border' => 'border-violet-500'],
            'emerald' => ['bg' => 'bg-linear-to-br from-emerald-500 to-teal-600', 'border' => 'border-emerald-100', 'bg-light' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'shadow' => 'shadow-emerald-200', 'top-border' => 'border-emerald-500'],
            'amber' => ['bg' => 'bg-linear-to-br from-amber-500 to-orange-600', 'border' => 'border-amber-100', 'bg-light' => 'bg-amber-50', 'text' => 'text-amber-700', 'shadow' => 'shadow-amber-200', 'top-border' => 'border-amber-500'],
        ];

        return $map[$colorClass] ?? $map['blue'];
    }
}
