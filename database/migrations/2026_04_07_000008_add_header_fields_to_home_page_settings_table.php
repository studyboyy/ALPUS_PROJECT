<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table): void {
            $table->string('header_logo_url')->nullable()->after('hero_items');
            $table->string('header_logo_label')->nullable()->after('header_logo_url');
            $table->string('header_title_text')->nullable()->after('header_logo_label');
        });
    }

    public function down(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table): void {
            $table->dropColumn(['header_logo_url', 'header_logo_label', 'header_title_text']);
        });
    }
};
