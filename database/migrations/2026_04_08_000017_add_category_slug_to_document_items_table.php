<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_items', function (Blueprint $table): void {
            $table->string('category_slug', 160)->nullable()->after('category');
            $table->index('category_slug');
        });
    }

    public function down(): void
    {
        Schema::table('document_items', function (Blueprint $table): void {
            $table->dropIndex(['category_slug']);
            $table->dropColumn('category_slug');
        });
    }
};
