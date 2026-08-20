<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Reconstruire la table presences avec eleve_id comme unique référence
     * (supprimer inscription_id) et un index unique sur (eleve_id, plan_id, date).
     */
    public function up(): void
    {
        // 1. Sauvegarder les données existantes
        $anciennes = DB::table('presences')->get()
            ->map(fn($p) => (array) $p)
            ->toArray();

        // 2. Supprimer la table actuelle
        Schema::dropIfExists('presences');

        // 3. Recréer la table simplifiée
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained('eleves')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->date('date');
            $table->string('statut')->default('present'); // present, absent, retard
            $table->foreignId('encode_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Un seul statut par élève / cours / jour
            $table->unique(['eleve_id', 'plan_id', 'date'], 'une_presence_par_eleve_plan_jour');
        });

        // 4. Réinsérer les données existantes (normalisées)
        foreach ($anciennes as $p) {
            if (empty($p['eleve_id']) || empty($p['plan_id']) || empty($p['date'])) {
                continue;
            }

            DB::table('presences')->updateOrInsert(
                [
                    'eleve_id' => $p['eleve_id'],
                    'plan_id' => $p['plan_id'],
                    'date' => \Illuminate\Support\Carbon::parse($p['date'])->format('Y-m-d'),
                ],
                [
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
            $table->foreignId('inscription_id')->constrained('inscriptions')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->date('date');
            $table->string('statut')->default('present');
            $table->foreignId('encode_par')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->unique(['inscription_id', 'plan_id', 'date'], 'une_presence_par_eleve_plan_jour');
        });
    }
};
