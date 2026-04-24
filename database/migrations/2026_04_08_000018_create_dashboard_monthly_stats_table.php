<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dashboard_monthly_stats', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->json('kpi')->nullable();
            $table->timestamps();

            $table->unique(['year', 'month']);
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_monthly_stats');
    }
};
