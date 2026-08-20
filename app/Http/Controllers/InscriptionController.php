<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Classe;
use App\Models\Plan;
use App\Models\Cote;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\BulletinImportAnomaly;
use App\Models\BulletinValidation;
use App\Services\BulletinService;

class InscriptionController extends Controller
{
    /**
     * Afficher le registre des élèves avec leurs inscriptions.
     */
    public function index(Request $request)
    {
        $ecoleId = session('ecole_id');

        $query = Eleve::where('ecole_id', $ecoleId)
            ->with(['inscriptions' => function ($q) {
                $q->with('classe.option');
            }]);

        // Filtre par classe
        if ($request->filled('classe_id')) {
            $query->whereHas('inscriptions', function ($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }

        // Recherche par matricule ou nom
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('postnom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('code_matricule', 'like', "%{$search}%");
            });
        }

        $eleves = $query->orderBy('nom')->orderBy('postnom')->paginate(20)->withQueryString();
        $classes = Classe::where(function ($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))
                  ->orWhereNull('option_id');
            })
            ->with('option')
            ->orderBy('niveau')
            ->orderBy('nom_classe')
            ->get();
        $stats = (object) [
            'total_eleves' => Eleve::where('ecole_id', $ecoleId)->count(),
            'total_inscriptions' => Inscription::where('ecole_id', $ecoleId)->count(),
            'classes_actives' => Inscription::where('ecole_id', $ecoleId)
                ->distinct('classe_id')->count('classe_id'),
        ];

        return view('directeur.eleves.index', compact('eleves', 'classes', 'stats'));
    }

    /**
     * Afficher le formulaire d'inscription d'un nouvel élève.
     */
    public function create()
    {
        $ecoleId = session('ecole_id');
        $classes = Classe::where(function ($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))
                  ->orWhereNull('option_id');
            })
            ->with('option')
            ->orderBy('niveau')->orderBy('nom_classe')
            ->get();

        return view('directeur.eleves.create', compact('classes'));
    }

    /**
     * Enregistrer un nouvel élève et l'inscrire dans une classe.
     */
    public function store(Request $request)
    {
        $ecoleId = session('ecole_id');

        $request->validate([
            'nom' => 'required|string|max:100',
            'postnom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'genre' => 'required|in:M,F',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:150',
            'classe_id' => 'required|exists:classes,id',
            'annee_scolaire' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'nullable|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            // Générer matricule unique
            $annee = date('Y');
            $count = Eleve::where('ecole_id', $ecoleId)->count() + 1;
            $matricule = "MT-{$annee}-{$ecoleId}-" . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Vérifier unicité du matricule
            while (Eleve::where('code_matricule', $matricule)->exists()) {
                $count++;
                $matricule = "MT-{$annee}-{$ecoleId}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            $user = null;
            // Créer un compte utilisateur si email fourni
            if ($request->filled('email')) {
                $user = User::create([
                    'name' => $request->nom . ' ' . $request->postnom,
                    'email' => $request->email,
                    'password' => Hash::make($request->password ?? 'eleve123'),
                    'role' => 'eleve',
                    'ecole_id' => $ecoleId,
                ]);
            }

            // Créer l'élève
            $eleve = Eleve::create([
                'ecole_id' => $ecoleId,
                'user_id' => $user?->id,
                'nom' => $request->nom,
                'postnom' => $request->postnom,
                'prenom' => $request->prenom,
                'genre' => $request->genre,
                'date_naissance' => $request->date_naissance,
                'lieu_naissance' => $request->lieu_naissance,
                'code_matricule' => $matricule,
            ]);

            // Créer l'inscription
            Inscription::create([
                'ecole_id' => $ecoleId,
                'eleve_id' => $eleve->id,
                'classe_id' => $request->classe_id,
                'annee_scolaire' => $request->annee_scolaire,
                'statut' => 'actif',
            ]);

            DB::commit();

            return redirect()
                ->route('directeur.eleves.index')
                ->with('success', "Élève {$eleve->nom} {$eleve->postnom} inscrit avec succès ! Matricule : {$matricule}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'inscription : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un élève (fiche individuelle avec ses inscriptions et cotes).
     */
    public function show($id)
    {
        $ecoleId = session('ecole_id');
        $eleve = Eleve::where('ecole_id', $ecoleId)
            ->with(['inscriptions' => function ($q) {
                $q->with(['classe.option', 'cotes' => function ($cq) {
                    $cq->with(['plan.cours', 'periode']);
                }]);
            }])
            ->findOrFail($id);

        $periodes = Periode::where('ecole_id', $ecoleId)->orderBy('nom_periode')->get();

        return view('directeur.eleves.show', compact('eleve', 'periodes'));
    }

    /**
     * Bulletin de notes pour un élève (année courante).
     */
    public function bulletin($inscriptionId)
    {
        $ecoleId = session('ecole_id');
        $inscription = Inscription::where('ecole_id', $ecoleId)
            ->with(['ecole', 'eleve', 'classe.option'])
            ->findOrFail($inscriptionId);

        $periodes = Periode::where('ecole_id', $ecoleId)->orderBy('nom_periode')->get();
        $plans = Plan::where('classe_id', $inscription->classe_id)
            ->where('annee_scolaire', $inscription->annee_scolaire)
            ->with('cours')
            ->get();

        // Les cotes récentes sont une ligne par matière; le service conserve la
        // compatibilité avec les anciennes notes enregistrées par période.
        $cotes = Cote::where('inscription_id', $inscriptionId)
            ->with(['periode', 'plan.cours'])
            ->get()
            ->groupBy(function ($c) {
                return $c->plan->cours_id . '_' . $c->periode_id;
            });

        $cotesBulletin = Cote::where('inscription_id', $inscriptionId)->with('plan.cours')->get();
        $resultatsParPlan = $cotesBulletin->keyBy('plan_id')->map(fn ($cote) => BulletinService::moyenneMatiere($cote, $cote->plan));
        $libellesNotes = [
            'interrogation_s1' => 'Int. S1', 'devoir_domicile_s1' => 'Dév. S1', 'periode_1' => 'P1', 'periode_2' => 'P2',
            'periode_3' => 'P3', 'examen_s1' => 'Ex. S1', 'interrogation_s2' => 'Int. S2', 'devoir_domicile_s2' => 'Dév. S2',
            'periode_4' => 'P4', 'periode_5' => 'P5', 'periode_6' => 'P6', 'examen_s2' => 'Ex. S2',
        ];
        $detailsNotesParPlan = $cotesBulletin->keyBy('plan_id')->map(function ($cote) use ($libellesNotes) {
            $notes = collect($libellesNotes)->map(function ($libelle, $champ) use ($cote) {
                if ($cote->{$champ} === null) return null;
                return ['libelle' => $libelle, 'note' => $cote->{$champ}, 'maximum' => BulletinService::maximumPourChamp($cote->plan, $champ)];
            })->filter()->values();
            if ($notes->isEmpty() && $cote->points_obtenus !== null) {
                $notes->push(['libelle' => 'Note historique', 'note' => $cote->points_obtenus, 'maximum' => $cote->plan->maxima_periode]);
            }
            return $notes;
        });
        $resumeBulletin = BulletinService::moyenneGenerale($cotesBulletin);
        $plansIds = $plans->pluck('id');
        $nbMatieresValidees = BulletinValidation::where('inscription_id', $inscription->id)
            ->whereIn('plan_id', $plansIds)->where('statut', BulletinValidation::VALIDE)->count();
        $bulletinDefinitif = $plansIds->isNotEmpty() && $nbMatieresValidees === $plansIds->count();
        $statutBulletin = $bulletinDefinitif ? 'definitif' : 'en_attente';
        return view('directeur.eleves.bulletin', compact('inscription', 'periodes', 'plans', 'cotes', 'resultatsParPlan', 'detailsNotesParPlan', 'resumeBulletin', 'bulletinDefinitif', 'statutBulletin', 'nbMatieresValidees'));
    }

    public function validationImports()
    {
        $anomalies = BulletinImportAnomaly::where('ecole_id', session('ecole_id'))->latest()->get();
        return view('directeur.eleves.validation_imports', compact('anomalies'));
    }

    public function corrigerImport(Request $request, BulletinImportAnomaly $anomalie)
    {
        abort_unless($anomalie->ecole_id == session('ecole_id'), 403);
        $request->validate(['note' => 'required|numeric|min:0|max:999']);
        $inscription = Inscription::where('ecole_id', $anomalie->ecole_id)
            ->whereHas('eleve', fn ($q) => $q->where('code_matricule', $anomalie->matricule))->first();
        $plan = $inscription ? Plan::where('classe_id', $inscription->classe_id)
            ->whereHas('cours', fn ($q) => $q->where('code_cours', $anomalie->code_cours))->first() : null;
        if (! $inscription || ! $plan || ! in_array($anomalie->champ, BulletinService::CHAMPS, true)) {
            return back()->with('error', 'Cette anomalie ne peut pas être corrigée automatiquement : matricule, cours ou champ invalide.');
        }
        $max = BulletinService::maximumPourChamp($plan, $anomalie->champ);
        if ((float) $request->note > $max) return back()->withErrors(['note' => "La note ne peut pas dépasser {$max}."]);
        Cote::updateOrCreate(['inscription_id' => $inscription->id, 'plan_id' => $plan->id], ['encode_par' => auth()->id(), $anomalie->champ => $request->note]);
        BulletinValidation::updateOrCreate(
            ['inscription_id' => $inscription->id, 'plan_id' => $plan->id],
            ['statut' => BulletinValidation::EN_ATTENTE, 'valide_par' => null, 'valide_le' => null]
        );
        $anomalie->delete();
        return back()->with('success', 'Anomalie corrigée et note intégrée au bulletin.');
    }

/**
     * Gestion des cotes par classe et par cours (pour le directeur).
     * Affiche les élèves d'une classe pour un cours spécifique.
     */
    public function cotesParClasse(Request $request, $classeId, $planId = null)
    {
        $ecoleId = session('ecole_id');

        $classe = Classe::where(function ($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))
                  ->orWhereNull('option_id');
            })
            ->with('option')
            ->findOrFail($classeId);

        $plans = Plan::where('classe_id', $classeId)
            ->with('cours')
            ->get();

        if ($plans->isEmpty()) {
            return redirect()->route('directeur.classes.index')
                ->with('error', 'Aucun cours attribué à cette classe. Veuillez d\'abord attribuer des cours.');
        }

        // Si aucun planId spécifié, prendre le premier
        if (!$planId || !$plans->contains('id', $planId)) {
            $planId = $plans->first()->id;
        }

        $plan = $plans->firstWhere('id', $planId);

        $inscriptions = Inscription::where('classe_id', $classeId)
            ->where('statut', 'actif')
            ->with(['eleve', 'cotes' => function ($q) use ($planId) {
                $q->where('plan_id', $planId)->with('plan.cours');
            }])
            ->orderBy('created_at')
            ->get();

        return view('directeur.eleves.cotes_classe', compact(
            'classe', 'plans', 'plan', 'inscriptions'
        ));
    }

    /**
     * Mise à jour d'une évaluation spécifique pour un élève.
     */
    public function mettreAJourCote(Request $request)
    {
        $ecoleId = session('ecole_id');

        $request->validate([
            'cote_id' => 'nullable|exists:cotes,id',
            'inscription_id' => 'required|exists:inscriptions,id',
            'plan_id' => 'required|exists:plans,id',
            'champ' => 'required|string|in:interrogation_s1,devoir_domicile_s1,periode_1,periode_2,periode_3,examen_s1,interrogation_s2,devoir_domicile_s2,periode_4,periode_5,periode_6,examen_s2',
            'valeur' => 'nullable|numeric|min:0|max:999',
        ]);

        // Vérifier que l'inscription est dans l'école
        $inscription = Inscription::where('ecole_id', $ecoleId)
            ->findOrFail($request->inscription_id);

        // Vérifier que le plan est bien dans une classe de l'école
        $plan = Plan::with('classe.option')->findOrFail($request->plan_id);
        if ($plan->classe->option && $plan->classe->option->ecole_id != $ecoleId) {
            abort(403);
        }
        abort_unless($plan->classe_id == $inscription->classe_id, 403);

        $cote = Cote::updateOrCreate(
            [
                'inscription_id' => $request->inscription_id,
                'plan_id' => $request->plan_id,
            ],
            [
                'encode_par' => auth()->id(),
            ]
        );

        // Mettre à jour le champ spécifique
        $cote->{$request->champ} = $request->valeur;
        $cote->save();
        BulletinValidation::updateOrCreate(
            ['inscription_id' => $inscription->id, 'plan_id' => $plan->id],
            ['statut' => BulletinValidation::EN_ATTENTE, 'valide_par' => null, 'valide_le' => null]
        );

        return back()->with('success', 'Note mise à jour avec succès !');
    }

    /**
     * Modifier les informations d'un élève.
     */
    public function edit($id)
    {
        $ecoleId = session('ecole_id');
        $eleve = Eleve::where('ecole_id', $ecoleId)->findOrFail($id);
        $classes = Classe::where(function ($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))
                  ->orWhereNull('option_id');
            })
            ->with('option')
            ->orderBy('niveau')->orderBy('nom_classe')
            ->get();

        return view('directeur.eleves.edit', compact('eleve', 'classes'));
    }

    /**
     * Mettre à jour les informations d'un élève.
     */
    public function update(Request $request, $id)
    {
        $ecoleId = session('ecole_id');
        $eleve = Eleve::where('ecole_id', $ecoleId)->findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:100',
            'postnom' => 'required|string|max:100',
            'prenom' => 'nullable|string|max:100',
            'genre' => 'required|in:M,F',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:150',
        ]);

        $eleve->update($request->only(['nom', 'postnom', 'prenom', 'genre', 'date_naissance', 'lieu_naissance']));

        return redirect()
            ->route('directeur.eleves.show', $eleve->id)
            ->with('success', 'Informations mises à jour.');
    }

    /**
     * Supprimer un élève (et ses inscriptions).
     */
    public function destroy($id)
    {
        $ecoleId = session('ecole_id');
        $eleve = Eleve::where('ecole_id', $ecoleId)->findOrFail($id);

        DB::beginTransaction();
        try {
            // Supprimer les cotes via inscriptions
            foreach ($eleve->inscriptions as $inscription) {
                Cote::where('inscription_id', $inscription->id)->delete();
            }
            // Supprimer les inscriptions
            Inscription::where('eleve_id', $eleve->id)->delete();
            // Optionnel : supprimer le compte utilisateur
            if ($eleve->user_id) {
                User::where('id', $eleve->user_id)->delete();
            }
            $eleve->delete();

            DB::commit();
            return redirect()->route('directeur.eleves.index')->with('success', 'Élève supprimé.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }
}
