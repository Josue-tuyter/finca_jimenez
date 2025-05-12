<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drying_tracking_fermentation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drying_tracking_id')->constrained()->onDelete('cascade');
            $table->foreignId('fermentation_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drying_tracking_fermentation');
    }
};
