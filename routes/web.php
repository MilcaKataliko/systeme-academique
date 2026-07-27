<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\ProviseurController;
use App\Http\Controllers\PeriodeController;
use App\Http\Controllers\CourController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\FraisController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\CoteController;
use App\Http\Controllers\EleveDashboardController;

// Page d'accueil / Redirection
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes d'authentification publiques
Route::get('/login', [CustomAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [CustomAuthController::class, 'login']);
Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');

// Inscription d'une nouvelle école
Route::get('/enregistrer-ecole', [CustomAuthController::class, 'showSchoolRegister'])->name('school.register.show');
Route::post('/enregistrer-ecole', [CustomAuthController::class, 'registerSchool'])->name('school.register.store');

// Routes protégées par Authentification
Route::middleware(['auth'])->group(function () {

    // ===================== ESPACE DIRECTEUR =====================
    Route::get('/directeur/dashboard', function () {
        return view('directeur.dashboard');
    })->name('directeur.dashboard');

    Route::get('/directeur/utilisateurs', [CustomAuthController::class, 'indexUsers'])->name('directeur.users.index');
    Route::post('/directeur/utilisateurs', [CustomAuthController::class, 'storeUserByDirector'])->name('directeur.users.store');

    // ===================== ESPACE PROVISEUR =====================
    Route::prefix('proviseur')->name('proviseur.')->group(function () {
        Route::get('/dashboard', [ProviseurController::class, 'dashboard'])->name('dashboard');

        // Gestion des Options
        Route::get('/options', [ProviseurController::class, 'indexOptions'])->name('options.index');
        Route::post('/options', [ProviseurController::class, 'storeOption'])->name('options.store');

        // Gestion des Années Scolaires
        Route::get('/annees', [ProviseurController::class, 'indexAnnees'])->name('annees.index');
        Route::post('/annees', [ProviseurController::class, 'storeAnnee'])->name('annees.store');
        Route::delete('/annees/{id}', [ProviseurController::class, 'destroyAnnee'])->name('annees.destroy');

        // Gestion des Classes
        Route::get('/classes', [ProviseurController::class, 'indexClasses'])->name('classes.index');
        Route::post('/classes', [ProviseurController::class, 'storeClasse'])->name('classes.store');
        Route::put('/classes/{id}', [ProviseurController::class, 'updateClasse'])->name('classes.update');
        Route::delete('/classes/{id}', [ProviseurController::class, 'destroyClasse'])->name('classes.destroy');

        // Gestion des Élèves
        Route::get('/eleves', [EleveController::class, 'index'])->name('eleves.index');
        Route::post('/eleves', [EleveController::class, 'store'])->name('eleves.store');
        Route::put('/eleves/{id}', [EleveController::class, 'update'])->name('eleves.update');
        Route::delete('/eleves/{id}', [EleveController::class, 'destroy'])->name('eleves.destroy');

        // Gestion des Périodes
        Route::get('/periodes', [PeriodeController::class, 'index'])->name('periodes.index');
        Route::post('/periodes', [PeriodeController::class, 'store'])->name('periodes.store');
        Route::put('/periodes/{id}', [PeriodeController::class, 'update'])->name('periodes.update');
        Route::delete('/periodes/{id}', [PeriodeController::class, 'destroy'])->name('periodes.destroy');

        // Gestion des Cours
        Route::get('/cours', [CourController::class, 'index'])->name('cours.index');
        Route::post('/cours', [CourController::class, 'store'])->name('cours.store');
        Route::put('/cours/{id}', [CourController::class, 'update'])->name('cours.update');
        Route::delete('/cours/{id}', [CourController::class, 'destroy'])->name('cours.destroy');

        // Gestion des Inscriptions
        Route::get('/inscriptions', [InscriptionController::class, 'index'])->name('inscriptions.index');
        Route::post('/inscriptions', [InscriptionController::class, 'store'])->name('inscriptions.store');
        Route::put('/inscriptions/{id}', [InscriptionController::class, 'update'])->name('inscriptions.update');
        Route::delete('/inscriptions/{id}', [InscriptionController::class, 'destroy'])->name('inscriptions.destroy');

        // Gestion des Frais
        Route::get('/frais', [FraisController::class, 'index'])->name('frais.index');
        Route::post('/frais', [FraisController::class, 'store'])->name('frais.store');
        Route::put('/frais/{id}', [FraisController::class, 'update'])->name('frais.update');
        Route::delete('/frais/{id}', [FraisController::class, 'destroy'])->name('frais.destroy');
        // Association Frais-Classe
        Route::post('/frais-classe', [FraisController::class, 'storeFraisClasse'])->name('frais.frais-classe.store');
        Route::delete('/frais-classe/{id}', [FraisController::class, 'destroyFraisClasse'])->name('frais.frais-classe.destroy');

        // Gestion des Plans (Cours par classe)
        Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
        Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
        Route::put('/plans/{id}', [PlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{id}', [PlanController::class, 'destroy'])->name('plans.destroy');
    });

    // ===================== ESPACE COMPTABLE =====================
    Route::prefix('comptable')->name('comptable.')->group(function () {
        Route::get('/dashboard', function () {
            return view('comptable.dashboard');
        })->name('dashboard');

        Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements.index');
        Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');
        Route::put('/paiements/{id}', [PaiementController::class, 'update'])->name('paiements.update');
        Route::delete('/paiements/{id}', [PaiementController::class, 'destroy'])->name('paiements.destroy');
        Route::get('/paiements/{id}/recu', [PaiementController::class, 'recu'])->name('paiements.recu');
    });

    // ===================== ESPACE ENSEIGNANT =====================
    Route::prefix('enseignant')->name('enseignant.')->group(function () {
        Route::get('/dashboard', function () {
            return view('enseignant.dashboard');
        })->name('dashboard');

        Route::get('/cotes', [CoteController::class, 'index'])->name('cotes.index');
        Route::post('/cotes/store-update', [CoteController::class, 'storeOrUpdate'])->name('cotes.store-update');
        Route::post('/cotes/store-multiple', [CoteController::class, 'storeMultiple'])->name('cotes.store-multiple');
    });

    // ===================== ESPACE ÉLÈVE =====================
    Route::prefix('eleve')->name('eleve.')->group(function () {
        Route::get('/dashboard', [EleveDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/finances', [EleveDashboardController::class, 'finances'])->name('finances');
        Route::get('/bulletin', [EleveDashboardController::class, 'bulletin'])->name('bulletin');
    });

});
