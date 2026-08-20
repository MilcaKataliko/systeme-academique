<?php

namespace App\Console\Commands;

use App\Models\BulletinImportAnomaly;
use App\Models\Cote;
use App\Models\Inscription;
use App\Models\Plan;
use App\Services\BulletinService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImporterNotesBulletin extends Command
{
    protected $signature = 'bulletin:importer-notes {fichier : CSV avec matricule,code_cours,champ,note} {--ecole= : ID de l\'école} {--encode-par= : ID utilisateur à enregistrer comme encodeur} {--dry-run : Contrôle sans enregistrer}';
    protected $description = 'Importe les notes existantes et place les lignes anormales en validation manuelle.';

    public function handle(): int
    {
        $path = $this->argument('fichier');
        if (! is_file($path) || ! ($handle = fopen($path, 'r'))) {
            $this->error("Fichier introuvable : {$path}"); return self::FAILURE;
        }
        $headers = fgetcsv($handle, 0, ';') ?: [];
        if (count($headers) === 1) { rewind($handle); $headers = fgetcsv($handle, 0, ',') ?: []; $separator = ','; } else $separator = ';';
        $headers = array_map(fn ($h) => strtolower(trim((string) $h)), $headers);
        if (array_diff(['matricule', 'code_cours', 'champ', 'note'], $headers)) {
            $this->error('Colonnes requises : matricule, code_cours, champ, note.'); return self::FAILURE;
        }
        $ecoleId = $this->option('ecole');
        if (! $ecoleId) { $this->error('Précisez --ecole=ID.'); return self::FAILURE; }
        $encodePar = $this->option('encode-par');
        if (! $encodePar) { $this->error('Précisez --encode-par=ID.'); return self::FAILURE; }
        $importees = $anomalies = 0; $ligne = 1;
        DB::beginTransaction();
        try {
            while (($values = fgetcsv($handle, 0, $separator)) !== false) {
                $ligne++; $row = array_combine($headers, array_pad($values, count($headers), null));
                $motif = null; $plan = null; $inscription = null;
                $champ = trim((string) ($row['champ'] ?? ''));
                $note = is_numeric(str_replace(',', '.', (string) ($row['note'] ?? ''))) ? (float) str_replace(',', '.', $row['note']) : null;
                $inscription = Inscription::where('ecole_id', $ecoleId)->whereHas('eleve', fn ($q) => $q->where('code_matricule', trim($row['matricule'] ?? '')))->first();
                if (! $inscription) $motif = 'Matricule introuvable pour cette école';
                if (! $motif) $plan = Plan::where('classe_id', $inscription->classe_id)->whereHas('cours', fn ($q) => $q->where('code_cours', trim($row['code_cours'] ?? '')))->first();
                if (! $motif && ! $plan) $motif = 'Cours introuvable dans la classe de l’élève';
                if (! $motif && ! in_array($champ, BulletinService::CHAMPS, true)) $motif = 'Champ d’évaluation inconnu';
                if (! $motif && $note === null) $motif = 'Note manquante ou non numérique';
                if (! $motif && ($note < 0 || $note > BulletinService::maximumPourChamp($plan, $champ))) $motif = 'Note hors barème (0 à '.BulletinService::maximumPourChamp($plan, $champ).')';
                if ($motif) {
                    BulletinImportAnomaly::create(['ecole_id' => $ecoleId, 'matricule' => trim($row['matricule'] ?? ''), 'code_cours' => trim($row['code_cours'] ?? ''), 'champ' => $champ, 'note' => $note, 'motif' => $motif, 'ligne_source' => $ligne]);
                    $anomalies++; continue;
                }
                Cote::updateOrCreate(['inscription_id' => $inscription->id, 'plan_id' => $plan->id], ['encode_par' => $encodePar, $champ => $note]);
                $importees++;
            }
        } catch (\Throwable $e) { DB::rollBack(); fclose($handle); throw $e; }
        fclose($handle);
        if ($this->option('dry-run')) DB::rollBack(); else DB::commit();
        $this->info("{$importees} note(s) importée(s), {$anomalies} anomalie(s) à valider.");
        return self::SUCCESS;
    }
}
