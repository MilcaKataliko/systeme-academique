<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ecole;
use App\Models\Enseignant;
use App\Models\Option;
use App\Models\Classe;
use App\Models\Cours;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Frais;
use App\Models\Cote;
use App\Models\Plan;
use App\Models\Periode;
use App\Services\BulletinService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
     * Afficher le tableau de bord du directeur avec des statistiques exhaustives.
     */
    public function directeurDashboard()
    {
        $ecoleId = session('ecole_id') ?? Auth::user()->ecole_id;

        // 1. Statistiques Globales Générales
        $stats = [
            'options'      => Option::where('ecole_id', $ecoleId)->count(),
            'classes'      => Classe::where(function($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))->orWhereNull('option_id');
            })->count(),
            'cours'        => Cours::where('ecole_id', $ecoleId)->count(),
            'enseignants'  => Enseignant::where('ecole_id', $ecoleId)->count(),
            'eleves'       => Eleve::where('ecole_id', $ecoleId)->count(),
            'utilisateurs' => User::where('ecole_id', $ecoleId)->count(),
        ];

        // 2. Démographie & Inscriptions
        $totalEleves = $stats['eleves'];
        $totalGarcons = Eleve::where('ecole_id', $ecoleId)->where('genre', 'M')->count();
        $totalFilles = Eleve::where('ecole_id', $ecoleId)->where('genre', 'F')->count();
        $pctGarcons = $totalEleves > 0 ? round(($totalGarcons / $totalEleves) * 100, 1) : 0;
        $pctFilles = $totalEleves > 0 ? round(($totalFilles / $totalEleves) * 100, 1) : 0;

        $inscriptionsAujourdhui = Inscription::where('ecole_id', $ecoleId)->whereDate('created_at', Carbon::today())->count();
        $inscriptionsCeMois = Inscription::where('ecole_id', $ecoleId)->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();

        // Évolution des inscriptions par mois (12 derniers mois)
        $inscriptionsParMoisLabels = [];
        $inscriptionsParMoisData = [];
        for ($i = 11; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $inscriptionsParMoisLabels[] = $dt->locale('fr')->isoFormat('MMM YYYY');
            $inscriptionsParMoisData[] = Inscription::where('ecole_id', $ecoleId)
                ->whereYear('created_at', $dt->year)
                ->whereMonth('created_at', $dt->month)
                ->count();
        }

        // Évolution des inscriptions par année scolaire
        $inscriptionsAnnees = Inscription::where('ecole_id', $ecoleId)
            ->select('annee_scolaire', DB::raw('count(*) as total'))
            ->groupBy('annee_scolaire')
            ->orderBy('annee_scolaire')
            ->get();
        $inscriptionsAnneesLabels = $inscriptionsAnnees->pluck('annee_scolaire')->toArray();
        $inscriptionsAnneesData = $inscriptionsAnnees->pluck('total')->toArray();

        // Répartition des élèves par option
        $options = Option::where('ecole_id', $ecoleId)->with(['classes.inscriptions.eleve'])->get();
        $repartitionOptions = [];
        foreach ($options as $opt) {
            $optElevesCount = 0;
            $optFilles = 0;
            $optGarcons = 0;
            foreach ($opt->classes as $cls) {
                foreach ($cls->inscriptions as $insc) {
                    if ($insc->statut === 'actif' && $insc->eleve) {
                        $optElevesCount++;
                        if ($insc->eleve->genre === 'F') $optFilles++;
                        else $optGarcons++;
                    }
                }
            }
            $repartitionOptions[] = [
                'id' => $opt->id,
                'nom' => $opt->nomoption,
                'code' => $opt->sigle,
                'total' => $optElevesCount,
                'filles' => $optFilles,
                'garcons' => $optGarcons,
                'pct' => $totalEleves > 0 ? round(($optElevesCount / $totalEleves) * 100, 1) : 0,
            ];
        }

        // Répartition des élèves par classe
        $classes = Classe::where(function($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))
                  ->orWhereNull('option_id');
            })
            ->with(['option', 'inscriptions.eleve'])
            ->orderBy('niveau')->orderBy('nom_classe')
            ->get();

        $repartitionClasses = [];
        $classesLabels = [];
        $classesEffectifs = [];
        $classesFilles = [];
        $classesGarcons = [];

        foreach ($classes as $cls) {
            $clsTotal = 0;
            $clsF = 0;
            $clsG = 0;
            foreach ($cls->inscriptions as $insc) {
                if ($insc->statut === 'actif' && $insc->eleve) {
                    $clsTotal++;
                    if ($insc->eleve->genre === 'F') $clsF++;
                    else $clsG++;
                }
            }
            $repartitionClasses[] = [
                'id' => $cls->id,
                'nom' => $cls->nom_classe,
                'niveau' => $cls->niveau,
                'option' => $cls->option?->nom_option ?? 'Générale',
                'total' => $clsTotal,
                'filles' => $clsF,
                'garcons' => $clsG,
            ];
            $classesLabels[] = $cls->nom_classe;
            $classesEffectifs[] = $clsTotal;
            $classesFilles[] = $clsF;
            $classesGarcons[] = $clsG;
        }

        // 3. Résultats Académiques & Pédagogie
        $inscriptions = Inscription::where('ecole_id', $ecoleId)
            ->where('statut', 'actif')
            ->with(['eleve', 'classe.option', 'cotes.plan.cours'])
            ->get();

        $elevesAverages = [];
        $classeScores = [];
        $optionScores = [];
        $coursScores = [];
        $periodesScores = [
            'periode_1' => ['total' => 0, 'max' => 0],
            'periode_2' => ['total' => 0, 'max' => 0],
            'examen_s1' => ['total' => 0, 'max' => 0],
            'periode_3' => ['total' => 0, 'max' => 0],
            'periode_4' => ['total' => 0, 'max' => 0],
            'examen_s2' => ['total' => 0, 'max' => 0],
        ];

        foreach ($inscriptions as $insc) {
            $cotes = $insc->cotes;
            if ($cotes->isEmpty()) continue;

            $res = BulletinService::moyenneGenerale($cotes);
            $moyenne = $res['moyenne'];

            if ($moyenne !== null) {
                $eleveData = [
                    'id' => $insc->eleve->id,
                    'nom' => $insc->eleve->nom,
                    'postnom' => $insc->eleve->postnom ?? '',
                    'prenom' => $insc->eleve->prenom ?? '',
                    'genre' => $insc->eleve->genre,
                    'matricule' => $insc->eleve->code_matricule,
                    'classe' => $insc->classe->nom_classe ?? '—',
                    'option' => $insc->classe->option?->nom_option ?? 'Générale',
                    'moyenne' => $moyenne,
                    'coef_total' => $res['total_coefficients'],
                ];
                $elevesAverages[] = $eleveData;

                // Par classe
                $cId = $insc->classe_id;
                $cNom = $insc->classe->nom_classe ?? "Classe {$cId}";
                if (!isset($classeScores[$cId])) {
                    $classeScores[$cId] = ['nom' => $cNom, 'somme' => 0, 'count' => 0, 'reussis' => 0];
                }
                $classeScores[$cId]['somme'] += $moyenne;
                $classeScores[$cId]['count']++;
                if ($moyenne >= 10) $classeScores[$cId]['reussis']++;

                // Par option
                $optId = $insc->classe->option_id ?? 0;
                $optNom = $insc->classe->option?->nom_option ?? 'Générale / Troncs Communs';
                if (!isset($optionScores[$optId])) {
                    $optionScores[$optId] = ['nom' => $optNom, 'somme' => 0, 'count' => 0, 'reussis' => 0];
                }
                $optionScores[$optId]['somme'] += $moyenne;
                $optionScores[$optId]['count']++;
                if ($moyenne >= 10) $optionScores[$optId]['reussis']++;
            }

            // Par matière & Par période
            foreach ($cotes as $cote) {
                $plan = $cote->plan;
                if (!$plan) continue;
                $coursId = $plan->cours_id;
                $coursNom = $plan->cours->nom_cours ?? "Cours {$coursId}";
                $moyMatiere = BulletinService::moyenneMatiere($cote, $plan);
                if ($moyMatiere !== null) {
                    if (!isset($coursScores[$coursId])) {
                        $coursScores[$coursId] = ['nom' => $coursNom, 'somme' => 0, 'count' => 0];
                    }
                    $coursScores[$coursId]['somme'] += $moyMatiere;
                    $coursScores[$coursId]['count']++;
                }

                // Périodes
                foreach (['periode_1', 'periode_2', 'examen_s1', 'periode_3', 'periode_4', 'examen_s2'] as $pKey) {
                    if ($cote->{$pKey} !== null) {
                        $maxP = BulletinService::maximumPourChamp($plan, $pKey);
                        if ($maxP > 0) {
                            $periodesScores[$pKey]['total'] += (float) $cote->{$pKey};
                            $periodesScores[$pKey]['max'] += $maxP;
                        }
                    }
                }
            }
        }

        $totalEvalues = count($elevesAverages);
        $sommeMoyennes = array_sum(array_column($elevesAverages, 'moyenne'));
        $moyenneGeneraleEcole = $totalEvalues > 0 ? round($sommeMoyennes / $totalEvalues, 2) : 0;

        $nombreReussis = count(array_filter($elevesAverages, fn($e) => $e['moyenne'] >= 10));
        $nombreDifficulte = count(array_filter($elevesAverages, fn($e) => $e['moyenne'] < 10));
        $tauxReussite = $totalEvalues > 0 ? round(($nombreReussis / $totalEvalues) * 100, 1) : 0;
        $tauxEchec = $totalEvalues > 0 ? round(($nombreDifficulte / $totalEvalues) * 100, 1) : 0;

        // Tri pour palmarès & soutien
        usort($elevesAverages, fn($a, $b) => $b['moyenne'] <=> $a['moyenne']);
        $meilleuresMoyennes = array_slice($elevesAverages, 0, 8);
        $moyennesPlusFaibles = array_slice(array_reverse($elevesAverages), 0, 8);

        // Formater résultats par classe
        $resultatsParClasse = [];
        foreach ($classeScores as $c) {
            $moyCls = $c['count'] > 0 ? round($c['somme'] / $c['count'], 2) : 0;
            $tauxCls = $c['count'] > 0 ? round(($c['reussis'] / $c['count']) * 100, 1) : 0;
            $resultatsParClasse[] = [
                'nom' => $c['nom'],
                'moyenne' => $moyCls,
                'taux_reussite' => $tauxCls,
                'effectif' => $c['count'],
            ];
        }

        // Formater résultats par option
        $resultatsParOption = [];
        foreach ($optionScores as $o) {
            $moyOpt = $o['count'] > 0 ? round($o['somme'] / $o['count'], 2) : 0;
            $tauxOpt = $o['count'] > 0 ? round(($o['reussis'] / $o['count']) * 100, 1) : 0;
            $resultatsParOption[] = [
                'nom' => $o['nom'],
                'moyenne' => $moyOpt,
                'taux_reussite' => $tauxOpt,
                'effectif' => $o['count'],
            ];
        }

        // Formater résultats par matière
        $resultatsParMatiere = [];
        foreach ($coursScores as $crs) {
            $moyCrs = $crs['count'] > 0 ? round($crs['somme'] / $crs['count'], 2) : 0;
            $resultatsParMatiere[] = [
                'nom' => $crs['nom'],
                'moyenne' => $moyCrs,
                'evalues' => $crs['count'],
            ];
        }
        usort($resultatsParMatiere, fn($a, $b) => $b['moyenne'] <=> $a['moyenne']);

        // Formater évolution par période (normalisé sur 20)
        $evolutionPeriodesLabels = ['1ère Période', '2ème Période', 'Examen S1', '3ème Période', '4ème Période', 'Examen S2'];
        $evolutionPeriodesData = [];
        $keys = ['periode_1', 'periode_2', 'examen_s1', 'periode_3', 'periode_4', 'examen_s2'];
        foreach ($keys as $k) {
            $item = $periodesScores[$k];
            $val = $item['max'] > 0 ? round(($item['total'] / $item['max']) * 20, 2) : null;
            $evolutionPeriodesData[] = $val;
        }

        // 4. Finances & Recouvrement
        $fraisList = Frais::where('ecole_id', $ecoleId)->get();
        $totalFacture = 0;
        $activeInscriptions = Inscription::where('ecole_id', $ecoleId)->where('statut', 'actif')->with('paiements')->get();
        $elevesEnRetard = 0;

        foreach ($activeInscriptions as $insc) {
            $fraisApplicables = $fraisList->filter(function($f) use ($insc) {
                $matchClasse = is_null($f->classe_id) || $f->classe_id == $insc->classe_id;
                $matchAnnee = is_null($f->annee_scolaire) || $f->annee_scolaire == $insc->annee_scolaire;
                return $matchClasse && $matchAnnee;
            });
            $montantEleveDu = $fraisApplicables->sum('montant');
            $totalFacture += $montantEleveDu;

            $montantElevePaye = $insc->paiements->sum('montant_paye');
            if ($montantEleveDu > $montantElevePaye) {
                $elevesEnRetard++;
            }
        }

        $totalPaye = (float) Paiement::where('ecole_id', $ecoleId)->sum('montant_paye');
        $totalRestant = max(0, $totalFacture - $totalPaye);
        $tauxRecouvrement = $totalFacture > 0 ? round(($totalPaye / $totalFacture) * 100, 1) : 0;
        $nombrePaiements = Paiement::where('ecole_id', $ecoleId)->count();

        // Évolution des paiements par mois (6 derniers mois)
        $evolutionPaiementsLabels = [];
        $evolutionPaiementsData = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $evolutionPaiementsLabels[] = $dt->locale('fr')->isoFormat('MMM YYYY');
            $evolutionPaiementsData[] = (float) Paiement::where('ecole_id', $ecoleId)
                ->whereYear('date_paiement', $dt->year)
                ->whereMonth('date_paiement', $dt->month)
                ->sum('montant_paye');
        }

        $paiementsRecents = Paiement::where('ecole_id', $ecoleId)
            ->with(['inscription.eleve', 'frais'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('directeur.dashboard', compact(
            'stats',
            'totalEleves', 'totalGarcons', 'totalFilles', 'pctGarcons', 'pctFilles',
            'inscriptionsAujourdhui', 'inscriptionsCeMois',
            'inscriptionsParMoisLabels', 'inscriptionsParMoisData',
            'inscriptionsAnneesLabels', 'inscriptionsAnneesData',
            'repartitionOptions', 'repartitionClasses',
            'classesLabels', 'classesEffectifs', 'classesFilles', 'classesGarcons',
            'totalEvalues', 'moyenneGeneraleEcole',
            'nombreReussis', 'nombreDifficulte', 'tauxReussite', 'tauxEchec',
            'meilleuresMoyennes', 'moyennesPlusFaibles',
            'resultatsParClasse', 'resultatsParOption', 'resultatsParMatiere',
            'evolutionPeriodesLabels', 'evolutionPeriodesData',
            'totalFacture', 'totalPaye', 'totalRestant', 'tauxRecouvrement',
            'nombrePaiements', 'elevesEnRetard',
            'evolutionPaiementsLabels', 'evolutionPaiementsData', 'paiementsRecents'
        ));
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
     * Afficher le formulaire de création de compte pour le personnel (Espace Directeur)
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Traiter la création d'un compte personnel (Enseignants, Comptables, Élèves)
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:enseignant,comptable,eleve'],
        ]);

        // On récupère l'ID de l'école du directeur connecté depuis sa session
        $ecoleId = session('ecole_id');

        $user = User::create([
            'ecole_id' => $ecoleId,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Si le rôle est 'enseignant', créer automatiquement la fiche Enseignant
        if ($request->role === 'enseignant') {
            $nameParts = explode(' ', $request->name, 2);
            $nom = $nameParts[0] ?? $request->name;
            $postnom = $nameParts[1] ?? '';

            Enseignant::create([
                'ecole_id' => $ecoleId,
                'user_id' => $user->id,
                'matricule' => $this->genererMatricule($ecoleId),
                'nom' => $nom,
                'postnom' => $postnom,
                'prenom' => '',
                'telephone' => '',
                'grade' => 'Titulaire', // Grade par défaut modifiable
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Le compte a été créé avec succès !');
    }

    /**
     * Générer un matricule unique pour un enseignant.
     * Format: ENS-AAAA-NNNN (ex: ENS-2026-0001)
     */
    private function genererMatricule($ecoleId): string
    {
        $annee = date('Y');
        $lastEnseignant = Enseignant::where('ecole_id', $ecoleId)
            ->where('matricule', 'like', "ENS-{$annee}-%")
            ->orderBy('matricule', 'desc')
            ->first();

        if ($lastEnseignant) {
            $lastNum = (int) substr($lastEnseignant->matricule, -4);
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }

        return "ENS-{$annee}-{$newNum}";
    }

    /**
     * Afficher la liste des utilisateurs de l'établissement (Espace Directeur)
     */
    public function usersIndex()
    {
        $ecoleId = session('ecole_id');
        $users = User::where('ecole_id', $ecoleId)
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('users.index', compact('users'));
    }

    /**
     * Afficher le formulaire de modification d'un utilisateur
     */
    public function usersEdit($id)
    {
        $ecoleId = session('ecole_id');
        $user = User::where('ecole_id', $ecoleId)->findOrFail($id);

        // Si c'est un enseignant, récupérer sa fiche professionnelle
        $enseignant = null;
        if ($user->role === 'enseignant') {
            $enseignant = Enseignant::where('user_id', $user->id)
                ->where('ecole_id', $ecoleId)
                ->first();
        }

        return view('users.edit', compact('user', 'enseignant'));
    }

    /**
     * Traiter la modification d'un utilisateur
     */
    public function usersUpdate(Request $request, $id)
    {
        $ecoleId = session('ecole_id');
        $user = User::where('ecole_id', $ecoleId)->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'role' => ['required', 'string', 'in:enseignant,comptable,eleve'],
'password' => ['nullable', 'string', 'min:6'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            // Champs enseignant
            'enseignant_nom' => ['nullable', 'string', 'max:255'],
            'enseignant_postnom' => ['nullable', 'string', 'max:255'],
            'enseignant_prenom' => ['nullable', 'string', 'max:255'],
            'enseignant_telephone' => ['nullable', 'string', 'max:20'],
            'enseignant_grade' => ['nullable', 'string', 'max:100'],
        ]);

$user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Gérer le téléversement de la photo de profil
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($user->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists('photos/' . $user->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete('photos/' . $user->photo);
            }

            $filename = 'user_' . $user->id . '_' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->storeAs('photos', $filename, 'public');
            $user->photo = $filename;
        }

        $user->save();

        // Mettre à jour la fiche Enseignant si le rôle est enseignant
        if ($request->role === 'enseignant') {
            $enseignant = Enseignant::where('user_id', $user->id)
                ->where('ecole_id', $ecoleId)
                ->first();

            if ($enseignant) {
                $enseignant->update([
                    'nom' => $request->enseignant_nom ?? $enseignant->nom,
                    'postnom' => $request->enseignant_postnom ?? $enseignant->postnom,
                    'prenom' => $request->enseignant_prenom ?? $enseignant->prenom,
                    'telephone' => $request->enseignant_telephone ?? $enseignant->telephone,
                    'grade' => $request->enseignant_grade ?? $enseignant->grade,
                ]);
            } else {
                // Si la fiche n'existe pas encore (ancien compte), la créer
                $nameParts = explode(' ', $request->name, 2);
                $nom = $nameParts[0] ?? $request->name;
                $postnom = $nameParts[1] ?? '';

                Enseignant::create([
                    'ecole_id' => $ecoleId,
                    'user_id' => $user->id,
                    'matricule' => $this->genererMatricule($ecoleId),
                    'nom' => $request->enseignant_nom ?? $nom,
                    'postnom' => $request->enseignant_postnom ?? $postnom,
                    'prenom' => $request->enseignant_prenom ?? '',
                    'telephone' => $request->enseignant_telephone ?? '',
                    'grade' => $request->enseignant_grade ?? 'Titulaire',
                ]);
            }
        }

        return redirect()->route('users.index')->with('success', 'Le compte a été modifié avec succès !');
    }

    /**
     * Supprimer un utilisateur
     */
    public function usersDestroy($id)
    {
        $ecoleId = session('ecole_id');
        $user = User::where('ecole_id', $ecoleId)->findOrFail($id);

        // Empêcher la suppression de son propre compte
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Le compte a été supprimé avec succès !');
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur (mot de passe par défaut: "password123")
     */
    public function usersResetPassword($id)
    {
        $ecoleId = session('ecole_id');
        $user = User::where('ecole_id', $ecoleId)->findOrFail($id);

        $defaultPassword = 'password123';
        $user->password = Hash::make($defaultPassword);
        $user->save();

        return redirect()->route('users.index')->with(
            'success',
            "Le mot de passe de {$user->name} a été réinitialisé avec succès. Nouveau mot de passe: <strong>{$defaultPassword}</strong>"
        );
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
            'nom_ecole' => ['required', 'string', 'max:255'],
            'code_national_epst' => ['required', 'string', 'unique:ecoles'],
            'province_educationnelle' => ['required', 'string'],
            'adresse' => ['required', 'string'],
            // Validation du directeur
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // 1. Création de l'école en base de données
        $ecole = Ecole::create([
            'nom_ecole' => $request->nom_ecole,
            'code_national_epst' => $request->code_national_epst,
            'province_educationnelle' => $request->province_educationnelle,
            'adresse' => $request->adresse,
        ]);

        // 2. Création explicite du compte Directeur lié à cette école
        $user = new User();
        $user->ecole_id = $ecole->id;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'directeur'; // Rôle forcé pour éviter l'écrasement par défaut
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
