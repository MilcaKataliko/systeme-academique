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
        // 1. Table : Élèves (Dépend de 'ecoles' existante)
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nom');
            $table->string('postnom')->nullable();
            $table->string('prenom')->nullable();
            $table->string('genre', 1); // M ou F
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('code_matricule')->unique()->nullable();
            $table->timestamps();
        });

        // 2. Table : Périodes
        Schema::create('periodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            $table->string('nom_periode'); // ex: 1ère Période, Examen 1er Semestre
            $table->boolean('est_cloturee')->default(false);
            $table->timestamps();
        });

        // 3. Table : Cours
        Schema::create('cours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            $table->string('nom_cours'); // ex: Mathématiques, Informatique
            $table->string('code_cours')->nullable();
            $table->timestamps();
        });

        // 4. Table : Inscriptions (Fait le lien entre l'Élève et ta table 'classes' existante)
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            $table->foreignId('eleve_id')->constrained('eleves')->onDelete('cascade');
            $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade'); // Clé vers ta table existante
            $table->string('annee_scolaire'); // ex: 2025-2026
            $table->string('statut')->default('actif');
            $table->timestamps();
        });

        // 5. Table : Frais
        Schema::create('frais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            $table->string('intitule_frais'); // ex: Minerval, Frais de participation
            $table->decimal('montant_standard', 10, 2);
            $table->string('devise', 3)->default('USD');
            $table->timestamps();
        });

        // 6. Table : FraisClasse (Associe un frais à ta table 'classes' existante)
        Schema::create('frais_classe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade'); // Clé vers ta table existante
            $table->foreignId('frais_id')->constrained('frais')->onDelete('cascade');
            $table->decimal('montant_specifique', 10, 2);
            $table->string('annee_scolaire');
            $table->timestamps();
        });

        // 7. Table : Paiements
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');
            $table->foreignId('frais_id')->constrained('frais')->onDelete('cascade');
            $table->foreignId('comptable_id')->constrained('users')->onDelete('restrict');
            $table->decimal('montant_paye', 10, 2);
            $table->date('date_paiement');
            $table->string('numero_recu')->unique();
            $table->string('mode_paiement')->default('especes');
            $table->timestamps();
        });

        // 8. Table : Plan (Associe un cours à ta table 'classes' existante)
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade'); // Clé vers ta table existante
            $table->foreignId('cours_id')->constrained('cours')->onDelete('cascade');
            $table->foreignId('enseignant_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('maxima_periode')->default(10);
            $table->integer('maxima_examen')->default(20);
            $table->string('annee_scolaire');
            $table->timestamps();
        });

        // 9. Table : Cotes
        Schema::create('cotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->foreignId('periode_id')->constrained('periodes')->onDelete('cascade');
            $table->decimal('points_obtenus', 5, 2);
            $table->foreignId('encode_par')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->unique(['inscription_id', 'plan_id', 'periode_id'], 'unique_cote_eleve_periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotes');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('paiements');
        Schema::dropIfExists('frais_classe');
        Schema::dropIfExists('frais');
        Schema::dropIfExists('inscriptions');
        Schema::dropIfExists('cours');
        Schema::dropIfExists('periodes');
        Schema::dropIfExists('eleves');
    }
};