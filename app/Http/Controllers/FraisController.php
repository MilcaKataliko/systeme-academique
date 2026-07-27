<?php

namespace App\Http\Controllers;

use App\Models\Frais;
use App\Models\FraisClasse;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FraisController extends Controller
{
    private function getEcoleId()
    {
        return session('ecole_id') ?? Auth::user()->ecole_id;
    }

    public function index(Request $request)
    {
        $ecoleId = $this->getEcoleId();
        $search = $request->input('search');

        $frais = Frais::where('ecole_id', $ecoleId)
            ->when($search, function ($query, $search) {
                return $query->where('intitule_frais', 'LIKE', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10);

        $classes = Classe::with('option')->where('ecole_id', $ecoleId)->get();
        $annees  = \App\Models\Annee::where('ecole_id', $ecoleId)->get();
        $fraisClasses = FraisClasse::with(['frais', 'classe'])->get();

        return view('proviseur.frais.index', compact('frais', 'classes', 'annees', 'fraisClasses', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'intitule_frais'   => 'required|string|max:255',
            'montant_standard' => 'required|numeric|min:0',
            'devise'           => 'required|string|max:3',
        ]);

        Frais::create([
            'ecole_id'        => $this->getEcoleId(),
            'intitule_frais'  => $request->intitule_frais,
            'montant_standard' => $request->montant_standard,
            'devise'          => $request->devise,
        ]);

        return back()->with('success', 'Frais ajouté avec succès !');
    }

    public function update(Request $request, $id)
    {
        $frais = Frais::findOrFail($id);

        $request->validate([
            'intitule_frais'   => 'required|string|max:255',
            'montant_standard' => 'required|numeric|min:0',
            'devise'           => 'required|string|max:3',
        ]);

        $frais->update($request->only(['intitule_frais', 'montant_standard', 'devise']));

        return back()->with('success', 'Frais mis à jour avec succès !');
    }

    public function destroy($id)
    {
        $frais = Frais::findOrFail($id);
        $frais->delete();

        return back()->with('success', 'Frais supprimé avec succès !');
    }

    // Association frais-classe
    public function storeFraisClasse(Request $request)
    {
        $request->validate([
            'classe_id'         => 'required|exists:classes,id',
            'frais_id'          => 'required|exists:frais,id',
            'montant_specifique' => 'required|numeric|min:0',
            'annee_scolaire'    => 'required|string|max:50',
        ]);

        FraisClasse::create($request->all());

        return back()->with('success', 'Frais associé à la classe avec succès !');
    }

    public function destroyFraisClasse($id)
    {
        $fc = FraisClasse::findOrFail($id);
        $fc->delete();

        return back()->with('success', 'Association frais-classe supprimée !');
    }
}

