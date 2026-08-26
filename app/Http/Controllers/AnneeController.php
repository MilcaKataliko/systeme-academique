<?php

namespace App\Http\Controllers;

use App\Models\Annee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnneeController extends Controller
{
    // 1. Afficher la liste des années scolaires
    public function index()
    {
        $ecoleId = session('ecole_id');
        $annees = Annee::where('ecole_id', $ecoleId)
            ->orderByDesc('anneescolaire')
            ->get();
        return view('annees.index', compact('annees'));
    }

    // 2. Afficher le formulaire de création
    public function create()
    {
        return view('annees.create');
    }

    // 3. Enregistrer l'année dans la base de données
    public function store(Request $request)
    {
        $request->validate([
            'anneescolaire' => [
                'required', 'regex:/^\d{4}-\d{4}$/',
                Rule::unique('annees', 'anneescolaire')->where('ecole_id', session('ecole_id')),
            ],
        ]);

        Annee::create([
            'anneescolaire' => $request->anneescolaire,
            'ecole_id' => session('ecole_id'),
        ]);

        return redirect()->route('annees.index')->with('success', 'Année scolaire ajoutée avec succès !');
    }

    public function edit($id)
    {
        $annee = Annee::where('ecole_id', session('ecole_id'))->findOrFail($id);

        return view('annees.edit', compact('annee'));
    }

    public function update(Request $request, $id)
    {
        $annee = Annee::where('ecole_id', session('ecole_id'))->findOrFail($id);

        $request->validate([
            'anneescolaire' => [
                'required', 'regex:/^\d{4}-\d{4}$/',
                Rule::unique('annees', 'anneescolaire')
                    ->where('ecole_id', session('ecole_id'))
                    ->ignore($annee->idAnnee, 'idAnnee'),
            ],
        ]);

        $annee->update(['anneescolaire' => $request->anneescolaire]);

        return redirect()->route('annees.index')->with('success', 'Année scolaire modifiée avec succès !');
    }

    public function destroy($id)
    {
        $annee = Annee::where('ecole_id', session('ecole_id'))->findOrFail($id);
        $annee->delete();

        return redirect()->route('annees.index')->with('success', 'Année scolaire supprimée avec succès !');
    }
}