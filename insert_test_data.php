<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Ecole;
use App\Models\User;
use App\Models\Enseignant;
use Illuminate\Support\Facades\Hash;

// 1. Vérifier ou créer l'école
$ecole = Ecole::first();
if (!$ecole) {
    $ecole = Ecole::create([
        'nom_ecole' => 'Complexe Scolaire EPST',
        'code_national_epst' => 'CNS-2025-001',
        'province_educationnelle' => 'Kinshasa',
        'adresse' => '123 Avenue de l\'Education, Kinshasa',
    ]);
    echo "✓ École créée : {$ecole->nom_ecole}" . PHP_EOL;
} else {
    echo "→ École existante : {$ecole->nom_ecole}" . PHP_EOL;
}

// 2. Créer les utilisateurs de test
$users = [
    [
        'name' => 'Jean Directeur',
        'email' => 'directeur@epst.cd',
        'password' => 'password123',
        'role' => 'directeur',
    ],
    [
        'name' => 'Marie Enseignante',
        'email' => 'enseignant@epst.cd',
        'password' => 'password123',
        'role' => 'enseignant',
    ],
    [
        'name' => 'Paul Comptable',
        'email' => 'comptable@epst.cd',
        'password' => 'password123',
        'role' => 'comptable',
    ],
    [
        'name' => 'Grace Eleve',
        'email' => 'eleve@epst.cd',
        'password' => 'password123',
        'role' => 'eleve',
    ],
];

$count = 0;
foreach ($users as $data) {
    $exists = User::where('email', $data['email'])->first();
    if (!$exists) {
        $user = User::create([
            'ecole_id' => $ecole->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);
        echo "✓ Utilisateur créé : {$data['name']} ({$data['role']})" . PHP_EOL;

        // Si c'est un enseignant, créer aussi sa fiche professionnelle
        if ($data['role'] === 'enseignant') {
            $enseignantExists = Enseignant::where('user_id', $user->id)->exists();
            if (!$enseignantExists) {
                // Générer un matricule
                $annee = date('Y');
                $lastEns = Enseignant::where('ecole_id', $ecole->id)
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

                Enseignant::create([
                    'ecole_id' => $ecole->id,
                    'user_id' => $user->id,
                    'matricule' => $matricule,
                    'nom' => 'Marie',
                    'postnom' => 'Enseignante',
                    'prenom' => 'Claire',
                    'telephone' => '+243 800 000 001',
                    'grade' => 'Titulaire',
                ]);
                echo "  ✓ Fiche Enseignant créée (Matricule: {$matricule})" . PHP_EOL;
            }
        }

        $count++;
    } else {
        echo "→ Existe déjà : {$data['name']} ({$data['email']})" . PHP_EOL;
    }
}

echo PHP_EOL . str_repeat('=', 60) . PHP_EOL;
echo "RÉCAPITULATIF DES COMPTES DE TEST" . PHP_EOL;
echo str_repeat('=', 60) . PHP_EOL;
echo sprintf("%-25s | %-30s | %-15s", "NOM", "EMAIL", "MOT DE PASSE") . PHP_EOL;
echo str_repeat('-', 75) . PHP_EOL;
echo sprintf("%-25s | %-30s | %-15s", "Jean Directeur", "directeur@epst.cd", "password123") . PHP_EOL;
echo sprintf("%-25s | %-30s | %-15s", "Marie Enseignante", "enseignant@epst.cd", "password123") . PHP_EOL;
echo sprintf("%-25s | %-30s | %-15s", "Paul Comptable", "comptable@epst.cd", "password123") . PHP_EOL;
echo sprintf("%-25s | %-30s | %-15s", "Grace Eleve", "eleve@epst.cd", "password123") . PHP_EOL;
echo str_repeat('-', 75) . PHP_EOL;

$total = User::count();
echo "Total utilisateurs dans la base : {$total}" . PHP_EOL;
