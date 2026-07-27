<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use Illuminate\Http\Request;

class EleveController extends Controller
{
    /**
     * Liste des élèves + Moteur de recherche
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $eleves = Eleve::when($search, function ($query, $search) {
            return $query->where('nom', 'LIKE', "%{$search}%")
                         ->orWhere('postnom', 'LIKE', "%{$search}%")
                         ->orWhere('prenom', 'LIKE', "%{$search}%");
        })
        ->latest('id')
        ->paginate(10);

        return view('proviseur.eleves.index', compact('eleves', 'search'));
    }

    /**
     * Enregistrer un nouvel élève
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom'              => 'required|string|max:255',
            'postnom'          => 'required|string|max:255',
            'prenom'           => 'nullable|string|max:255',
            'genre'            => 'required|in:M,F',
            'date_naissance'   => 'nullable|date',
            'lieu_naissance'   => 'nullable|string|max:255',
        ]);

        Eleve::create([
            'ecole_id'         => session('ecole_id') ?? auth()->user()->ecole_id,
            'nom'              => $request->nom,
            'postnom'          => $request->postnom,
            'prenom'           => $request->prenom,
            'genre'            => $request->genre,
            'date_naissance'   => $request->date_naissance,
            'lieu_naissance'   => $request->lieu_naissance,
        ]);

        return back()->with('success', 'Élève enregistré avec succès !');
    }

    /**
     * Mettre à jour les informations d'un élève
     */
    public function update(Request $request, $id)
    {
        $eleve = Eleve::findOrFail($id);

        $request->validate([
            'nom'              => 'required|string|max:255',
            'postnom'          => 'required|string|max:255',
            'prenom'           => 'nullable|string|max:255',
            'genre'            => 'required|in:M,F',
            'date_naissance'   => 'nullable|date',
            'lieu_naissance'   => 'nullable|string|max:255',
        ]);

        $eleve->update($request->only(['nom', 'postnom', 'prenom', 'genre', 'date_naissance', 'lieu_naissance']));

        return back()->with('success', 'Informations de l\'élève mises à jour !');
    }

    /**
     * Supprimer un élève
     */
    public function destroy($id)
    {
        $eleve = Eleve::findOrFail($id);
        $eleve->delete();

        return back()->with('success', 'Élève supprimé avec succès !');
    }
}