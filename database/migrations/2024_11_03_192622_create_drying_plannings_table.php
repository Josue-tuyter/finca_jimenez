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
        Schema::create('drying_plannings', function (Blueprint $table) {
            $table->id(); 
            $table->string('name')->nullable();
            $table->date('D_date_start')->default(now());
            $table->date('D_date_end')->default(now());
            $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drying_plannings');
    }
};
