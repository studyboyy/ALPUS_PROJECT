<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dashboard_program_items', function (Blueprint $table): void {
            $table->unsignedInteger('year')->nullable()->after('id');
        });

        DB::table('dashboard_program_items')->update([
            'year' => 2025,
        ]);

        Schema::table('dashboard_program_items', function (Blueprint $table): void {
            $table->unsignedInteger('year')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('dashboard_program_items', function (Blueprint $table): void {
            $table->dropColumn('year');
        });
    }
};
