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
        Schema::table('cotes', function (Blueprint $table) {
            // Supprimer l'ancienne contrainte unique
            $table->dropUnique('unique_cote_eleve_periode');

            // Rendre periode_id nullable (car on passe à une structure par évaluation)
            $table->foreignId('periode_id')->nullable()->change();

            // Ajouter les champs d'évaluation
            $table->decimal('interrogation_s1', 5, 2)->nullable()->after('points_obtenus');
            $table->decimal('devoir_domicile_s1', 5, 2)->nullable()->after('interrogation_s1');
            $table->decimal('periode_1', 5, 2)->nullable()->after('devoir_domicile_s1');
            $table->decimal('periode_2', 5, 2)->nullable()->after('periode_1');
            $table->decimal('periode_3', 5, 2)->nullable()->after('periode_2');
            $table->decimal('examen_s1', 5, 2)->nullable()->after('periode_3');
            $table->decimal('interrogation_s2', 5, 2)->nullable()->after('examen_s1');
            $table->decimal('devoir_domicile_s2', 5, 2)->nullable()->after('interrogation_s2');
            $table->decimal('periode_4', 5, 2)->nullable()->after('devoir_domicile_s2');
            $table->decimal('periode_5', 5, 2)->nullable()->after('periode_4');
            $table->decimal('periode_6', 5, 2)->nullable()->after('periode_5');
            $table->decimal('examen_s2', 5, 2)->nullable()->after('periode_6');

            // Nouvelle contrainte unique : une ligne par élève par cours
            $table->unique(['inscription_id', 'plan_id'], 'unique_cote_eleve_cours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotes', function (Blueprint $table) {
            $table->dropUnique('unique_cote_eleve_cours');
            $table->dropColumn([
                'interrogation_s1', 'devoir_domicile_s1',
                'periode_1', 'periode_2', 'periode_3', 'examen_s1',
                'interrogation_s2', 'devoir_domicile_s2',
                'periode_4', 'periode_5', 'periode_6', 'examen_s2',
            ]);
            $table->decimal('points_obtenus', 5, 2)->change();
            $table->foreignId('periode_id')->nullable(false)->change();
            $table->unique(['inscription_id', 'plan_id', 'periode_id'], 'unique_cote_eleve_periode');
        });
    }
};
