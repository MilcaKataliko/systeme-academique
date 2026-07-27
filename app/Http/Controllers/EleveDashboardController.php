<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\Cote;
use App\Models\Plan;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EleveDashboardController extends Controller
{
    public function dashboard()
    {
        $ecoleId = session('ecole_id') ?? Auth::user()->ecole_id;

        // Trouver l'élève connecté via l'utilisateur
        $eleve = \App\Models\Eleve::where('user_id', Auth::id())->first();

        if (!$eleve) {
            return back()->withErrors(['erreur' => 'Aucun profil élève trouvé pour votre compte.']);
        }

        $inscriptions = Inscription::with('classe.option')
            ->where('eleve_id', $eleve->id)
            ->where('statut', 'actif')
            ->get();

        $totalPaiements = Paiement::whereIn('inscription_id', $inscriptions->pluck('id'))
            ->sum('montant_paye');

        $derniersPaiements = Paiement::with('frais')
            ->whereIn('inscription_id', $inscriptions->pluck('id'))
            ->latest()
            ->take(5)
            ->get();

        return view('eleve.dashboard', compact(
            'eleve', 'inscriptions', 'totalPaiements', 'derniersPaiements'
        ));
    }

    public function finances()
    {
        $eleve = \App\Models\Eleve::where('user_id', Auth::id())->first();

        if (!$eleve) {
            return back()->withErrors(['erreur' => 'Profil élève introuvable.']);
        }

        $inscriptions = Inscription::with('classe.option')
            ->where('eleve_id', $eleve->id)
            ->get();

        $paiements = Paiement::with(['frais', 'inscription.classe'])
            ->whereIn('inscription_id', $inscriptions->pluck('id'))
            ->latest()
            ->paginate(10);

        return view('eleve.finances', compact('eleve', 'inscriptions', 'paiements'));
    }

    public function bulletin(Request $request)
    {
        $eleve = \App\Models\Eleve::where('user_id', Auth::id())->first();

        if (!$eleve) {
            return back()->withErrors(['erreur' => 'Profil élève introuvable.']);
        }

        $anneeScolaire = $request->input('annee_scolaire');

        $inscriptions = Inscription::with('classe.option')
            ->where('eleve_id', $eleve->id)
            ->when($anneeScolaire, function ($q) use ($anneeScolaire) {
                $q->where('annee_scolaire', $anneeScolaire);
            })
            ->get();

        $periodes = Periode::where('ecole_id', session('ecole_id'))->get();

        $bulletins = collect();

        foreach ($inscriptions as $inscription) {
            $plans = Plan::with('cour')
                ->where('classe_id', $inscription->classe_id)
                ->get();

            foreach ($plans as $plan) {
                foreach ($periodes as $periode) {
                    $cote = Cote::where('inscription_id', $inscription->id)
                        ->where('plan_id', $plan->id)
                        ->where('periode_id', $periode->id)
                        ->first();

                    if ($cote) {
                        $bulletins->push((object) [
                            'cours'          => $plan->cour->nom_cours,
                            'classe'         => $inscription->classe->nom_classe,
                            'option'         => $inscription->classe->option->nomoption ?? 'Générale',
                            'periode'        => $periode->nom_periode,
                            'max_periode'    => $plan->maxima_periode,
                            'max_examen'     => $plan->maxima_examen,
                            'points'         => $cote->points_obtenus,
                            'annee_scolaire' => $inscription->annee_scolaire,
                        ]);
                    }
                }
            }
        }

        $anneesDisponibles = Inscription::where('eleve_id', $eleve->id)
            ->pluck('annee_scolaire')
            ->unique();

        return view('eleve.bulletin', compact('bulletins', 'anneesDisponibles', 'anneeScolaire', 'eleve'));
    }
}

