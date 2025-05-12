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
        Schema::create('harvest_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('size')->nullable();
            $table->string('humidity')->nullable();
            $table->string('disease')->nullable();//PONER QUE COMO LOS OWNERS SE CARGUE Y PUEDAN CREARSE
            $table->foreignId('harvest_planning_id')->constrained('harvest_plannings')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvest_trackings');
    }
};
