<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Dans le nouveau système à 12 champs d'évaluation, le total est calculé
     * automatiquement à partir des champs (interrogation_s1, periode_1, ...).
     * La colonne `points_obtenus` (historique) n'est plus utilisée, donc elle
     * doit être nullable pour éviter l'erreur NOT NULL lors de l'insertion
     * d'une nouvelle cote sans points_obtenus.
     */
    public function up(): void
    {
        Schema::table('cotes', function (Blueprint $table) {
            $table->decimal('points_obtenus', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotes', function (Blueprint $table) {
            $table->decimal('points_obtenus', 5, 2)->nullable(false)->change();
        });
    }
};
