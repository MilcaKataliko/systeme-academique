<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Simplifier la table frais : ajouter classe_id, annee_scolaire, montant
     * Compatible SQLite.
     */
    public function up(): void
    {
        // 1. Créer une table temporaire avec la nouvelle structure
        Schema::create('frais_temp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            $table->string('intitule_frais');
            $table->decimal('montant', 10, 2)->default(0);
            $table->string('devise', 3)->default('USD');
            $table->foreignId('classe_id')->nullable()->constrained('classes')->onDelete('cascade');
            $table->string('annee_scolaire')->nullable();
            $table->timestamps();
        });

        // 2. Copier les données existantes avec montant_standard comme montant
        $frais = DB::table('frais')->get();
        foreach ($frais as $f) {
            DB::table('frais_temp')->insert([
                'id' => $f->id,
                'ecole_id' => $f->ecole_id,
                'intitule_frais' => $f->intitule_frais,
                'montant' => $f->montant_standard ?? 0,
                'devise' => $f->devise ?? 'USD',
                'classe_id' => null,
                'annee_scolaire' => null,
                'created_at' => $f->created_at,
                'updated_at' => $f->updated_at,
            ]);
        }

        // 3. Copier les données de frais_classe si la table existe
        if (Schema::hasTable('frais_classe')) {
            $fraisClasses = DB::table('frais_classe')->get();
            foreach ($fraisClasses as $fc) {
                // Mettre à jour les lignes existantes dans frais_temp
                DB::table('frais_temp')
                    ->where('id', $fc->frais_id)
                    ->update([
                        'classe_id' => $fc->classe_id,
                        'annee_scolaire' => $fc->annee_scolaire,
                        'montant' => $fc->montant_specifique,
                    ]);
            }
        }

        // 4. Supprimer l'ancienne table et renommer la nouvelle
        Schema::dropIfExists('frais');
        Schema::rename('frais_temp', 'frais');

        // 5. Recréer les clés étrangères pour paiements (frais_id)
        // Supprimer d'abord l'ancienne, puis recréer
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['frais_id']);
            $table->foreign('frais_id')->references('id')->on('frais')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Restaurer la structure originale
        Schema::create('frais_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_id')->constrained('ecoles')->onDelete('cascade');
            $table->string('intitule_frais');
            $table->decimal('montant_standard', 10, 2);
            $table->string('devise', 3)->default('USD');
            $table->timestamps();
        });

        $frais = DB::table('frais')->get();
        foreach ($frais as $f) {
            DB::table('frais_old')->insert([
                'id' => $f->id,
                'ecole_id' => $f->ecole_id,
                'intitule_frais' => $f->intitule_frais,
                'montant_standard' => $f->montant,
                'devise' => $f->devise,
                'created_at' => $f->created_at,
                'updated_at' => $f->updated_at,
            ]);
        }

        Schema::dropIfExists('frais');
        Schema::rename('frais_old', 'frais');
    }
};

