<?php

namespace Database\Seeders;

use App\Models\Ecole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer une école (obligatoire pour les utilisateurs)
        $ecole = Ecole::create([
            'nom_ecole' => 'Complexe Scolaire EPST',
            'code_national_epst' => 'CD-KN-001-EPST',
            'province_educationnelle' => 'Kinshasa',
            'adresse' => '123 Avenue de l\'Éducation, Kinshasa',
        ]);

        // Créer un directeur
        User::create([
            'name' => 'Directeur',
            'email' => 'directeur@epst.cd',
            'password' => Hash::make('password'),
            'role' => 'directeur',
            'ecole_id' => $ecole->id,
        ]);

        // Créer un enseignant
        User::create([
            'name' => 'Enseignant',
            'email' => 'enseignant@epst.cd',
            'password' => Hash::make('password'),
            'role' => 'enseignant',
            'ecole_id' => $ecole->id,
        ]);

        // Créer un comptable
        User::create([
            'name' => 'Comptable',
            'email' => 'comptable@epst.cd',
            'password' => Hash::make('password'),
            'role' => 'comptable',
            'ecole_id' => $ecole->id,
        ]);
    }
}
