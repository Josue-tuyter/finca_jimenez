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
        Schema::create('dryings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('number_sacks')->nullable();
            $table->float('weight')->nullable();
            $table->foreignId('drying_planning_id')->constrained('drying_plannings')->onDelete('cascade');
            // llamar el nombre de la fermentacion
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
        Schema::dropIfExists('dryings');
    }
};
