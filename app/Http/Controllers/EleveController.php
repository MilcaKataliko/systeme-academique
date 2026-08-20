<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Cote;
use App\Models\Plan;
use App\Models\Paiement;
use App\Models\Frais;
use App\Models\Periode;
use App\Models\BulletinValidation;
use App\Services\BulletinService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EleveController extends Controller
{
    /**
     * Calcul d'un score de risque basé sur la moyenne, les notes et le solde financier.
     */
    private function buildRiskAnalysis(?float $moyenneGenerale, int $notesRecues, float $solde): array
    {
        $scoreRisque = 0;
        $alertes = [];

        if ($moyenneGenerale !== null) {
            $scoreRisque += max(0, (20 - $moyenneGenerale) * 5);
            if ($moyenneGenerale < 10) {
                $alertes[] = 'Moyenne générale sous la moyenne attendue';
            }
        } else {
            $alertes[] = 'Peu de données académiques pour une analyse fiable';
        }

        if ($solde > 0) {
            $scoreRisque += min(25, $solde * 0.8);
            $alertes[] = 'Solde financier restant à régulariser';
        }

        if ($notesRecues === 0) {
            $scoreRisque += 20;
            $alertes[] = 'Aucune note enregistrée pour le moment';
        }

        $scoreRisque = min(100, round($scoreRisque));

        if ($scoreRisque < 30) {
            $niveauRisque = 'Faible';
            $couleurRisque = 'emerald';
            $recommandations = [
                'Continuez votre rythme actuel pour consolider les résultats.',
                'Poursuivez les révisions régulières avant les évaluations.'
            ];
        } elseif ($scoreRisque < 60) {
            $niveauRisque = 'Moyen';
            $couleurRisque = 'amber';
            $recommandations = [
                'Suivez de plus près les matières faibles.',
                'Demandez un accompagnement ou un soutien pédagogique ciblé.'
            ];
        } else {
            $niveauRisque = 'Élevé';
            $couleurRisque = 'red';
            $recommandations = [
                'Une aide pédagogique rapide est recommandée.',
                'Établissez un plan de rattrapage avec l’enseignant et le conseiller.'
            ];
        }

        return compact('scoreRisque', 'niveauRisque', 'couleurRisque', 'alertes', 'recommandations');
    }

    /**
     * Afficher le tableau de bord de l'élève.
     */
    public function dashboard()
    {
        $ecoleId = session('ecole_id') ?? Auth::user()->ecole_id;
        $userId = Auth::id();

        // Récupérer l'élève connecté
        $eleve = Eleve::where('user_id', $userId)
                      ->where('ecole_id', $ecoleId)
                      ->firstOrFail();

        // Récupérer les inscriptions actives
        $inscriptions = Inscription::where('eleve_id', $eleve->id)
            ->where('statut', 'actif')
            ->with(['classe', 'cotes.plan.cours', 'paiements.frais'])
            ->get();

        // Calcul de la moyenne générale
        $totalPoints = 0;
        $totalMax = 0;
        $coursCount = 0;

        foreach ($inscriptions as $inscription) {
            foreach ($inscription->cotes as $cote) {
                $totalPoints += $cote->total_points;
                $maxVal = $cote->max_total;
                if ($maxVal > 0) {
                    $totalMax += $maxVal;
                    $coursCount++;
                }
            }
        }
        $moyenneGenerale = $totalMax > 0 ? round(($totalPoints / $totalMax) * 20, 2) : null;

        // Nombre de cours suivis
        $plansIds = $inscriptions->pluck('cotes')->flatten()->pluck('plan_id')->unique();
        $coursSuivis = Plan::whereIn('id', $plansIds)->count();

        // Nombre de bulletins disponibles
        $periodesAvecCotes = Periode::where('ecole_id', $ecoleId)
            ->where('est_cloturee', true)
            ->whereHas('cotes', function ($q) use ($eleve) {
                $q->whereHas('inscription', fn($iq) => $iq->where('eleve_id', $eleve->id));
            })
            ->count();

        // Situation financière
        $totalDu = 0;
        $totalPaye = 0;
        foreach ($inscriptions as $inscription) {
            $fraisClasse = Frais::where('classe_id', $inscription->classe_id)
                ->where('annee_scolaire', $inscription->annee_scolaire)
                ->get();
            foreach ($fraisClasse as $f) {
                $totalDu += $f->montant;
            }
            foreach ($inscription->paiements as $paiement) {
                $totalPaye += $paiement->montant_paye;
            }
        }
        $solde = max(0, $totalDu - $totalPaye);

        $notesRecues = 0;
        $matieresScores = [];
        foreach ($inscriptions as $inscription) {
            $notesRecues += $inscription->cotes->count();
            foreach ($inscription->cotes as $cote) {
                if ($cote->plan && $cote->plan->cours) {
                    $cNom = $cote->plan->cours->nom_cours;
                    $mMoy = BulletinService::moyenneMatiere($cote, $cote->plan);
                    if ($mMoy !== null) {
                        $matieresScores[$cNom] = $mMoy;
                    }
                }
            }
        }

        $matieresLabels = array_keys($matieresScores);
        $matieresNotes = array_values($matieresScores);

        ['scoreRisque' => $scoreRisque, 'niveauRisque' => $niveauRisque, 'couleurRisque' => $couleurRisque, 'alertes' => $alertes, 'recommandations' => $recommandations] = $this->buildRiskAnalysis($moyenneGenerale, $notesRecues, $solde);

        return view('eleve.dashboard', compact(
            'eleve',
            'inscriptions',
            'moyenneGenerale',
            'coursSuivis',
            'periodesAvecCotes',
            'totalDu',
            'totalPaye',
            'solde',
            'scoreRisque',
            'niveauRisque',
            'couleurRisque',
            'alertes',
            'recommandations',
            'matieresLabels',
            'matieresNotes'
        ));
    }

    /**
     * Afficher les notes de l'élève.
     */
    public function notes()
    {
        $ecoleId = session('ecole_id') ?? Auth::user()->ecole_id;
        $userId = Auth::id();

        $eleve = Eleve::where('user_id', $userId)
                      ->where('ecole_id', $ecoleId)
                      ->firstOrFail();

        $inscriptions = Inscription::where('eleve_id', $eleve->id)
            ->where('statut', 'actif')
            ->with(['classe', 'cotes.plan.cours', 'cotes.periode'])
            ->get();

        // Organiser les notes par plan (cours)
        $notesParCours = collect();
        foreach ($inscriptions as $inscription) {
            foreach ($inscription->cotes->groupBy('plan_id') as $planId => $cotes) {
                $plan = $cotes->first()->plan;
                $cours = $plan->cours;
                $maxPeriode = $plan->maxima_periode ?? 10;
                $maxExamen = $plan->maxima_examen ?? 20;

                $totalObtenu = $cotes->sum('total_points');
                $maxTotal = $cotes->sum('max_total');
                $pourcentage = $maxTotal > 0 ? round(($totalObtenu / $maxTotal) * 100, 2) : null;

                $notesParCours->push((object) [
                    'cours' => $cours,
                    'classe' => $inscription->classe,
                    'cotes' => $cotes,
                    'total_obtenu' => $totalObtenu,
                    'max_total' => $maxTotal,
                    'pourcentage' => $pourcentage,
                    'max_periode' => $maxPeriode,
                    'max_examen' => $maxExamen,
                ]);
            }
        }

        $notesRecues = $notesParCours->sum(fn($item) => $item->cotes->count());
        $moyenneGenerale = $notesParCours->isNotEmpty() ? round($notesParCours->avg(fn($item) => $item->pourcentage !== null ? ($item->pourcentage / 5) : null), 2) : null;

        ['scoreRisque' => $scoreRisque, 'niveauRisque' => $niveauRisque, 'couleurRisque' => $couleurRisque, 'alertes' => $alertes, 'recommandations' => $recommandations] = $this->buildRiskAnalysis($moyenneGenerale, $notesRecues, 0);

        return view('eleve.notes', compact('eleve', 'inscriptions', 'notesParCours', 'scoreRisque', 'niveauRisque', 'couleurRisque', 'alertes', 'recommandations'));
    }

    /**
     * Afficher les bulletins disponibles.
     */
    public function bulletins()
    {
        $ecoleId = session('ecole_id') ?? Auth::user()->ecole_id;
        $userId = Auth::id();

        $eleve = Eleve::where('user_id', $userId)
                      ->where('ecole_id', $ecoleId)
                      ->firstOrFail();

        $inscriptions = Inscription::where('eleve_id', $eleve->id)
            ->with(['classe', 'cotes.plan.cours', 'cotes.periode'])
            ->get();

        $cotesBulletin = $inscriptions->flatMap(fn ($inscription) => $inscription->cotes)
            ->filter(fn ($cote) => $cote->plan)
            ->values();

        $libellesNotes = [
            'interrogation_s1' => 'Interro S1', 'devoir_domicile_s1' => 'Devoir S1',
            'periode_1' => 'P1', 'periode_2' => 'P2', 'periode_3' => 'P3', 'examen_s1' => 'Examen S1',
            'interrogation_s2' => 'Interro S2', 'devoir_domicile_s2' => 'Devoir S2',
            'periode_4' => 'P4', 'periode_5' => 'P5', 'periode_6' => 'P6', 'examen_s2' => 'Examen S2',
        ];

        $bulletinRows = $cotesBulletin->map(function ($cote) use ($libellesNotes) {
            $notes = collect($libellesNotes)->map(function ($libelle, $champ) use ($cote) {
                if ($cote->{$champ} === null) {
                    return null;
                }

                return [
                    'libelle' => $libelle,
                    'note' => $cote->{$champ},
                    'maximum' => BulletinService::maximumPourChamp($cote->plan, $champ),
                ];
            })->filter()->values();

            return (object) [
                'inscription' => $cote->inscription,
                'plan' => $cote->plan,
                'notes' => $notes,
                'moyenne' => BulletinService::moyenneMatiere($cote, $cote->plan),
            ];
        })->filter(fn ($row) => $row->moyenne !== null)->sortBy(fn ($row) => $row->plan->cours->nom_cours)->values();

        $resumeBulletin = BulletinService::moyenneGenerale($cotesBulletin);

        $periodesIds = $inscriptions
            ->flatMap(fn ($inscription) => $inscription->cotes)
            ->pluck('periode_id')
            ->unique()
            ->values();

        $periodes = $periodesIds->isNotEmpty()
            ? Periode::whereIn('id', $periodesIds)->orderBy('id')->get()
            : collect();

        $notesRecues = $cotesBulletin->count();
        $moyenneGenerale = $resumeBulletin['moyenne'];

        ['scoreRisque' => $scoreRisque, 'niveauRisque' => $niveauRisque, 'couleurRisque' => $couleurRisque, 'alertes' => $alertes, 'recommandations' => $recommandations] = $this->buildRiskAnalysis($moyenneGenerale, $notesRecues, 0);

        return view('eleve.bulletins', compact('eleve', 'inscriptions', 'periodes', 'bulletinRows', 'resumeBulletin', 'scoreRisque', 'niveauRisque', 'couleurRisque', 'alertes', 'recommandations'));
    }

    /**
     * Afficher la situation financière de l'élève.
     */
    public function finances()
    {
        $ecoleId = session('ecole_id') ?? Auth::user()->ecole_id;
        $userId = Auth::id();

        $eleve = Eleve::where('user_id', $userId)
                      ->where('ecole_id', $ecoleId)
                      ->firstOrFail();

        $inscriptions = Inscription::where('eleve_id', $eleve->id)
            ->where('statut', 'actif')
            ->with(['classe', 'paiements.frais'])
            ->get();

        $totalDu = 0;
        $totalPaye = 0;
        $detailsFrais = collect();

        foreach ($inscriptions as $inscription) {
            $fraisClasse = Frais::where('classe_id', $inscription->classe_id)
                ->where('annee_scolaire', $inscription->annee_scolaire)
                ->get();

            foreach ($fraisClasse as $f) {
                $payePourFrais = $inscription->paiements
                    ->where('frais_id', $f->id)
                    ->sum('montant_paye');

                $totalDu += $f->montant;
                $totalPaye += $payePourFrais;

                $detailsFrais->push((object) [
                    'inscription' => $inscription,
                    'frais' => $f,
                    'montant_du' => $f->montant,
                    'montant_paye' => $payePourFrais,
                    'solde' => $f->montant - $payePourFrais,
                    'paiements' => $inscription->paiements->where('frais_id', $f->id),
                ]);
            }
        }

        $solde = $totalDu - $totalPaye;

        return view('eleve.finances', compact('eleve', 'inscriptions', 'totalDu', 'totalPaye', 'solde', 'detailsFrais'));
    }

    /**
     * Afficher le profil de l'élève.
     */
    public function profil()
    {
        $ecoleId = session('ecole_id') ?? Auth::user()->ecole_id;
        $userId = Auth::id();

        $eleve = Eleve::where('user_id', $userId)
                      ->where('ecole_id', $ecoleId)
                      ->firstOrFail();

        $inscriptions = Inscription::where('eleve_id', $eleve->id)
            ->with(['classe', 'ecole'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('eleve.profil', compact('eleve', 'inscriptions'));
    }
}
