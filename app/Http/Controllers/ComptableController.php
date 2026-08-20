<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Frais;
use App\Models\FraisClasse;
use App\Models\Paiement;
use App\Models\Inscription;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class ComptableController extends Controller
{
    /**
     * Tableau de bord du comptable avec statistiques complètes et graphiques.
     */
    public function dashboard()
    {
        $ecoleId = session('ecole_id') ?? Auth::user()->ecole_id;

        $totalPaiements = (float) Paiement::where('ecole_id', $ecoleId)->sum('montant_paye');
        $nombrePaiements = Paiement::where('ecole_id', $ecoleId)->count();

        // Calcul des frais attendus (Total facturé réel)
        $fraisList = Frais::where('ecole_id', $ecoleId)->get();
        $inscriptions = Inscription::where('ecole_id', $ecoleId)->where('statut', 'actif')->with(['paiements', 'classe'])->get();

        $totalFacture = 0;
        $elevesEnRetard = 0;
        $elevesEnRegle = 0;

        foreach ($inscriptions as $insc) {
            $fraisApplicables = $fraisList->filter(function($f) use ($insc) {
                $matchClasse = is_null($f->classe_id) || $f->classe_id == $insc->classe_id;
                $matchAnnee = is_null($f->annee_scolaire) || $f->annee_scolaire == $insc->annee_scolaire;
                return $matchClasse && $matchAnnee;
            });
            $montantDu = (float) $fraisApplicables->sum('montant');
            $totalFacture += $montantDu;

            $montantPaye = (float) $insc->paiements->sum('montant_paye');
            if ($montantDu > $montantPaye) {
                $elevesEnRetard++;
            } else {
                $elevesEnRegle++;
            }
        }

        $totalRestant = max(0, $totalFacture - $totalPaiements);
        $tauxRecouvrement = $totalFacture > 0 ? round(($totalPaiements / $totalFacture) * 100, 1) : 0;

        $stats = (object) [
            'total_paiements'   => $totalPaiements,
            'nombre_paiements'  => $nombrePaiements,
            'total_frais'       => $totalFacture,
            'total_restant'     => $totalRestant,
            'taux_recouvrement' => $tauxRecouvrement,
            'eleves_en_retard'  => $elevesEnRetard,
            'eleves_en_regle'   => $elevesEnRegle,
            'total_eleves'      => $inscriptions->count(),
        ];

        // Évolution des encaissements par mois (6 derniers mois)
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

        // Répartition par mode de paiement
        $modes = Paiement::where('ecole_id', $ecoleId)
            ->select('mode_paiement', DB::raw('count(*) as count'), DB::raw('sum(montant_paye) as total'))
            ->groupBy('mode_paiement')
            ->get();
        $modesLabels = [];
        $modesData = [];
        foreach ($modes as $m) {
            $modesLabels[] = ucfirst($m->mode_paiement ?: 'Espèces');
            $modesData[] = (float) $m->total;
        }

        $paiementsRecents = Paiement::where('ecole_id', $ecoleId)
            ->with(['inscription.eleve', 'frais', 'inscription.classe'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('comptable.dashboard', compact(
            'stats',
            'paiementsRecents',
            'evolutionPaiementsLabels',
            'evolutionPaiementsData',
            'modesLabels',
            'modesData'
        ));
    }

    /**
     * Afficher la liste des frais (un frais = intitulé + classe + montant + année).
     */
    public function fraisIndex()
    {
        $ecoleId = session('ecole_id');
        $frais = Frais::where('ecole_id', $ecoleId)
            ->with('classe')
            ->orderBy('created_at', 'desc')
            ->get();

        $classes = Classe::where(function ($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))
                  ->orWhereNull('option_id');
            })
            ->orderBy('niveau')->orderBy('nom_classe')
            ->get();

        // Données pour la vue frais/classe.blade.php (association frais ↔ classe)
        $fraisClasses = FraisClasse::whereHas('frais', fn($q) => $q->where('ecole_id', $ecoleId))
            ->with(['frais', 'classe'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('comptable.frais.index', compact('frais', 'classes', 'fraisClasses'));
    }

    /**
     * Associer un frais existant à une classe (table pivot frais_classe).
     */
    public function fraisClasseStore(Request $request)
    {
        $ecoleId = session('ecole_id');

        $request->validate([
            'frais_id' => 'required|exists:frais,id',
            'classe_id' => 'required|exists:classes,id',
            'montant_specifique' => 'required|numeric|min:0',
            'annee_scolaire' => 'required|string|max:20',
        ]);

        // Vérifier que le frais appartient à l'école
        Frais::where('ecole_id', $ecoleId)->findOrFail($request->frais_id);
        // Vérifier que la classe appartient à l'école
        Classe::where(function ($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))
                  ->orWhereNull('option_id');
            })
            ->findOrFail($request->classe_id);

        FraisClasse::create([
            'frais_id' => $request->frais_id,
            'classe_id' => $request->classe_id,
            'montant_specifique' => $request->montant_specifique,
            'annee_scolaire' => $request->annee_scolaire,
        ]);

        return redirect()->route('comptable.frais.index')
            ->with('success', 'Frais associé à la classe avec succès !');
    }

    /**
     * Supprimer une association frais ↔ classe.
     */
    public function fraisClasseDestroy($id)
    {
        $ecoleId = session('ecole_id');
        $fc = FraisClasse::whereHas('frais', fn($q) => $q->where('ecole_id', $ecoleId))
            ->findOrFail($id);
        $fc->delete();

        return redirect()->route('comptable.frais.index')
            ->with('success', 'Association frais-classe supprimée.');
    }

    /**
     * Enregistrer un nouveau frais (avec classe + montant + année).
     */
    public function fraisStore(Request $request)
    {
        $ecoleId = session('ecole_id');

        $request->validate([
            'intitule_frais' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'devise' => 'required|string|max:3',
            'classe_id' => 'required|exists:classes,id',
        ]);

        // Vérifier que la classe appartient à l'école
        Classe::where(function ($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))
                  ->orWhereNull('option_id');
            })
            ->findOrFail($request->classe_id);

        // Déterminer l'année scolaire courante
        $anneeCourante = date('Y') . '-' . (date('Y') + 1);

        Frais::create([
            'ecole_id' => $ecoleId,
            'intitule_frais' => $request->intitule_frais,
            'montant' => $request->montant,
            'devise' => strtoupper($request->devise),
            'classe_id' => $request->classe_id,
            'annee_scolaire' => $anneeCourante,
        ]);

        return redirect()->route('comptable.frais.index')
            ->with('success', 'Frais créé avec succès !');
    }

    /**
     * Supprimer un frais.
     */
    public function fraisDestroy($id)
    {
        $ecoleId = session('ecole_id');
        $frais = Frais::where('ecole_id', $ecoleId)->findOrFail($id);
        $frais->delete();

        return redirect()->route('comptable.frais.index')
            ->with('success', 'Frais supprimé.');
    }

    /**
     * Liste des paiements effectués.
     */
    public function paiementsIndex(Request $request)
    {
        $ecoleId = session('ecole_id');

        $query = Paiement::where('ecole_id', $ecoleId)
            ->with(['inscription.eleve', 'frais.classe', 'comptable']);

        if ($request->filled('classe_id')) {
            $query->whereHas('inscription', fn($q) => $q->where('classe_id', $request->classe_id));
        }
        if ($request->filled('frais_id')) {
            $query->where('frais_id', $request->frais_id);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('date_paiement', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_paiement', '<=', $request->date_fin);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_recu', 'like', "%{$search}%")
                  ->orWhereHas('inscription.eleve', function ($eq) use ($search) {
                      $eq->where('nom', 'like', "%{$search}%")
                         ->orWhere('postnom', 'like', "%{$search}%")
                         ->orWhere('code_matricule', 'like', "%{$search}%");
                  });
            });
        }

        $paiements = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $classes = Classe::where(function ($q) use ($ecoleId) {
                $q->whereHas('option', fn($qq) => $qq->where('ecole_id', $ecoleId))
                  ->orWhereNull('option_id');
            })
            ->orderBy('nom_classe')->get();
        $frais = Frais::where('ecole_id', $ecoleId)->with('classe')->orderBy('intitule_frais')->get();

        return view('comptable.paiements.index', compact('paiements', 'classes', 'frais'));
    }

    /**
     * Formulaire d'enregistrement d'un nouveau paiement.
     */
    public function paiementsCreate(Request $request)
    {
        $ecoleId = session('ecole_id');

        $eleves = Eleve::where('ecole_id', $ecoleId)
            ->whereHas('inscriptions', fn($q) => $q->where('statut', 'actif'))
            ->orderBy('nom')
            ->get();

        $frais = Frais::where('ecole_id', $ecoleId)->with('classe')->orderBy('intitule_frais')->get();

        $selectedEleve = null;
        $inscriptions = collect();

        if ($request->filled('eleve_id')) {
            $selectedEleve = Eleve::where('ecole_id', $ecoleId)->findOrFail($request->eleve_id);
            $inscriptions = Inscription::where('eleve_id', $selectedEleve->id)
                ->where('statut', 'actif')
                ->with('classe')
                ->get();
        }

        return view('comptable.paiements.create', compact('eleves', 'frais', 'selectedEleve', 'inscriptions'));
    }

    /**
     * Enregistrer un paiement.
     */
    public function paiementsStore(Request $request)
    {
        $ecoleId = session('ecole_id');
        $userId = Auth::id();

        $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'frais_id' => 'required|exists:frais,id',
            'montant_paye' => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|string|in:especes,cheque,virement_bancaire,depot_mobile',
        ]);

        // Vérifier que l'inscription est dans l'école
        $inscription = Inscription::where('ecole_id', $ecoleId)->findOrFail($request->inscription_id);

        // Générer un numéro de reçu unique
        $annee = date('Y');
        $count = Paiement::where('ecole_id', $ecoleId)->count() + 1;
        $numero_recu = "REC-{$annee}-{$ecoleId}-" . str_pad($count, 6, '0', STR_PAD_LEFT);

        Paiement::create([
            'ecole_id' => $ecoleId,
            'inscription_id' => $request->inscription_id,
            'frais_id' => $request->frais_id,
            'comptable_id' => $userId,
            'montant_paye' => $request->montant_paye,
            'date_paiement' => $request->date_paiement,
            'numero_recu' => $numero_recu,
            'mode_paiement' => $request->mode_paiement,
        ]);

        return redirect()->route('comptable.paiements.index')
            ->with('success', "Paiement enregistré avec succès ! Reçu n° {$numero_recu}");
    }

    /**
     * Afficher les détails d'un paiement (reçu).
     */
    public function paiementsShow($id)
    {
        $ecoleId = session('ecole_id');
        $paiement = Paiement::where('ecole_id', $ecoleId)
            ->with(['inscription.eleve', 'inscription.classe', 'frais.classe', 'comptable'])
            ->findOrFail($id);

        return view('comptable.paiements.show', compact('paiement'));
    }

    /**
     * Supprimer un paiement.
     */
    public function paiementsDestroy($id)
    {
        $ecoleId = session('ecole_id');
        $paiement = Paiement::where('ecole_id', $ecoleId)->findOrFail($id);
        $paiement->delete();

        return redirect()->route('comptable.paiements.index')
            ->with('success', 'Paiement supprimé.');
    }

    /**
     * Relevé des paiements par élève.
     */
    public function releveEleve($eleveId)
    {
        $ecoleId = session('ecole_id');
        $eleve = Eleve::where('ecole_id', $ecoleId)
            ->with(['inscriptions' => function ($q) {
                $q->with(['classe', 'paiements' => function ($pq) {
                    $pq->with('frais');
                }]);
            }])
            ->findOrFail($eleveId);

        $totalDu = 0;
        $totalPaye = 0;

        foreach ($eleve->inscriptions as $inscription) {
            // Total dû = somme des frais de la même classe + même année
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

        $solde = $totalDu - $totalPaye;

        return view('comptable.paiements.releve', compact('eleve', 'totalDu', 'totalPaye', 'solde'));
    }
}

