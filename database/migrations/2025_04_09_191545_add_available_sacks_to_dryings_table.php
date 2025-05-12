<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dryings', function (Blueprint $table) {
            $table->integer('available_sacks')->after('number_sacks')->default(0);
        });


        // Actualiza los registros existentes
        DB::statement('UPDATE dryings SET available_sacks = number_sacks');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dryings', function (Blueprint $table) {
            $table->dropColumn('available_sacks');
        });
    }
};
