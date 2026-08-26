<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnneeController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\CorpsEnseignantController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\ComptableController;

Route::get('/ping', function () {
    try {
        DB::select('SELECT 1');
        return response()->json(['status' => 'ok', 'timestamp' => now()], 200);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});
// --- ROUTES PUBLIQUES (Accessibles sans être connecté) ---
Route::get('/', [CustomAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [CustomAuthController::class, 'login']);
Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');

// Les deux routes d'inscription de l'établissement à vérifier :
Route::get('/enregistrer-ecole', [CustomAuthController::class, 'showSchoolRegister'])->name('school.register.show');
Route::post('/enregistrer-ecole', [CustomAuthController::class, 'registerSchool'])->name('school.register.store');


// --- ROUTES SÉCURISÉES (Uniquement pour les utilisateurs connectés) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/profil', [CustomAuthController::class, 'profile'])->name('profile');
    Route::put('/profil', [CustomAuthController::class, 'profileUpdate'])->name('profile.update');

    // Création de comptes par le Directeur
    Route::get('/directeur/creer-compte', [CustomAuthController::class, 'showRegister'])->name('register.show');
    Route::post('/directeur/creer-compte', [CustomAuthController::class, 'register'])->name('register.store');

    // Gestion des utilisateurs (CRUD complet + réinitialisation mot de passe)
    Route::get('/directeur/utilisateurs', [CustomAuthController::class, 'usersIndex'])->name('users.index');
    Route::get('/directeur/utilisateurs/{id}/modifier', [CustomAuthController::class, 'usersEdit'])->name('users.edit');
    Route::put('/directeur/utilisateurs/{id}', [CustomAuthController::class, 'usersUpdate'])->name('users.update');
    Route::delete('/directeur/utilisateurs/{id}', [CustomAuthController::class, 'usersDestroy'])->name('users.destroy');
    Route::put('/directeur/utilisateurs/{id}/reset-password', [CustomAuthController::class, 'usersResetPassword'])->name('users.reset-password');

    // Gestion des options
    Route::get('/directeur/options', [OptionController::class, 'index'])->name('options.index');
    Route::get('/directeur/options/creer', [OptionController::class, 'create'])->name('options.create');
    Route::post('/directeur/options', [OptionController::class, 'store'])->name('options.store');
    Route::delete('/directeur/options/{id}', [OptionController::class, 'destroy'])->name('options.destroy');

    // Gestion des années scolaires
    Route::get('/directeur/annees', [AnneeController::class, 'index'])->name('annees.index');
    Route::get('/directeur/annees/creer', [AnneeController::class, 'create'])->name('annees.create');
    Route::post('/directeur/annees', [AnneeController::class, 'store'])->name('annees.store');
    Route::get('/directeur/annees/{id}/modifier', [AnneeController::class, 'edit'])->name('annees.edit');
    Route::put('/directeur/annees/{id}', [AnneeController::class, 'update'])->name('annees.update');
    Route::delete('/directeur/annees/{id}', [AnneeController::class, 'destroy'])->name('annees.destroy');

    // Gestion des classes
    Route::get('/directeur/classes', [ClasseController::class, 'index'])->name('directeur.classes.index');
    Route::post('/directeur/classes', [ClasseController::class, 'store'])->name('directeur.classes.store');
    Route::get('/directeur/classes/{id}/modifier', [ClasseController::class, 'edit'])->name('directeur.classes.edit');
    Route::put('/directeur/classes/{id}', [ClasseController::class, 'update'])->name('directeur.classes.update');
    Route::delete('/directeur/classes/{id}', [ClasseController::class, 'destroy'])->name('directeur.classes.destroy');

    // Gestion des cours (matières)
    Route::get('/directeur/cours', [CoursController::class, 'index'])->name('directeur.cours.index');
    Route::post('/directeur/cours', [CoursController::class, 'store'])->name('directeur.cours.store');
    Route::get('/directeur/cours/{id}/modifier', [CoursController::class, 'edit'])->name('directeur.cours.edit');
    Route::put('/directeur/cours/{id}', [CoursController::class, 'update'])->name('directeur.cours.update');
    Route::delete('/directeur/cours/{id}', [CoursController::class, 'destroy'])->name('directeur.cours.destroy');

    // Tableau de bord directeur
    Route::get('/directeur/dashboard', [\App\Http\Controllers\CustomAuthController::class, 'directeurDashboard'])->name('directeur.dashboard');

    // --- Module Corps Enseignant (Directeur) ---
    Route::get('/directeur/enseignants', [CorpsEnseignantController::class, 'index'])->name('directeur.enseignants');
    Route::get('/directeur/enseignants/attributions/{enseignantId?}', [CorpsEnseignantController::class, 'attributionForm'])->name('directeur.enseignants.attributions');
    Route::post('/directeur/enseignants/attributions', [CorpsEnseignantController::class, 'storeAttribution'])->name('directeur.enseignants.attributions.store');
    Route::delete('/directeur/enseignants/attributions/{planId}', [CorpsEnseignantController::class, 'destroyAttribution'])->name('directeur.enseignants.attributions.destroy');
    Route::get('/directeur/enseignants/supervision-cotes', [CorpsEnseignantController::class, 'supervisionCotes'])->name('directeur.enseignants.supervision');

    // --- Module Inscriptions Élèves (Directeur) ---
    Route::get('/directeur/eleves', [InscriptionController::class, 'index'])->name('directeur.eleves.index');
    Route::get('/directeur/eleves/creer', [InscriptionController::class, 'create'])->name('directeur.eleves.create');
    Route::post('/directeur/eleves', [InscriptionController::class, 'store'])->name('directeur.eleves.store');
    Route::get('/directeur/eleves/{id}', [InscriptionController::class, 'show'])->name('directeur.eleves.show');
    Route::get('/directeur/eleves/{id}/modifier', [InscriptionController::class, 'edit'])->name('directeur.eleves.edit');
    Route::put('/directeur/eleves/{id}', [InscriptionController::class, 'update'])->name('directeur.eleves.update');
    Route::delete('/directeur/eleves/{id}', [InscriptionController::class, 'destroy'])->name('directeur.eleves.destroy');
    Route::get('/directeur/eleves/bulletin/{inscriptionId}', [InscriptionController::class, 'bulletin'])->name('directeur.eleves.bulletin');

    // Gestion des cotes par classe (Directeur)
    Route::get('/directeur/eleves/cotes/classe/{classeId}/{planId?}', [InscriptionController::class, 'cotesParClasse'])->name('directeur.eleves.cotes.classe');
    Route::post('/directeur/eleves/cotes/mettre-a-jour', [InscriptionController::class, 'mettreAJourCote'])->name('directeur.eleves.cotes.update');
    Route::get('/directeur/bulletin/validation-imports', [InscriptionController::class, 'validationImports'])->name('directeur.bulletin.validation');
    Route::put('/directeur/bulletin/validation-imports/{anomalie}', [InscriptionController::class, 'corrigerImport'])->name('directeur.bulletin.validation.corriger');

    // --- Espace Enseignant ---
    Route::get('/enseignant/dashboard', [EnseignantController::class, 'dashboard'])->name('enseignant.dashboard');
Route::get('/enseignant/classe/{classeId}/eleves/{planId?}', [EnseignantController::class, 'elevesParClasse'])->name('enseignant.eleves.classe');
    Route::post('/enseignant/classe/{classeId}/cotes', [EnseignantController::class, 'enregistrerCotes'])->name('enseignant.cotes.store');
    Route::post('/enseignant/classe/{classeId}/matiere/{planId}/valider-bulletins', [EnseignantController::class, 'validerMatiereBulletins'])->name('enseignant.bulletins.matiere.valider');

// Gestion des présences hebdomadaires (Enseignant)
    Route::get('/enseignant/classe/{classeId}/presence/{planId?}/{date?}', [EnseignantController::class, 'presenceForm'])->name('enseignant.presence.form');
    Route::post('/enseignant/classe/{classeId}/presence', [EnseignantController::class, 'enregistrerPresence'])->name('enseignant.presence.store');
    Route::get('/enseignant/profil', [EnseignantController::class, 'profil'])->name('enseignant.profil');
    Route::get('/enseignant/statistiques', [EnseignantController::class, 'statistiques'])->name('enseignant.statistiques');

    // --- Espace Comptable (Gestion des frais et paiements) ---
    Route::get('/comptable/dashboard', [ComptableController::class, 'dashboard'])->name('comptable.dashboard');

    // Gestion des frais (un frais = intitulé + classe + montant + année)
    Route::get('/comptable/frais', [ComptableController::class, 'fraisIndex'])->name('comptable.frais.index');
    Route::post('/comptable/frais', [ComptableController::class, 'fraisStore'])->name('comptable.frais.store');
    Route::delete('/comptable/frais/{id}', [ComptableController::class, 'fraisDestroy'])->name('comptable.frais.destroy');

    // Association frais ↔ classe (table pivot frais_classe)
    Route::post('/comptable/frais/classe', [ComptableController::class, 'fraisClasseStore'])->name('comptable.frais.classe.store');
    Route::delete('/comptable/frais/classe/{id}', [ComptableController::class, 'fraisClasseDestroy'])->name('comptable.frais.classe.destroy');

    // Gestion des paiements
    Route::get('/comptable/paiements', [ComptableController::class, 'paiementsIndex'])->name('comptable.paiements.index');
    Route::get('/comptable/paiements/creer', [ComptableController::class, 'paiementsCreate'])->name('comptable.paiements.create');
    Route::post('/comptable/paiements', [ComptableController::class, 'paiementsStore'])->name('comptable.paiements.store');
    Route::get('/comptable/paiements/{id}', [ComptableController::class, 'paiementsShow'])->name('comptable.paiements.show');
    Route::delete('/comptable/paiements/{id}', [ComptableController::class, 'paiementsDestroy'])->name('comptable.paiements.destroy');

    // Relevé par élève
    Route::get('/comptable/releve/{eleveId}', [ComptableController::class, 'releveEleve'])->name('comptable.paiements.releve');

    // --- Module Rappels Automatiques (Comptable) ---
    Route::get('/comptable/rappels', [\App\Http\Controllers\RappelController::class, 'index'])->name('comptable.rappels.index');
    Route::put('/comptable/rappels/config', [\App\Http\Controllers\RappelController::class, 'updateConfig'])->name('comptable.rappels.update');
    Route::get('/comptable/rappels/logs', [\App\Http\Controllers\RappelController::class, 'logs'])->name('comptable.rappels.logs');
    Route::post('/comptable/rappels/declencher', [\App\Http\Controllers\RappelController::class, 'declencher'])->name('comptable.rappels.declencher');

    // --- Espace Élève ---
    Route::get('/eleve/dashboard', [\App\Http\Controllers\EleveController::class, 'dashboard'])->name('eleve.dashboard');
    Route::get('/eleve/notes', [\App\Http\Controllers\EleveController::class, 'notes'])->name('eleve.notes');
    Route::get('/eleve/bulletins', [\App\Http\Controllers\EleveController::class, 'bulletins'])->name('eleve.bulletins');
    Route::get('/eleve/finances', [\App\Http\Controllers\EleveController::class, 'finances'])->name('eleve.finances');
    Route::get('/eleve/profil', [\App\Http\Controllers\EleveController::class, 'profil'])->name('eleve.profil');
});
