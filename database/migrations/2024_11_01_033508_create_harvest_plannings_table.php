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
        Schema::create('harvest_plannings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->date('date_start')->default(now());
            $table->date('date_end')->default(now());
            $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            $table->foreignId('parcel_id')->constrained('parcels')->cascadeOnDelete();
            $table->timestamps();
        });

        //$table->string('name')->default('');`
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvest_plannings');
    }
};
