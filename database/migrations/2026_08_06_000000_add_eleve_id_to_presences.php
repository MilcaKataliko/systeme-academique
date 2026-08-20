<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Simplifier la table presences : ajouter eleve_id (référence directe à l'élève)
     * et convertir les données existantes depuis inscription_id.
     *
     * Table finale : eleve_id, plan_id, date, statut (present|absent|retard), encode_par.
     */
    public function up(): void
    {
        // 1. Ajouter la colonne eleve_id (nullable dans un premier temps)
        Schema::table('presences', function (Blueprint $table) {
            $table->foreignId('eleve_id')->nullable()->after('id')->constrained('eleves')->onDelete('cascade');
        });

        // 2. Backfill eleve_id depuis inscription_id (chaque inscription est liée à un élève)
        $presences = DB::table('presences')
            ->join('inscriptions', 'inscriptions.id', '=', 'presences.inscription_id')
            ->select('presences.id', 'inscriptions.eleve_id')
            ->get();

        foreach ($presences as $p) {
            if ($p->eleve_id !== null) {
                DB::table('presences')
                    ->where('id', $p->id)
                    ->update(['eleve_id' => $p->eleve_id]);
            }
        }

        // 3. Rendre eleve_id non nullable maintenant qu'il est rempli
        Schema::table('presences', function (Blueprint $table) {
            $table->foreignId('eleve_id')->nullable(false)->change();
        });

        // 4. Ajouter le statut 'retard' (les statuts existants present/absent/malade/abandonne
        //    sont conservés ; retard est ajouté pour la nouvelle logique simplifiée)
        //    On normalise : malade|abandonne => present/absent selon la logique simplifiée.
        DB::table('presences')
            ->where('statut', 'malade')
            ->update(['statut' => 'present']);
        DB::table('presences')
            ->where('statut', 'abandonne')
            ->update(['statut' => 'absent']);
    }

    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->dropForeign(['eleve_id']);
            $table->dropColumn('eleve_id');
        });
    }
};
