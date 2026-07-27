<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ecole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Traiter la connexion des utilisateurs (Routage par rôle)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Stocker l'ID de l'école en session pour le cloisonnement multi-écoles
            session(['ecole_id' => $user->ecole_id]);

            // Redirection stricte selon le rôle de l'utilisateur
            return match($user->role) {
                'directeur'  => redirect()->route('directeur.dashboard'),
                'proviseur'  => redirect()->route('proviseur.dashboard'),
                'enseignant' => redirect()->route('enseignant.dashboard'),
                'comptable'  => redirect()->route('comptable.dashboard'),
                'eleve'      => redirect()->route('eleve.dashboard'),
                default      => redirect('/'),
            };
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Afficher la liste des utilisateurs de l'établissement (Espace Directeur)
     */
    public function indexUsers()
    {
        $ecoleId = session('ecole_id');

        // Récupérer uniquement les utilisateurs rattachés à l'école du directeur
        $users = User::where('ecole_id', $ecoleId)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('directeur.users.index', compact('users'));
    }

    /**
     * Traiter la création d'un compte utilisateur par le Directeur
     */
    public function storeUserByDirector(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role'     => ['required', 'string', 'in:proviseur,enseignant,comptable,eleve'],
        ]);

        $ecoleId = session('ecole_id');

        User::create([
            'ecole_id' => $ecoleId,
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('directeur.users.index')
                         ->with('success', 'Le compte utilisateur a été créé avec succès !');
    }

    /**
     * Afficher le formulaire de création de compte pour le personnel
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Traiter la création d'un compte personnel (Proviseurs, Enseignants, Comptables, Élèves)
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', 'string', 'in:proviseur,enseignant,comptable,eleve'],
        ]);

        // On récupère l'ID de l'école du directeur connecté depuis sa session
        $ecoleId = session('ecole_id');

        User::create([
            'ecole_id' => $ecoleId,
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('directeur.dashboard')->with('success', 'Le compte a été créé avec succès !');
    }

    /**
     * Afficher le formulaire d'enregistrement d'un nouvel établissement scolaire
     */
    public function showSchoolRegister()
    {
        return view('auth.register_school');
    }

    /**
     * Traiter l'enregistrement de l'école et de son Directeur (Initialisation)
     */
    public function registerSchool(Request $request)
    {
        $request->validate([
            // Validation de l'école
            'nom_ecole'               => ['required', 'string', 'max:255'],
            'code_national_epst'     => ['required', 'string', 'unique:ecoles'],
            'province_educationnelle' => ['required', 'string'],
            'adresse'                 => ['required', 'string'],
            // Validation du directeur
            'name'                    => ['required', 'string', 'max:255'],
            'email'                   => ['required', 'string', 'email', 'unique:users'],
            'password'                => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // 1. Création de l'école en base de données
        $ecole = Ecole::create([
            'nom_ecole'               => $request->nom_ecole,
            'code_national_epst'     => $request->code_national_epst,
            'province_educationnelle' => $request->province_educationnelle,
            'adresse'                 => $request->adresse,
        ]);

        // 2. Création explicite du compte Directeur lié à cette école
        $user = new User();
        $user->ecole_id = $ecole->id;
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        $user->role     = 'directeur'; // Rôle forcé pour éviter l'écrasement par défaut
        $user->save();

        // Nettoyer les anciennes redirections stockées en session par le framework
        session()->forget('url.intended');

        // Connecter manuellement le nouveau directeur
        Auth::login($user);
        
        // Stocker l'école en session pour le cloisonnement immédiat des requêtes
        session(['ecole_id' => $ecole->id]);

        // Redirection forcée et directe vers le tableau de bord de direction
        return redirect()->route('directeur.dashboard');
    }
}