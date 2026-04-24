<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_page_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('hero_background_url')->nullable();
            $table->string('kaprodi_name')->nullable();
            $table->string('kaprodi_title')->nullable();
            $table->text('kaprodi_quote')->nullable();
            $table->string('kaprodi_photo_url')->nullable();
            $table->json('gallery_items')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_page_settings');
    }
};
