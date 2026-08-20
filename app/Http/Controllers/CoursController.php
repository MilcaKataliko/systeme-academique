<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cours;

class CoursController extends Controller
{
    /**
     * Afficher la liste des cours (matières).
     */
    public function index()
    {
        $ecoleId = session('ecole_id');

        $cours = Cours::where('ecole_id', $ecoleId)
            ->orderBy('nom_cours')
            ->get();

        // Compter le nombre de cours attribués à au moins une classe (via plans)
        $attribues = \App\Models\Plan::whereHas('cours', fn($q) => $q->where('ecole_id', $ecoleId))
            ->distinct('cours_id')
            ->count('cours_id');

        $stats = (object) [
            'total' => $cours->count(),
            'attribues' => $attribues,
        ];

        return view('directeur.cours.index', compact('cours', 'stats'));
    }

    /**
     * Enregistrer un nouveau cours.
     */
    public function store(Request $request)
    {
        $ecoleId = session('ecole_id');

        $request->validate([
            'nom_cours'  => 'required|string|max:255',
            'code_cours' => 'nullable|string|max:20',
        ]);

        // Vérifier l'unicité du nom pour cette école
        $existe = Cours::where('ecole_id', $ecoleId)
            ->where('nom_cours', $request->nom_cours)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Ce cours existe déjà dans votre établissement.');
        }

        Cours::create([
            'ecole_id'   => $ecoleId,
            'nom_cours'  => $request->nom_cours,
            'code_cours' => $request->code_cours,
        ]);

        return redirect()->route('directeur.cours.index')->with('success', 'Cours créé avec succès !');
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit($id)
    {
        $ecoleId = session('ecole_id');
        $cours = Cours::where('ecole_id', $ecoleId)->findOrFail($id);

        return view('directeur.cours.edit', compact('cours'));
    }

    /**
     * Mettre à jour un cours.
     */
    public function update(Request $request, $id)
    {
        $ecoleId = session('ecole_id');
        $cours = Cours::where('ecole_id', $ecoleId)->findOrFail($id);

        $request->validate([
            'nom_cours'  => 'required|string|max:255',
            'code_cours' => 'nullable|string|max:20',
        ]);

        // Vérifier l'unicité (sauf pour ce cours même)
        $existe = Cours::where('ecole_id', $ecoleId)
            ->where('nom_cours', $request->nom_cours)
            ->where('id', '!=', $id)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Un autre cours porte déjà ce nom dans votre établissement.');
        }

        $cours->update($request->only(['nom_cours', 'code_cours']));

        return redirect()->route('directeur.cours.index')->with('success', 'Cours mis à jour avec succès.');
    }

    /**
     * Supprimer un cours.
     */
    public function destroy($id)
    {
        $ecoleId = session('ecole_id');
        $cours = Cours::where('ecole_id', $ecoleId)->findOrFail($id);

        // Vérifier si le cours est utilisé dans des plans d'attribution
        if ($cours->plans()->exists()) {
            return back()->with('error', 'Impossible : ce cours est déjà attribué à des classes. Supprimez d\'abord les attributions.');
        }

        $cours->delete();
        return redirect()->route('directeur.cours.index')->with('success', 'Cours supprimé avec succès.');
    }
}
