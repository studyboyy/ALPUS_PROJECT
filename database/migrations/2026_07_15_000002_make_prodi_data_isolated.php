<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $drop = [
            ['dashboard_year_stats', 'dashboard_year_stats_year_unique'],
            ['dashboard_monthly_stats', 'dashboard_monthly_stats_year_month_unique'],
            ['annual_report_sections', 'annual_report_sections_year_section_key_unique'],
            ['profile_sections', 'profile_sections_slug_unique'],
        ];
        foreach ($drop as [$table, $index]) {
            if (Schema::hasTable($table)) {
                try {
                    Schema::table($table, function (Blueprint $blueprint) use ($index): void {
                        $blueprint->dropUnique($index);
                    });
                } catch (Throwable) {
                }
            }
        }
        if (Schema::hasTable('dashboard_year_stats')) {
            Schema::table('dashboard_year_stats', fn (Blueprint $t) => $t->unique(['prodi_id', 'year']));
        }
        if (Schema::hasTable('dashboard_monthly_stats')) {
            Schema::table('dashboard_monthly_stats', fn (Blueprint $t) => $t->unique(['prodi_id', 'year', 'month']));
        }
        if (Schema::hasTable('annual_report_sections')) {
            Schema::table('annual_report_sections', fn (Blueprint $t) => $t->unique(['prodi_id', 'year', 'section_key']));
        }
        if (Schema::hasTable('profile_sections')) {
            Schema::table('profile_sections', fn (Blueprint $t) => $t->unique(['prodi_id', 'slug']));
        }

        $source = DB::table('prodis')->where('code', 'SI')->value('id');
        $targets = DB::table('prodis')->where('code', '!=', 'ADMIN')->where('id', '!=', $source)->pluck('id');
        foreach ($targets as $target) {
            foreach (['dashboard_year_stats', 'dashboard_program_items', 'home_page_settings', 'document_items', 'profile_sections', 'annual_report_sections', 'dashboard_monthly_stats'] as $table) {
                if (! Schema::hasTable($table)) {
                    continue;
                }
                $rows = DB::table($table)->where('prodi_id', $source)->get();
                foreach ($rows as $row) {
                    $payload = (array) $row;
                    unset($payload['id']);
                    $payload['prodi_id'] = $target;
                    $exists = DB::table($table)->where('prodi_id', $target);
                    if (isset($payload['year'])) {
                        $exists->where('year', $payload['year']);
                    }
                    if (isset($payload['month'])) {
                        $exists->where('month', $payload['month']);
                    }
                    if (isset($payload['slug'])) {
                        $exists->where('slug', $payload['slug']);
                    }
                    if (isset($payload['section_key'])) {
                        $exists->where('section_key', $payload['section_key']);
                    }
                    if (! $exists->exists()) {
                        DB::table($table)->insert($payload);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // Data remains valid; isolation indexes are intentionally retained on rollback.
    }
};
