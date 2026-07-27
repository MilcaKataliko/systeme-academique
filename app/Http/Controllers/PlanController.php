<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Cour;
use App\Models\Classe;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    private function getEcoleId()
    {
        return session('ecole_id') ?? Auth::user()->ecole_id;
    }

    public function index(Request $request)
    {
        $ecoleId = $this->getEcoleId();
        $search = $request->input('search');

        $plans = Plan::with(['classe', 'cour', 'enseignant'])
            ->whereHas('classe', function ($q) use ($ecoleId) {
                $q->where('ecole_id', $ecoleId);
            })
            ->when($search, function ($query, $search) {
                return $query->whereHas('cour', function ($q) use ($search) {
                    $q->where('nom_cours', 'LIKE', "%{$search}%");
                })->orWhereHas('classe', function ($q) use ($search) {
                    $q->where('nom_classe', 'LIKE', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(10);

        $cours      = Cour::where('ecole_id', $ecoleId)->get();
        $classes    = Classe::with('option')->where('ecole_id', $ecoleId)->get();
        $enseignants = Enseignant::where('ecole_id', $ecoleId)->get();
        $annees     = \App\Models\Annee::where('ecole_id', $ecoleId)->get();

        return view('proviseur.plans.index', compact('plans', 'cours', 'classes', 'enseignants', 'annees', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'classe_id'       => 'required|exists:classes,id',
            'cours_id'        => 'required|exists:cours,id',
            'enseignant_id'   => 'nullable|exists:enseignants,id',
            'maxima_periode'  => 'nullable|integer|min:0|max:100',
            'maxima_examen'   => 'nullable|integer|min:0|max:100',
            'annee_scolaire'  => 'required|string|max:50',
        ]);

        // Vérifier unicité (même cours + même classe + même année)
        $existe = Plan::where('classe_id', $request->classe_id)
            ->where('cours_id', $request->cours_id)
            ->where('annee_scolaire', $request->annee_scolaire)
            ->exists();

        if ($existe) {
            return back()->withErrors(['cours_id' => 'Ce cours est déjà planifié pour cette classe cette année.']);
        }

        Plan::create($request->all());

        return back()->with('success', 'Planification ajoutée avec succès !');
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $request->validate([
            'classe_id'       => 'required|exists:classes,id',
            'cours_id'        => 'required|exists:cours,id',
            'enseignant_id'   => 'nullable|exists:enseignants,id',
            'maxima_periode'  => 'nullable|integer|min:0|max:100',
            'maxima_examen'   => 'nullable|integer|min:0|max:100',
            'annee_scolaire'  => 'required|string|max:50',
        ]);

        $plan->update($request->all());

        return back()->with('success', 'Planification mise à jour avec succès !');
    }

    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return back()->with('success', 'Planification supprimée avec succès !');
    }
}

