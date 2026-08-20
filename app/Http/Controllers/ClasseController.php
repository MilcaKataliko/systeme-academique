<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classe;
use App\Models\Option;
use App\Models\Plan;
use App\Models\Inscription;
use App\Models\Frais;

class ClasseController extends Controller
{
    /**
     * Afficher la liste des classes.
     */
    public function index()
    {
        $ecoleId = session('ecole_id');

        // Récupérer les IDs des options de l'ecole
        $optionIds = Option::where('ecole_id', $ecoleId)->pluck('idOption');

        // Classes : soit liees a une option de l'ecole, soit sans option (7eme/8eme)
        $classes = Classe::with('option')
            ->where(function ($q) use ($optionIds) {
                $q->whereIn('option_id', $optionIds)
                  ->orWhereNull('option_id');
            })
            ->orderBy('niveau')
            ->orderBy('nom_classe')
            ->get()
            ->map(function ($classe) use ($ecoleId) {
                $classe->nb_inscriptions = Inscription::where('ecole_id', $ecoleId)
                    ->where('classe_id', $classe->id)
                    ->where('statut', 'actif')
                    ->count();
                $classe->nb_plans = Plan::where('classe_id', $classe->id)->count();
                return $classe;
            });

        $stats = (object) [
            'total' => $classes->count(),
            'total_eleves' => $classes->sum('nb_inscriptions'),
            'options' => Option::where('ecole_id', $ecoleId)->count(),
        ];

        $options = Option::where('ecole_id', $ecoleId)->orderBy('nomoption')->get();

        return view('directeur.classes.index', compact('classes', 'stats', 'options'));
    }

    /**
     * Enregistrer une nouvelle classe.
     */
    public function store(Request $request)
    {
        $ecoleId = session('ecole_id');

        $request->validate([
            'nom_classe' => 'required|string|max:50',
            'niveau'     => 'required|integer|in:7,8,1,2,3,4',
            'section'    => 'nullable|string|max:50',
            'option_id'  => 'nullable|exists:options,idOption',
        ]);

        $niveau = (int) $request->niveau;

        // Pour les niveaux 1ere a 4eme, l'option est obligatoire
        if (in_array($niveau, [1, 2, 3, 4]) && !$request->option_id) {
            return back()->with('error', "L'option est obligatoire pour les niveaux 1ere a 4eme.");
        }

        // Si option_id fourni, verifier qu'elle appartient bien a l'ecole
        if ($request->option_id) {
            $optionExists = Option::where('idOption', $request->option_id)
                ->where('ecole_id', $ecoleId)
                ->exists();

            if (!$optionExists) {
                return back()->with('error', 'Option invalide pour votre etablissement.');
            }
        }

        // Verifier unicite
        $query = Classe::where('nom_classe', $request->nom_classe)
            ->where('niveau', $niveau);

        if ($request->option_id) {
            $query->where('option_id', $request->option_id);
        } else {
            $query->whereNull('option_id');
        }

        if ($query->exists()) {
            return back()->with('error', 'Cette classe existe deja pour ce niveau.');
        }

        Classe::create($request->only(['nom_classe', 'niveau', 'section', 'option_id']));

        return redirect()->route('directeur.classes.index')->with('success', 'Classe creee avec succes !');
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit($id)
    {
        $ecoleId = session('ecole_id');
        $classe = Classe::with('option')->findOrFail($id);

        // Verifier que la classe appartient a l'ecole via son option
        if ($classe->option && $classe->option->ecole_id != $ecoleId) {
            abort(403, 'Acces non autorise.');
        }

        $options = Option::where('ecole_id', $ecoleId)->orderBy('nomoption')->get();

        return view('directeur.classes.edit', compact('classe', 'options'));
    }

    /**
     * Mettre a jour une classe.
     */
    public function update(Request $request, $id)
    {
        $ecoleId = session('ecole_id');
        $classe = Classe::with('option')->findOrFail($id);

        // Verifier l'appartenance a l'ecole
        if ($classe->option && $classe->option->ecole_id != $ecoleId) {
            abort(403, 'Acces non autorise.');
        }

        $request->validate([
            'nom_classe' => 'required|string|max:50',
            'niveau'     => 'required|integer|in:7,8,1,2,3,4',
            'section'    => 'nullable|string|max:50',
            'option_id'  => 'nullable|exists:options,idOption',
        ]);

        $niveau = (int) $request->niveau;

        // Pour les niveaux 1ere a 4eme, l'option est obligatoire
        if (in_array($niveau, [1, 2, 3, 4]) && !$request->option_id) {
            return back()->with('error', "L'option est obligatoire pour les niveaux 1ere a 4eme.");
        }

        $classe->update($request->only(['nom_classe', 'niveau', 'section', 'option_id']));

        return redirect()->route('directeur.classes.index')->with('success', 'Classe mise a jour avec succes.');
    }

    /**
     * Supprimer une classe (supprime d'abord les cours attribues et frais lies).
     */
    public function destroy($id)
    {
        $ecoleId = session('ecole_id');
        $classe = Classe::with('option')->findOrFail($id);

        // Verifier l'appartenance a l'ecole
        if ($classe->option && $classe->option->ecole_id != $ecoleId) {
            abort(403, 'Acces non autorise.');
        }

        // Verifier si des eleves sont inscrits (interdit pour ne pas perdre de donnees)
        if (Inscription::where('classe_id', $id)->exists()) {
            return back()->with('error', "Impossible : des eleves sont inscrits dans cette classe. Veuillez d'abord reafficcter les eleves.");
        }

        // Supprimer les plans (cours attribues) lies a cette classe avant de supprimer la classe
        $nbPlans = Plan::where('classe_id', $id)->count();
        if ($nbPlans > 0) {
            Plan::where('classe_id', $id)->delete();
        }

        // Supprimer les frais lies a cette classe
        $nbFrais = Frais::where('classe_id', $id)->count();
        if ($nbFrais > 0) {
            Frais::where('classe_id', $id)->delete();
        }

        $classe->delete();

        $message = "Classe supprimee avec succes.";
        if ($nbPlans > 0) {
            $message .= " {$nbPlans} cours(s) attribue(s) ont ete retires.";
        }

        return redirect()->route('directeur.classes.index')->with('success', $message);
    }
}
