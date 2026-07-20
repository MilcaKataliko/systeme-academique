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
        // 1. Création de la table des Écoles (Norme RDC - EPST)
        Schema::create('ecoles', function (Blueprint $table) {
            $table->id();
            $table->string('nom_ecole');
            $table->string('code_national_epst')->unique();
            $table->string('province_educationnelle');
            $table->string('adresse');
            $table->timestamps();
        });

        // 2. Ajout du cloisonnement et des rôles dans la table Users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('ecole_id')->nullable()->after('id')->constrained('ecoles')->onDelete('cascade');
            $table->string('role')->after('password')->default('eleve');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ecole_id']);
            $table->dropColumn(['ecole_id', 'role']);
        });

        Schema::dropIfExists('ecoles');
    }
};