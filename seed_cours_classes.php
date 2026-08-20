<?php
/**
 * Script pour insérer des données de test :
 * - Options (Commerciale, Pédagogie, Chimie-Bio, Latin-Philo)
 * - Classes (par option et niveau)
 * - Cours/Matières (Math, Français, Anglais, etc.)
 * - Attribution des cours aux classes (Plan)
 * - Enseignant de test
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ecole;
use App\Models\Option;
use App\Models\Classe;
use App\Models\Cours;
use App\Models\Plan;
use App\Models\Enseignant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "=== INSERTION DES DONNÉES DE TEST ===\n\n";

// 1. Récupérer l'école
$ecole = Ecole::first();
if (!$ecole) {
    die("❌ Aucune école trouvée. Exécutez d'abord 'php insert_test_data.php'");
}
echo "✓ École : {$ecole->nom_ecole} (ID: {$ecole->id})\n";

$ecoleId = $ecole->id;

// 2. Créer les Options
$options = [
    ['nomoption' => 'Commerciale', 'sigle' => 'COM'],
    ['nomoption' => 'Pédagogie', 'sigle' => 'PED'],
    ['nomoption' => 'Chimie-Biologie', 'sigle' => 'CHB'],
    ['nomoption' => 'Latin-Philosophie', 'sigle' => 'LPH'],
];

$optionIds = [];
foreach ($options as $optData) {
    $opt = Option::firstOrCreate(
        ['nomoption' => $optData['nomoption'], 'ecole_id' => $ecoleId],
        ['sigle' => $optData['sigle'], 'ecole_id' => $ecoleId]
    );
    $optionIds[] = $opt->idOption;
    echo "✓ Option : {$opt->nomoption} ({$opt->sigle})\n";
}

// 3. Créer les Cours/Matières
$coursList = [
    ['nom_cours' => 'Mathématiques', 'code_cours' => 'MATH'],
    ['nom_cours' => 'Français', 'code_cours' => 'FRAN'],
    ['nom_cours' => 'Anglais', 'code_cours' => 'ANGL'],
    ['nom_cours' => 'Physique', 'code_cours' => 'PHYS'],
    ['nom_cours' => 'Chimie', 'code_cours' => 'CHIM'],
    ['nom_cours' => 'Biologie', 'code_cours' => 'BIO'],
    ['nom_cours' => 'Histoire', 'code_cours' => 'HIST'],
    ['nom_cours' => 'Géographie', 'code_cours' => 'GEO'],
    ['nom_cours' => 'Informatique', 'code_cours' => 'INFO'],
    ['nom_cours' => 'Éducation Physique', 'code_cours' => 'EDPH'],
];

$coursIds = [];
foreach ($coursList as $coursData) {
    $cours = Cours::firstOrCreate(
        ['nom_cours' => $coursData['nom_cours'], 'ecole_id' => $ecoleId],
        ['code_cours' => $coursData['code_cours'], 'ecole_id' => $ecoleId]
    );
    $coursIds[] = $cours->id;
    echo "✓ Cours : {$cours->nom_cours} ({$cours->code_cours})\n";
}

// 4. Créer les Classes (3 classes par option = 12 classes)
$niveaux = [7, 8, 1]; // 7ème, 8ème, 1ère
$nomsNiveaux = [7 => '7ème', 8 => '8ème', 1 => '1ère'];
$classesCrees = [];

foreach ($options as $optData) {
    $opt = Option::where('nomoption', $optData['nomoption'])
        ->where('ecole_id', $ecoleId)
        ->first();

    foreach ($niveaux as $niv) {
        $nomClasse = "{$nomsNiveaux[$niv]} {$optData['nomoption']}";
        $classe = Classe::firstOrCreate(
            [
                'nom_classe' => $nomClasse,
                'option_id' => $opt->idOption,
                'niveau' => $niv,
            ],
            ['section' => $optData['nomoption']]
        );
        $classesCrees[] = $classe;
        echo "✓ Classe : {$classe->nom_classe} (Niv. {$niv})\n";
    }
}

// 5. Créer un enseignant de test si pas déjà fait
$enseignantUser = User::where('email', 'professeur@epst.cd')->first();
if (!$enseignantUser) {
    $enseignantUser = User::create([
        'ecole_id' => $ecoleId,
        'name' => 'Pierre Professeur',
        'email' => 'professeur@epst.cd',
        'password' => Hash::make('password123'),
        'role' => 'enseignant',
    ]);
    echo "✓ Compte enseignant créé : Pierre Professeur\n";
}

$enseignant = Enseignant::firstOrCreate(
    ['user_id' => $enseignantUser->id],
    [
        'ecole_id' => $ecoleId,
        'matricule' => 'ENS-' . str_pad($enseignantUser->id, 4, '0', STR_PAD_LEFT),
        'nom' => 'Pierre',
        'postnom' => 'Professeur',
        'prenom' => 'Jean',
        'grade' => 'Gradué',
        'telephone' => '0999123456',
    ]
);
echo "✓ Enseignant : {$enseignant->nom} {$enseignant->postnom} ({$enseignant->grade})\n";

// 6. Attribuer quelques cours aux classes (Plan)
$annee = date('Y') . '-' . (date('Y') + 1);
$attributions = 0;

// Pour chaque classe, attribuer 4-5 cours
foreach ($classesCrees as $classe) {
    // Prendre 5 cours aléatoires
    $selectedCours = array_slice($coursIds, 0, 5);
    
    foreach ($selectedCours as $coursId) {
        $existe = Plan::where('classe_id', $classe->id)
            ->where('cours_id', $coursId)
            ->where('annee_scolaire', $annee)
            ->exists();

        if (!$existe) {
            Plan::create([
                'classe_id' => $classe->id,
                'cours_id' => $coursId,
                'enseignant_id' => $enseignantUser->id,
                'maxima_periode' => 20,
                'maxima_examen' => 20,
                'annee_scolaire' => $annee,
            ]);
            $attributions++;

            $cours = Cours::find($coursId);
            echo "✓ Attribution : {$cours->nom_cours} → {$classe->nom_classe} ({$annee})\n";
        }
    }
}

echo "\n=== RÉSUMÉ ===\n";
echo "Options : " . count($options) . "\n";
echo "Cours : " . count($coursList) . "\n";
echo "Classes : " . count($classesCrees) . "\n";
echo "Attributions : {$attributions}\n";
echo "\n✅ Connectez-vous avec :\n";
echo "   Directeur    : directeur@epst.cd / password123\n";
echo "   Enseignant   : professeur@epst.cd / password123\n";
echo "   Enseignante  : enseignant@epst.cd / password123\n";
