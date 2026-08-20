<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enseignant;
use App\Models\Cours;
use App\Models\Plan;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Cote;
use App\Models\Periode;
use App\Models\Classe;
use App\Models\Presence;
use App\Models\BulletinValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnseignantController extends Controller
{
    /**
     * Afficher le tableau de bord de l'enseignant avec statistiques et performances.
     */
    public function dashboard()
    {
        $ecoleId = session('ecole_id') ?? Auth::user()->ecole_id;
        $userId = Auth::id();

        // Récupérer les informations professionnelles de l'enseignant
        $enseignant = Enseignant::where('user_id', $userId)
                                ->where('ecole_id', $ecoleId)
                                ->first();

        // Récupérer les plans (cours assignés) via la table plans
        $plans = Plan::where('enseignant_id', $userId)
                     ->whereHas('cours', function ($q) use ($ecoleId) {
                         $q->where('ecole_id', $ecoleId);
                     })
                     ->with(['cours', 'classe.option'])
                     ->get();

        // Compter les élèves inscrits dans les classes de l'enseignant
        $classeIds = $plans->pluck('classe_id')->unique();
        $totalEleves = Inscription::whereIn('classe_id', $classeIds)
                                  ->where('statut', 'actif')
                                  ->count();

        // Compter les périodes non clôturées
        $periodesActives = Periode::where('ecole_id', $ecoleId)
                                  ->where('est_cloturee', false)
                                  ->count();

        // Performance et cotes par classe / cours
        $planIds = $plans->pluck('id');
        $cotesEnseignant = Cote::whereIn('plan_id', $planIds)->with(['plan.cours', 'plan.classe', 'inscription.eleve'])->get();

        $scoresParClasse = [];
        $totalCotesCount = 0;
        $sommeNotes = 0;
        $nbReussites = 0;

        foreach ($plans as $p) {
            $cotesDuPlan = $cotesEnseignant->where('plan_id', $p->id);
            $somme = 0;
            $count = 0;
            $reussis = 0;

            foreach ($cotesDuPlan as $c) {
                $moy = \App\Services\BulletinService::moyenneMatiere($c, $p);
                if ($moy !== null) {
                    $somme += $moy;
                    $count++;
                    $sommeNotes += $moy;
                    $totalCotesCount++;
                    if ($moy >= 10) {
                        $reussis++;
                        $nbReussites++;
                    }
                }
            }

            $scoresParClasse[] = [
                'plan_id'       => $p->id,
                'classe_id'     => $p->classe_id,
                'classe_nom'    => $p->classe->nom_classe ?? 'Classe',
                'cours_nom'     => $p->cours->nom_cours ?? 'Cours',
                'effectif'      => Inscription::where('classe_id', $p->classe_id)->where('statut', 'actif')->count(),
                'notes_saisies' => $count,
                'moyenne'       => $count > 0 ? round($somme / $count, 2) : null,
                'taux_reussite' => $count > 0 ? round(($reussis / $count) * 100, 1) : null,
            ];
        }

        $moyenneGlobale = $totalCotesCount > 0 ? round($sommeNotes / $totalCotesCount, 2) : null;
        $tauxReussiteGlobal = $totalCotesCount > 0 ? round(($nbReussites / $totalCotesCount) * 100, 1) : null;

        // Données pour le graphique des classes
        $classesChartLabels = array_map(fn($item) => $item['classe_nom'] . ' (' . substr($item['cours_nom'], 0, 12) . ')', $scoresParClasse);
        $classesChartData = array_map(fn($item) => $item['moyenne'] ?? 0, $scoresParClasse);

        return view('enseignant.dashboard', compact(
            'enseignant',
            'plans',
            'totalEleves',
            'periodesActives',
            'scoresParClasse',
            'moyenneGlobale',
            'tauxReussiteGlobal',
            'classesChartLabels',
            'classesChartData'
        ));
    }
    public function elevesParClasse($classeId, $planId = null)
    {
        $ecoleId = session('ecole_id');
        $userId = Auth::id();

        // Vérifier que l'enseignant est bien assigné à cette classe
        $plans = Plan::where('classe_id', $classeId)
                    ->where('enseignant_id', $userId)
                    ->with('cours')
                    ->get();

        if ($plans->isEmpty()) {
            return redirect()->route('enseignant.dashboard')
                ->with('error', 'Aucun cours ne vous est attribué dans cette classe.');
        }

        // Si aucun planId spécifié, prendre le premier
        if (!$planId || !$plans->contains('id', $planId)) {
            $planId = $plans->first()->id;
        }

        $plan = $plans->firstWhere('id', $planId);
        $classe = Classe::findOrFail($classeId);

        // Récupérer les inscriptions actives de cette classe
        $inscriptions = Inscription::where('classe_id', $classeId)
                                   ->where('statut', 'actif')
                                   ->with(['eleve', 'cotes' => function ($q) use ($planId) {
                                       $q->where('plan_id', $planId);
                                   }])
                                   ->get();

        $cours = Cours::findOrFail($plan->cours_id);
        $inscriptionIds = $inscriptions->pluck('id');
        $matiereValidee = $inscriptions->isNotEmpty()
            && BulletinValidation::where('plan_id', $plan->id)->whereIn('inscription_id', $inscriptionIds)
                ->where('statut', BulletinValidation::VALIDE)->count() === $inscriptions->count();

        return view('enseignant.cotes', compact(
            'inscriptions',
            'classe',
            'cours',
            'plans',
            'plan', 'matiereValidee'
        ));
    }

    /**
     * Enregistrer les cotes pour les élèves (nouveau système à 12 champs).
     */
    public function enregistrerCotes(Request $request, $classeId)
    {
        $userId = Auth::id();

$request->validate([
            'plan_id' => 'required|exists:plans,id',
            'champ' => 'required|string|in:interrogation_s1,devoir_domicile_s1,periode_1,periode_2,periode_3,examen_s1,interrogation_s2,devoir_domicile_s2,periode_4,periode_5,periode_6,examen_s2',
            'notes' => 'required|array',
            'notes.*' => 'nullable|numeric|min:0|max:999',
        ]);

        $planId = $request->plan_id;
        $champ = $request->champ;

        abort_unless(Plan::whereKey($planId)->where('classe_id', $classeId)->where('enseignant_id', $userId)->exists(), 403);

        foreach ($request->notes as $inscriptionId => $valeur) {
            $inscription = Inscription::findOrFail($inscriptionId);

            // Vérifier que l'inscription appartient bien à cette classe
            if ($inscription->classe_id != $classeId) {
                continue;
            }

            $cote = Cote::updateOrCreate(
                [
                    'inscription_id' => $inscriptionId,
                    'plan_id' => $planId,
                ],
                [
                    'encode_par' => $userId,
                ]
            );

            $cote->{$champ} = $valeur !== '' ? $valeur : null;
            $cote->save();
            // Toute correction doit être revue avant publication du bulletin.
            BulletinValidation::updateOrCreate(
                ['inscription_id' => $inscriptionId, 'plan_id' => $planId],
                ['statut' => BulletinValidation::EN_ATTENTE, 'valide_par' => null, 'valide_le' => null]
            );
        }

return redirect()->back()->with('success', 'Notes enregistrées avec succès !');
    }

    /** Valide une matière, pour tous les élèves de la classe, avant la génération définitive. */
    public function validerMatiereBulletins($classeId, $planId)
    {
        $plan = Plan::whereKey($planId)->where('classe_id', $classeId)
            ->where('enseignant_id', Auth::id())->firstOrFail();
        $inscriptions = Inscription::where('classe_id', $classeId)->where('statut', 'actif')->get();
        $champs = ['interrogation_s1', 'devoir_domicile_s1', 'periode_1', 'periode_2', 'periode_3', 'examen_s1', 'interrogation_s2', 'devoir_domicile_s2', 'periode_4', 'periode_5', 'periode_6', 'examen_s2'];

        foreach ($inscriptions as $inscription) {
            $cote = Cote::where('inscription_id', $inscription->id)->where('plan_id', $plan->id)->first();
            if (! $cote || ! collect($champs)->contains(fn ($champ) => $cote->{$champ} !== null)) {
                return back()->with('error', 'Validation impossible : chaque élève doit avoir au moins une note pour cette matière.');
            }
        }

        foreach ($inscriptions as $inscription) {
            BulletinValidation::updateOrCreate(
                ['inscription_id' => $inscription->id, 'plan_id' => $plan->id],
                ['statut' => BulletinValidation::VALIDE, 'valide_par' => Auth::id(), 'valide_le' => now()]
            );
        }

        return back()->with('success', 'Matière validée pédagogiquement. Les bulletins restent en attente des autres matières.');
    }

/**
     * Afficher le formulaire de saisie des présences hebdomadaires.
     * Grille : lignes = élèves de la classe, colonnes = jours de la semaine (Lun-Ven).
     * La semaine est calculée automatiquement à partir de la date (défaut : aujourd'hui).
     */
    public function presenceForm($classeId, $planId = null, $date = null)
    {
        $ecoleId = session('ecole_id');
        $userId = Auth::id();

        // Vérifier que l'enseignant est bien assigné à cette classe
        $plans = Plan::where('classe_id', $classeId)
                    ->where('enseignant_id', $userId)
                    ->with('cours')
                    ->get();

        if ($plans->isEmpty()) {
            return redirect()->route('enseignant.dashboard')
                ->with('error', 'Aucun cours ne vous est attribué dans cette classe.');
        }

        if (!$planId || !$plans->contains('id', $planId)) {
            $planId = $plans->first()->id;
        }

        $plan = $plans->firstWhere('id', $planId);
        $classe = Classe::findOrFail($classeId);
        $cours = Cours::findOrFail($plan->cours_id);

        // Déterminer la semaine affichée (par défaut : semaine courante via today())
        $dateRef = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::today();
        $lundi = $dateRef->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $jours = [];
        for ($i = 0; $i < 5; $i++) {
            $jours[] = $lundi->copy()->addDays($i);
        }

        // Dates de début/fin de semaine + navigation
        $dateDebut = $lundi->toDateString();
        $dateFin = $lundi->copy()->addDays(4)->toDateString();
        $semainePrecedente = $lundi->copy()->subWeek()->toDateString();
        $semaineSuivante = $lundi->copy()->addWeek()->toDateString();

        // Récupérer les inscriptions actives de cette classe (avec l'élève)
        $inscriptions = Inscription::where('classe_id', $classeId)
                                   ->where('statut', 'actif')
                                   ->with('eleve')
                                   ->get();

// Récupérer les présences déjà enregistrées pour ce plan et cette semaine
        // La clé est : eleve_id - jour_index (colonne), indépendante de la date modifiable
        $presences = Presence::where('plan_id', $planId)
                             ->where('semaine_debut', $dateDebut)
                             ->get()
                             ->keyBy(fn($p) => $p->eleve_id . '-' . $p->jour_index);

        return view('enseignant.presence', compact(
            'inscriptions',
            'classe',
            'cours',
            'plans',
            'plan',
            'jours',
            'dateDebut',
            'dateFin',
            'semainePrecedente',
            'semaineSuivante',
            'presences'
        ));
    }

    /**
     * Enregistrer les présences hebdomadaires pour les élèves.
     * Chaque élève reçoit un statut par jour : present, absent, retard.
     * L'enregistrement se fait par eleve_id (référence directe à l'élève).
     */
    public function enregistrerPresence(Request $request, $classeId)
    {
        $userId = Auth::id();

$request->validate([
            'plan_id' => 'required|exists:plans,id',
            'date_debut' => 'required|date',
            'dates' => 'required|array|size:5',
            'dates.*' => 'required|date',
            'statuts' => 'required|array',
            'statuts.*' => 'required|array',
            'statuts.*.*' => 'required|string|in:present,absent,retard',
        ]);

        $planId = $request->plan_id;
        $dateDebut = \Carbon\Carbon::parse($request->date_debut)->startOfWeek(\Carbon\Carbon::MONDAY)
                                   ->toDateString();

        // Les dates de chaque colonne sont saisies (et modifiables) par l'enseignant
        $dates = $request->dates;

        foreach ($request->statuts as $eleveId => $joursStatuts) {
            // L'élève doit être inscrit dans cette classe
            $inscription = Inscription::where('classe_id', $classeId)
                                      ->where('eleve_id', $eleveId)
                                      ->where('statut', 'actif')
                                      ->first();

            if (!$inscription) {
                continue;
            }

            foreach ($joursStatuts as $jourIndex => $statut) {
                // Ne pas enregistrer si la date du jour n'est pas fournie
                if (!isset($dates[$jourIndex]) || empty($dates[$jourIndex])) {
                    continue;
                }

                $date = \Carbon\Carbon::parse($dates[$jourIndex])->toDateString();

                Presence::updateOrCreate(
                    [
                        'eleve_id' => $eleveId,
                        'plan_id' => $planId,
                        'semaine_debut' => $dateDebut,
                        'jour_index' => (int) $jourIndex,
                    ],
                    [
                        'date' => $date,
                        'statut' => $statut,
                        'encode_par' => $userId,
                    ]
                );
            }
        }

        // Mettre à jour le pourcentage de présence sur la cote de chaque élève
        $this->mettreAJourPourcentagePresence($planId, $classeId);

        // Rediriger vers la feuille de présence de la même semaine
        return redirect()
            ->route('enseignant.presence.form', [$classeId, $planId, $dateDebut->toDateString()])
            ->with('success', 'Présences de la semaine enregistrées avec succès !');
    }

    /**
     * Calculer et mettre à jour le pourcentage de présence de chaque élève
     * sur sa cote, à partir des présences enregistrées.
     *
     * Règle : % = (présents / total jours encodés) × 100
     * Seul le statut 'present' compte comme présent.
     */
    private function mettreAJourPourcentagePresence($planId, $classeId)
    {
        $inscriptions = Inscription::where('classe_id', $classeId)
                                   ->where('statut', 'actif')
                                   ->get();

        foreach ($inscriptions as $inscription) {
            $presences = Presence::where('eleve_id', $inscription->eleve_id)
                                 ->where('plan_id', $planId)
                                 ->get();

            $total = $presences->count();
            if ($total === 0) {
                continue;
            }

            $present = $presences->where('est_present', true)->count();
            $pourcentage = round(($present / $total) * 100, 2);

            Cote::updateOrCreate(
                [
                    'inscription_id' => $inscription->id,
                    'plan_id' => $planId,
                ],
                [
                    'encode_par' => Auth::id(),
                    'pourcentage_presence' => $pourcentage,
                ]
            );
        }
    }

    /**
     * Afficher le profil de l'enseignant.
     */
    public function profil()
    {
        $ecoleId = session('ecole_id');
        $userId = Auth::id();

        $enseignant = Enseignant::where('user_id', $userId)
                                ->where('ecole_id', $ecoleId)
                                ->first();

        $user = Auth::user();

        return view('enseignant.profil', compact('enseignant', 'user'));
    }

    /**
     * Afficher les statistiques rapides pour l'enseignant.
     */
    public function statistiques()
    {
        $ecoleId = session('ecole_id');
        $userId = Auth::id();

        // Statistiques des cotes encodées par l'enseignant
        $stats = Cote::where('encode_par', $userId)
                     ->selectRaw('COUNT(*) as total_cotes')
                     ->selectRaw('ROUND(AVG(points_obtenus), 2) as moyenne_generale')
                     ->first();

        return response()->json($stats);
    }
}
