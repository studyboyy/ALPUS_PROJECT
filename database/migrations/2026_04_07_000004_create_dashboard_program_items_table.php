<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_program_items', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 32);
            $table->string('title');
            $table->text('description');
            $table->string('style_key', 32)->default('blue');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_program_items');
    }
};
