<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InscriptionController extends Controller
{
    private function getEcoleId()
    {
        return session('ecole_id') ?? Auth::user()->ecole_id;
    }

    public function index(Request $request)
    {
        $ecoleId = $this->getEcoleId();
        $search = $request->input('search');

        $inscriptions = Inscription::with(['eleve', 'classe'])
            ->where('ecole_id', $ecoleId)
            ->when($search, function ($query, $search) {
                return $query->whereHas('eleve', function ($q) use ($search) {
                    $q->where('nom', 'LIKE', "%{$search}%")
                      ->orWhere('postnom', 'LIKE', "%{$search}%")
                      ->orWhere('prenom', 'LIKE', "%{$search}%");
                })->orWhere('annee_scolaire', 'LIKE', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10);

        $eleves  = Eleve::where('ecole_id', $ecoleId)->get();
        $classes = Classe::with('option')->where('ecole_id', $ecoleId)->get();
        $annees  = \App\Models\Annee::where('ecole_id', $ecoleId)->get();

        return view('proviseur.inscriptions.index', compact('inscriptions', 'eleves', 'classes', 'annees', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'eleve_id'      => 'required|exists:eleves,id',
            'classe_id'     => 'required|exists:classes,id',
            'annee_scolaire' => 'required|string|max:50',
        ]);

        // Vérifier que l'élève n'est pas déjà inscrit pour cette année
        $existe = Inscription::where('eleve_id', $request->eleve_id)
            ->where('annee_scolaire', $request->annee_scolaire)
            ->exists();

        if ($existe) {
            return back()->withErrors(['eleve_id' => 'Cet élève est déjà inscrit pour cette année scolaire.']);
        }

        Inscription::create([
            'ecole_id'      => $this->getEcoleId(),
            'eleve_id'      => $request->eleve_id,
            'classe_id'     => $request->classe_id,
            'annee_scolaire' => $request->annee_scolaire,
            'statut'        => 'actif',
        ]);

        return back()->with('success', 'Inscription enregistrée avec succès !');
    }

    public function update(Request $request, $id)
    {
        $inscription = Inscription::findOrFail($id);

        $request->validate([
            'classe_id'     => 'required|exists:classes,id',
            'annee_scolaire' => 'required|string|max:50',
            'statut'        => 'required|in:actif,inactif,termine',
        ]);

        $inscription->update($request->only(['classe_id', 'annee_scolaire', 'statut']));

        return back()->with('success', 'Inscription mise à jour avec succès !');
    }

    public function destroy($id)
    {
        $inscription = Inscription::findOrFail($id);
        $inscription->delete();

        return back()->with('success', 'Inscription supprimée avec succès !');
    }
}

