<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Reconstruire la table presences sur le modèle :
     * une présence = élève + plan + semaine_debut (lundi) + jour_index (0..4)
     * la date réelle de la colonne reste éditable (colonne `date`).
     */
    public function up(): void
    {
        // 1. Sauvegarder les données existantes
        $anciennes = DB::table('presences')->get()->map(fn($p) => (array) $p)->toArray();

        // 2. Supprimer l'ancienne table
        Schema::dropIfExists('presences');

        // 3. Recréer la table
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained('eleves')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            // Clé de semaine : le lundi de la semaine affichée dans la feuille
            $table->date('semaine_debut');
            // Index de la colonne : 0 = Lundi ... 4 = Vendredi
            $table->unsignedTinyInteger('jour_index');
            // Date réelle (éditable) de la séance
            $table->date('date');
            $table->string('statut')->default('present'); // present, absent, retard
            $table->foreignId('encode_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Un seul statut par élève / cours / semaine / colonne
            $table->unique(['eleve_id', 'plan_id', 'semaine_debut', 'jour_index'], 'une_presence_par_eleve_plan_semaine_colonne');
        });

        // 4. Réinsérer les données existantes en déduisant semaine_debut et jour_index
        foreach ($anciennes as $p) {
            if (empty($p['eleve_id']) || empty($p['plan_id']) || empty($p['date'])) {
                continue;
            }

            $date = Carbon::parse($p['date']);
            $lundi = $date->copy()->startOfWeek(Carbon::MONDAY);
            $jourIndex = (int) $date->format('N') - 1; // 1=Lundi..7=Dimanche -> 0..6

            // On ne garde que les jours scolaires (Lun-Ven) pour l'historique
            if ($jourIndex > 4) {
                $jourIndex = 4; // les séances du week-end sont rattachées au vendredi
            }

            DB::table('presences')->updateOrInsert(
                [
                    'eleve_id' => $p['eleve_id'],
                    'plan_id' => $p['plan_id'],
                    'semaine_debut' => $lundi->toDateString(),
                    'jour_index' => $jourIndex,
                ],
                [
                    'date' => $date->toDateString(),
                    'statut' => $p['statut'] ?? 'present',
                    'encode_par' => $p['encode_par'] ?? null,
                    'created_at' => $p['created_at'] ?? now(),
                    'updated_at' => $p['updated_at'] ?? now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('presences');

        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained('eleves')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->date('date');
            $table->string('statut')->default('present');
            $table->foreignId('encode_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->unique(['eleve_id', 'plan_id', 'date']);
        });
    }
};
