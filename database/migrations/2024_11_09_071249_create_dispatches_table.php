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
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('number_sacks')->nullable();
            $table->integer('original_number_sacks');
            $table->date('delivery_date')->default(now());
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->timestamps();
        }); 


    }

    /*

Cantidad de sacos.
Fecha de entrega.
Nombre del cliente.
Estado del pedido.

    */


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
