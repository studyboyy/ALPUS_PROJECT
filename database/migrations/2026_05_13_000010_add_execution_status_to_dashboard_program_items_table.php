<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dashboard_program_items', function (Blueprint $table): void {
            $table->string('execution_status', 32)->default('belum_terlaksana')->after('style_key');
        });
    }

    public function down(): void
    {
        Schema::table('dashboard_program_items', function (Blueprint $table): void {
            $table->dropColumn('execution_status');
        });
    }
};
