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
        Schema::create('drying_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            // Claves foráneas
            $table->foreignId('drying_method_id')->constrained('drying_methods')->cascadeOnDelete();
            $table->foreignId('drying_planning_id')->constrained('drying_plannings')->onDelete('cascade');
            // Campos adicionales
            $table->string('humidity')->nullable();
            $table->string('color')->nullable();
            $table->string('textura')->nullable();
            $table->string('moho')->nullable();
            
            $table->timestamps();
        });
    }

// Método de secado a sol o máquina.

// Fecha de inicio del secado.
// Fecha de fin del secado.

// Peso del cacao secado.
// Trabajador encargado.
// Nivel del cacao secado: Nivel de 
// humedad, color uniforme, ausencia o 
// presencia de moho y textura adecuada.



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drying_trackings');
    }
};
