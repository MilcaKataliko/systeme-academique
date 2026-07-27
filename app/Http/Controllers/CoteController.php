<?php

namespace App\Http\Controllers;

use App\Models\Cote;
use App\Models\Plan;
use App\Models\Inscription;
use App\Models\Periode;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoteController extends Controller
{
    public function index(Request $request)
    {
        $ecoleId = session('ecole_id') ?? Auth::user()->ecole_id;

        // Trouver l'enseignant connecté
        $enseignant = Enseignant::where('user_id', Auth::id())->first();

        if (!$enseignant) {
            return back()->withErrors(['erreur' => 'Vous devez être un enseignant pour accéder à cette page.']);
        }

        $search = $request->input('search');
        $planId = $request->input('plan_id');
        $periodeId = $request->input('periode_id');

        // Plans assignés à cet enseignant
        $plans = Plan::with(['classe', 'cour'])
            ->where('enseignant_id', $enseignant->id)
            ->get();

        $periodes = Periode::where('ecole_id', $ecoleId)->get();

        $cotes = collect();
        $selectedPlan = null;
        $selectedPeriode = null;

        if ($planId && $periodeId) {
            $selectedPlan = Plan::with(['classe', 'cour'])->find($planId);
            $selectedPeriode = Periode::find($periodeId);

            // Récupérer les inscriptions pour cette classe
            $inscriptions = Inscription::with('eleve')
                ->where('classe_id', $selectedPlan->classe_id)
                ->where('statut', 'actif')
                ->get();

            // Récupérer les cotes existantes
            $cotesExistantes = Cote::where('plan_id', $planId)
                ->where('periode_id', $periodeId)
                ->get()
                ->keyBy('inscription_id');

            // Préparer les données
            $cotes = $inscriptions->map(function ($inscription) use ($cotesExistantes, $planId, $periodeId) {
                $cote = $cotesExistantes->get($inscription->id);
                return (object) [
                    'inscription_id' => $inscription->id,
                    'eleve_nom'      => $inscription->eleve->nom_complet,
                    'cote_id'        => $cote->id ?? null,
                    'points_obtenus' => $cote->points_obtenus ?? null,
                    'plan_id'        => $planId,
                    'periode_id'     => $periodeId,
                ];
            })->filter();
        }

        return view('enseignant.cotes.index', compact(
            'plans', 'periodes', 'cotes', 'search',
            'selectedPlan', 'selectedPeriode', 'planId', 'periodeId'
        ));
    }

    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'plan_id'        => 'required|exists:plans,id',
            'periode_id'     => 'required|exists:periodes,id',
            'points_obtenus' => 'required|numeric|min:0|max:100',
        ]);

        $cote = Cote::updateOrCreate(
            [
                'inscription_id' => $request->inscription_id,
                'plan_id'        => $request->plan_id,
                'periode_id'     => $request->periode_id,
            ],
            [
                'points_obtenus' => $request->points_obtenus,
                'encode_par'     => Auth::id(),
            ]
        );

        return back()->with('success', 'Cote enregistrée avec succès !');
    }

    /**
     * Saisie multiple des cotes
     */
    public function storeMultiple(Request $request)
    {
        $request->validate([
            'plan_id'    => 'required|exists:plans,id',
            'periode_id' => 'required|exists:periodes,id',
            'cotes'      => 'required|array',
            'cotes.*.inscription_id' => 'required|exists:inscriptions,id',
            'cotes.*.points_obtenus' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($request->cotes as $coteData) {
            Cote::updateOrCreate(
                [
                    'inscription_id' => $coteData['inscription_id'],
                    'plan_id'        => $request->plan_id,
                    'periode_id'     => $request->periode_id,
                ],
                [
                    'points_obtenus' => $coteData['points_obtenus'],
                    'encode_par'     => Auth::id(),
                ]
            );
        }

        return back()->with('success', 'Toutes les cotes ont été enregistrées avec succès !');
    }
}

