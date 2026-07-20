<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;

// --- ROUTES PUBLIQUES (Accessibles sans être connecté) ---
Route::get('/', [CustomAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [CustomAuthController::class, 'login']);
Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');

// Les deux routes d'inscription de l'établissement à vérifier :
Route::get('/enregistrer-ecole', [CustomAuthController::class, 'showSchoolRegister'])->name('school.register.show');
Route::post('/enregistrer-ecole', [CustomAuthController::class, 'registerSchool'])->name('school.register.store');


// --- ROUTES SÉCURISÉES (Uniquement pour les utilisateurs connectés) ---
Route::middleware(['auth'])->group(function () {
    // Création de comptes par le Directeur
    Route::get('/directeur/creer-compte', [CustomAuthController::class, 'showRegister'])->name('register.show');
    Route::post('/directeur/creer-compte', [CustomAuthController::class, 'register'])->name('register.store');

    // Tableaux de bord
    Route::get('/directeur/dashboard', function () { return view('directeur.dashboard'); })->name('directeur.dashboard');
    Route::get('/enseignant/dashboard', function () { return "Espace Enseignant"; })->name('enseignant.dashboard');
    Route::get('/comptable/dashboard', function () { return "Espace Comptable"; })->name('comptable.dashboard');
    Route::get('/eleve/dashboard', function () { return "Espace Élève"; })->name('eleve.dashboard');
});