<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enseignant;
use App\Models\User;
use App\Models\Classe;
use App\Models\Cours;
use App\Models\Plan;
use App\Models\Periode;
use App\Models\Cote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CorpsEnseignantController extends Controller
{
    /**
     * Afficher la liste des enseignants de l'établissement.
     */
    public function index()
    {
        $ecoleId = session('ecole_id');

        $enseignants = Enseignant::where('ecole_id', $ecoleId)
            ->with('user')
            ->orderBy('nom')
            ->get();

        $stats = (object) [
            'total' => $enseignants->count(),
            'avec_affectation' => Plan::whereHas('cours', fn($q) => $q->where('ecole_id', $ecoleId))
                ->distinct('enseignant_id')
                ->count('enseignant_id'),
        ];

        return view('directeur.enseignants.index', compact('enseignants', 'stats'));
    }

    /**
     * Afficher le formulaire d'attribution des classes/cours.
     */
    public function attributionForm($enseignantId = null)
    {
        $ecoleId = session('ecole_id');

        $enseignants = Enseignant::where('ecole_id', $ecoleId)
            ->with('user')
            ->orderBy('nom')
            ->get();

        $classes = Classe::where(function ($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))
                  ->orWhereNull('option_id');
            })
            ->orderBy('niveau')
            ->orderBy('nom_classe')
            ->get();
        $cours = Cours::where('ecole_id', $ecoleId)->orderBy('nom_cours')->get();

        $selectedEnseignant = null;
        $affectations = collect();

        if ($enseignantId) {
            $selectedEnseignant = Enseignant::where('ecole_id', $ecoleId)
                ->with('user')
                ->findOrFail($enseignantId);

            $affectations = Plan::where('enseignant_id', $selectedEnseignant->user_id)
                ->with(['classe', 'cours'])
                ->get();
        }

        return view('directeur.enseignants.attributions', compact(
            'enseignants', 'classes', 'cours',
            'selectedEnseignant', 'affectations'
        ));
    }

    /**
     * Enregistrer une attribution (Plan) pour un enseignant.
     */
    public function storeAttribution(Request $request)
    {
        $ecoleId = session('ecole_id');

        $request->validate([
            'enseignant_id' => 'required|exists:enseignants,id',
            'classe_id' => 'required|exists:classes,id',
            'cours_id' => 'required|exists:cours,id',
            'maxima_periode' => 'required|integer|min:1|max:100',
            'maxima_examen' => 'required|integer|min:1|max:100',
            'annee_scolaire' => 'required|string|max:20',
        ]);

        $enseignant = Enseignant::where('ecole_id', $ecoleId)->findOrFail($request->enseignant_id);

        // Vérifier que le cours appartient bien à l'école
        $cours = Cours::where('ecole_id', $ecoleId)->findOrFail($request->cours_id);

// Vérifier les doublons strictement identiques :
        // même enseignant + même cours + même classe + même année.
        // Un cours peut être attribué à plusieurs classes (niveaux différents)
        // et par plusieurs enseignants différents.
        $existe = Plan::where('classe_id', $request->classe_id)
            ->where('cours_id', $request->cours_id)
            ->where('enseignant_id', $enseignant->user_id)
            ->where('annee_scolaire', $request->annee_scolaire)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Cet enseignant enseigne déjà ce cours dans cette classe pour cette année scolaire.');
        }

        Plan::create([
            'classe_id' => $request->classe_id,
            'cours_id' => $request->cours_id,
            'enseignant_id' => $enseignant->user_id,
            'maxima_periode' => $request->maxima_periode,
            'maxima_examen' => $request->maxima_examen,
            'annee_scolaire' => $request->annee_scolaire,
        ]);

        return redirect()
            ->route('directeur.enseignants.attributions', $enseignant->id)
            ->with('success', 'Cours attribué avec succès !');
    }

    /**
     * Supprimer une attribution (Plan).
     */
    public function destroyAttribution($planId)
    {
        $plan = Plan::findOrFail($planId);

        // Vérifier que l'enseignant appartient à l'école du directeur
        $enseignant = Enseignant::where('user_id', $plan->enseignant_id)
            ->where('ecole_id', session('ecole_id'))
            ->firstOrFail();

        $plan->delete();

        return redirect()
            ->route('directeur.enseignants.attributions', $enseignant->id)
            ->with('success', 'Attribution supprimée.');
    }

/**
     * Superviser les cotes encodées par les enseignants.
     */
    public function supervisionCotes(Request $request)
    {
        $ecoleId = session('ecole_id');

        $periodes = Periode::where('ecole_id', $ecoleId)->orderBy('nom_periode')->get();
        $enseignants = Enseignant::where('ecole_id', $ecoleId)->with('user')->orderBy('nom')->get();

        $query = Cote::whereHas('plan.cours', fn($q) => $q->where('ecole_id', $ecoleId))
            ->with(['inscription.eleve', 'plan.cours', 'plan.classe', 'periode', 'encodeur']);

        if ($request->filled('enseignant_id')) {
            $userIds = Enseignant::where('ecole_id', $ecoleId)
                ->where('id', $request->enseignant_id)
                ->pluck('user_id');
            $query->whereIn('encode_par', $userIds);
        }

        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }

        if ($request->filled('classe_id')) {
            $planIds = Plan::where('classe_id', $request->classe_id)->pluck('id');
            $query->whereIn('plan_id', $planIds);
        }

        // --- Statistiques globales (sur toutes les cotes filtrées, hors pagination) ---
        $toutesCotes = (clone $query)->get();

        $totalCotes = $toutesCotes->count();
        $totalPoints = $toutesCotes->sum('total_points');
        $totalMax = $toutesCotes->sum('max_total');
        $moyenneGlobale = $totalMax > 0 ? round(($totalPoints / $totalMax) * 20, 2) : null;

        $reussis = $toutesCotes->filter(fn($c) => $c->statut === 'Réussi')->count();
        $echoues = $toutesCotes->filter(fn($c) => $c->statut === 'Échoué')->count();
        $tauxReussite = $totalCotes > 0 ? round(($reussis / $totalCotes) * 100, 1) : null;

        $presenceMoyenne = $toutesCotes
            ->filter(fn($c) => $c->pourcentage_presence !== null)
            ->avg('pourcentage_presence');
        $presenceMoyenne = $presenceMoyenne !== null ? round($presenceMoyenne, 2) : null;

        // Nombre d'élèves concernés
        $elevesConcernes = $toutesCotes->pluck('inscription.eleve_id')->unique()->count();

        $stats = (object) [
            'total_cotes' => $totalCotes,
            'moyenne_globale' => $moyenneGlobale,
            'taux_reussite' => $tauxReussite,
            'reussis' => $reussis,
            'echoues' => $echoues,
            'presence_moyenne' => $presenceMoyenne,
            'eleves_concernes' => $elevesConcernes,
        ];

        $cotes = $query->orderBy('created_at', 'desc')
            ->paginate(50)
            ->withQueryString();

        $classes = Classe::where(function ($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))
                  ->orWhereNull('option_id');
            })
            ->orderBy('nom_classe')
            ->get();

        return view('directeur.enseignants.supervision_cotes', compact(
            'cotes', 'periodes', 'enseignants', 'classes', 'stats'
        ));
    }
}
