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
        Schema::create('enseignants', function (Blueprint $table) {
            $table->id();
            // Lien avec l'école (Multi-tenant)
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            
            // Lien optionnel avec la table users (pour son compte de connexion)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Informations de l'enseignant
            $table->string('matricule')->unique(); // Matricule de l'enseignant
            $table->string('nom');
            $table->string('postnom');
            $table->string('prenom')->nullable();
            $table->string('telephone')->nullable();
            $table->string('grade'); // ex: Principal, Titulaire, A1, G0...
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enseignants');
    }
};