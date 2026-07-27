<?php

namespace App\Services;

use App\Models\Ecole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolRegistrationService
{
    /**
     * Enregistre une nouvelle école et son utilisateur principal (Proviseur / Directeur)
     */
    public function registerSchoolWithAdmin(array $schoolData, array $userData): User
    {
        return DB::transaction(function () use ($schoolData, $userData) {
            // 1. Création de l'établissement
            $ecole = Ecole::create([
                'nom'     => $schoolData['nom'],
                'adresse' => $schoolData['adresse'] ?? null,
            ]);

            // 2. Création de l'utilisateur directement rattaché à l'école
            $user = User::create([
                'ecole_id' => $ecole->id, // 👈 Clé étrangère garantie dès le départ
                'name'     => $userData['name'],
                'email'    => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role'     => $userData['role'] ?? 'proviseur',
            ]);

            return $user;
        });
    }
}