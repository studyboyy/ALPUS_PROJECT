<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table): void {
            $table->string('contact_email')->nullable()->after('header_title_text');
            $table->text('contact_address')->nullable()->after('contact_email');
            $table->string('contact_socials')->nullable()->after('contact_address');
            $table->text('contact_map_embed_url')->nullable()->after('contact_socials');
        });
    }

    public function down(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'contact_email',
                'contact_address',
                'contact_socials',
                'contact_map_embed_url',
            ]);
        });
    }
};
