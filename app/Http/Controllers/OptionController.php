<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    // 1. Afficher la liste des options
    public function index()
    {
        $options = Option::all();
        return view('options.index', compact('options'));
    }

    // 2. Afficher le formulaire de création
    public function create()
    {
        return view('options.create');
    }

    // 3. Enregistrer l'option dans la base de données
    public function store(Request $request)
    {
        $request->validate([
            'nomoption' => 'required|string|max:255',
            'sigle' => 'required|string|max:50',
        ]);

        Option::create([
            'nomoption' => $request->nomoption,
            'sigle' => $request->sigle,
        ]);

        return redirect()->route('options.index')->with('success', 'Option ajoutée avec succès !');
    }
}