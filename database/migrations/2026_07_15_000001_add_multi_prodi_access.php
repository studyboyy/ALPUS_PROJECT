<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('prodis')) {
            Schema::create('prodis', function (Blueprint $table): void {
                $table->id();
                $table->string('code', 32)->unique();
                $table->string('name', 160);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        DB::table('prodis')->updateOrInsert(['code' => 'ADMIN'], ['name' => 'Administrator', 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]);
        DB::table('prodis')->updateOrInsert(['code' => 'SI'], ['name' => 'Sistem Informasi', 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]);
        DB::table('prodis')->updateOrInsert(['code' => 'IF'], ['name' => 'Informatika', 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]);

        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('role', 32)->default('kaprodi')->after('email');
                $table->foreignId('prodi_id')->nullable()->after('role')->constrained('prodis')->nullOnDelete();
            });
        }

        $adminProdi = DB::table('prodis')->where('code', 'ADMIN')->value('id');
        DB::table('users')->where('email', env('ADMIN_EMAIL', 'admin@prodi.local'))->update(['role' => 'admin', 'prodi_id' => $adminProdi]);

        foreach (['dashboard_year_stats', 'dashboard_program_items', 'home_page_settings', 'contact_feedback', 'document_items', 'profile_sections', 'annual_report_sections', 'dashboard_monthly_stats'] as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'prodi_id')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->foreignId('prodi_id')->nullable()->after('id')->constrained('prodis')->nullOnDelete();
                    $table->index('prodi_id');
                });
            }
        }

        $defaultProdi = DB::table('prodis')->where('code', 'SI')->value('id');
        foreach (['dashboard_year_stats', 'dashboard_program_items', 'home_page_settings', 'contact_feedback', 'document_items', 'profile_sections', 'annual_report_sections', 'dashboard_monthly_stats'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'prodi_id')) {
                DB::table($tableName)->whereNull('prodi_id')->update(['prodi_id' => $defaultProdi]);
            }
        }
    }

    public function down(): void
    {
        foreach (['dashboard_year_stats', 'dashboard_program_items', 'home_page_settings', 'contact_feedback', 'document_items', 'profile_sections', 'annual_report_sections', 'dashboard_monthly_stats'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'prodi_id')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropForeign(['prodi_id']);
                    $table->dropColumn('prodi_id');
                });
            }
        }
        if (Schema::hasColumn('users', 'prodi_id')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropForeign(['prodi_id']);
                $table->dropColumn(['role', 'prodi_id']);
            });
        }
        Schema::dropIfExists('prodis');
    }
};
