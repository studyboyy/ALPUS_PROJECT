<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table): void {
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->string('contact_whatsapp')->nullable()->after('contact_phone');
            $table->json('contact_social_links')->nullable()->after('contact_socials');
        });
    }

    public function down(): void
    {
        Schema::table('home_page_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'contact_phone',
                'contact_whatsapp',
                'contact_social_links',
            ]);
        });
    }
};
