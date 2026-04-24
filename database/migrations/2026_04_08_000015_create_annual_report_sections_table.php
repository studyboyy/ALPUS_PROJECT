<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annual_report_sections', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('year');
            $table->string('section_key', 100);
            $table->string('title', 180);
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['year', 'section_key']);
            $table->index(['year', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_report_sections');
    }
};
