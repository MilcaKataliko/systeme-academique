<?php

namespace App\Http\Controllers;

use App\Models\Annee;
use Illuminate\Http\Request;

class AnneeController extends Controller
{
    // 1. Afficher la liste des années scolaires
    public function index()
    {
        $annees = Annee::all();
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
            'anneescolaire' => 'required|string|max:255',
        ]);

        Annee::create([
            'anneescolaire' => $request->anneescolaire,
        ]);

        return redirect()->route('annees.index')->with('success', 'Année scolaire ajoutée avec succès !');
    }
}