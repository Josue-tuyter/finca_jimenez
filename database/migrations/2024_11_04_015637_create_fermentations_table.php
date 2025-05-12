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
        Schema::create('fermentations', function (Blueprint $table) {
            $table->id();
            $table->string('F_name');//poner asi "Fermentacion 1"
            $table->string('humidity')->nullable(); 
            $table->float('F_total_weight')->nullable(); 
            // esta ralcion tienen que ser para el ---peso total--- de la fermentacion
            $table->foreignId('fermentation_tracking_id')->constrained('fermentation_trackings')->onDelete('cascade'); //peso de la fermentacion
            $table->foreignId('fermentation_planning_id')->constrained('fermentation_plannings')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fermentations');
    }
};
