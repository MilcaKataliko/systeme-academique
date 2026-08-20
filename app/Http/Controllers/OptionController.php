<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\Classe;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    // 1. Afficher la liste des options
    public function index()
    {
        $options = Option::withCount('classes')->get();
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
            'ecole_id' => session('ecole_id'),
        ]);

        return redirect()->route('options.index')->with('success', 'Option ajoutée avec succès !');
    }

    // 4. Supprimer une option (même si des classes y sont liées)
    public function destroy($id)
    {
        $option = Option::findOrFail($id);

        // Compter les classes liées avant suppression
        $nbClasses = $option->classes()->count();

        // ÉTAPE CRUCIALE : Dissocier les classes en mettant option_id = NULL
        // AVANT de supprimer l'option, pour éviter les erreurs de contrainte SQL
        if ($nbClasses > 0) {
            Classe::where('option_id', $id)->update(['option_id' => null]);
        }

        // Supprimer l'option (plus de contrainte bloquante)
        $option->delete();

        $message = "Option supprimée avec succès !";
        if ($nbClasses > 0) {
            $message .= " {$nbClasses} classe(s) liée(s) ont été dissociées de cette option.";
        }

        return redirect()->route('options.index')->with('success', $message);
    }
}
