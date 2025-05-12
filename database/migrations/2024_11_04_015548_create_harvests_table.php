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
        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('number_buckests')->nullable();
            $table->float('weight')->nullable();
            // $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            // $table->foreignId('parcel_id')->constrained('parcels')->cascadeOnDelete();
            $table->foreignId('harvest_planning_id')->constrained('harvest_plannings')->onDelete('cascade');
            //$table->foreignId('fermentation_tracking_id')->constrained('fermentations_trackings')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};
