<?php
/**
 * Script pour créer les fiches Enseignant pour les comptes utilisateurs 
 * existants avec le rôle 'enseignant' qui n'ont pas encore de fiche.
 * 
 * Usage: php seed_enseignants_existants.php
 */
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Enseignant;

echo "Recherche des enseignants sans fiche professionnelle...\n";
echo str_repeat('-', 60) . "\n";

$enseignants = User::where('role', 'enseignant')
    ->whereDoesntHave('enseignant')
    ->get();

if ($enseignants->isEmpty()) {
    echo "✓ Tous les enseignants ont déjà une fiche professionnelle.\n";
    exit(0);
}

echo "→ {$enseignants->count()} enseignant(s) sans fiche trouvé(s).\n\n";

foreach ($enseignants as $user) {
    // Générer un matricule
    $annee = date('Y');
    $lastEns = Enseignant::where('ecole_id', $user->ecole_id)
        ->where('matricule', 'like', "ENS-{$annee}-%")
        ->orderBy('matricule', 'desc')
        ->first();
    
    if ($lastEns) {
        $lastNum = (int) substr($lastEns->matricule, -4);
        $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newNum = '0001';
    }
    $matricule = "ENS-{$annee}-{$newNum}";

    $nameParts = explode(' ', $user->name, 2);
    $nom = $nameParts[0] ?? $user->name;
    $postnom = $nameParts[1] ?? '';

    Enseignant::create([
        'ecole_id' => $user->ecole_id,
        'user_id' => $user->id,
        'matricule' => $matricule,
        'nom' => $nom,
        'postnom' => $postnom,
        'prenom' => '',
        'telephone' => '',
        'grade' => 'Titulaire',
    ]);

    echo "✓ Fiche créée pour : {$user->name} ({$user->email}) -> Matricule: {$matricule}\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Opération terminée. {$enseignants->count()} fiche(s) créée(s).\n";

