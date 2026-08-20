<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Enseignant;
use App\Models\User;

echo "=== FICHES ENSEIGNANTS EN BASE ===\n\n";

$enseignants = Enseignant::with('user')->orderBy('id')->get();

echo sprintf("%-3s | %-20s | %-20s | %-10s | %-15s | %-20s\n", 
    "ID", "NOM", "POSTNOM", "MATRICULE", "GRADE", "COMPTE LIE");
echo str_repeat('-', 95) . "\n";

foreach ($enseignants as $e) {
    echo sprintf("%-3d | %-20s | %-20s | %-10s | %-15s | %-20s\n",
        $e->id,
        $e->nom,
        $e->postnom,
        $e->matricule,
        $e->grade,
        $e->user->name ?? 'N/A'
    );
}

echo "\n---\n";
echo "Total enseignants avec fiche : " . $enseignants->count() . "\n";

// Vérifier les users enseignant sans fiche
echo "\n=== COMPTES ENSEIGNANTS SANS FICHE ===\n\n";
$sansFiche = User::where('role', 'enseignant')
    ->whereDoesntHave('enseignant')
    ->get();

if ($sansFiche->isEmpty()) {
    echo "✓ Tous les comptes enseignants ont une fiche professionnelle !\n";
} else {
    echo "⚠️ " . $sansFiche->count() . " compte(s) sans fiche :\n";
    foreach ($sansFiche as $u) {
        echo "  - {$u->name} ({$u->email})\n";
    }
}

