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
        Schema::create('fermentation_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('name_of_tracking')->nullable();
            $table->foreignId('fermentation_planning_id')->constrained('fermentation_plannings')->cascadeOnDelete();
            $table->foreignId('harvest_id')->constrained('harvests')->cascadeOnDelete();//el peso de la cosecha
            $table->float('weight')->nullable();
            $table->string('temperature')->nullable();
            $table->text('humidity')->nullable();


            $table->float('B_weight')->nullable();
            $table->float('total_weight')->nullable();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            //$table->foreignId('fermentation_id')->constrained('fermentations')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    
    /**

     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fermentation_trackings');
    }
};
