<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table des présences hebdomadaires.
     * Chaque ligne = présence d'un élève (inscription) pour un cours (plan)
     * à une date donnée, avec un statut :
     *   - present    : l'élève est présent
     *   - absent     : l'élève est absent
     *   - malade     : l'élève est malade (considéré comme présent)
     *   - abandonne  : l'élève a abandonné le cours
     */
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->date('date');
            $table->string('statut')->default('present'); // present, absent, malade, abandonne
            $table->foreignId('encode_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Un seul statut par élève / cours / jour
            $table->unique(['inscription_id', 'plan_id', 'date'], 'une_presence_par_eleve_plan_jour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
