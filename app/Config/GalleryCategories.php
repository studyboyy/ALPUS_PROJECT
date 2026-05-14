<?php

namespace App\Config;

/**
 * Gallery Categories Configuration
 * Centralized source of truth for all gallery category options
 */
class GalleryCategories
{
    /**
     * Core gallery categories with their display names
     * These are the predefined categories available in the system
     */
    public const CATEGORIES = [
        'Prestasi Mahasiswa',
        'Kegiatan Akademik',
        'Kegiatan Mahasiswa',
        'Pengabdian Masyarakat',
        'Kerjasama & MoU',
    ];

    /**
     * Get category slug from category name
     *
     * @param string $category
     * @return string
     */
    public static function slugFromCategory(string $category): string
    {
        return strtolower(
            preg_replace(
                '/[^a-z0-9]+/',
                '-',
                preg_replace('/&/', 'dan', $category)
            )
        );
    }

    /**
     * Get all categories for use in dropdowns
     *
     * @return array
     */
    public static function all(): array
    {
        return self::CATEGORIES;
    }

    /**
     * Get all categories with their slugs
     *
     * @return array
     */
    public static function withSlugs(): array
    {
        return array_reduce(
            self::CATEGORIES,
            function ($carry, $category) {
                $carry[$category] = self::slugFromCategory($category);
                return $carry;
            },
            []
        );
    }

    /**
     * Check if category exists
     *
     * @param string $category
     * @return bool
     */
    public static function exists(string $category): bool
    {
        return in_array($category, self::CATEGORIES, true);
    }
}
