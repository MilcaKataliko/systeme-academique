<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Annee;
use App\Models\Classe;
use App\Models\Option;
use App\Models\Eleve;
use App\Models\Cour;
use App\Models\Periode;
use App\Models\Inscription;
use App\Models\Frais;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class ProviseurController extends Controller
{
    public function dashboard()
    {
        $ecoleId = $this->getEcoleId();

        $totalAnnees      = Annee::where('ecole_id', $ecoleId)->count();
        $totalClasses     = Classe::where('ecole_id', $ecoleId)->count();
        $totalEleves      = Eleve::where('ecole_id', $ecoleId)->count();
        $totalCours       = Cour::where('ecole_id', $ecoleId)->count();
        $totalPeriodes    = Periode::where('ecole_id', $ecoleId)->count();
        $totalInscriptions = Inscription::where('ecole_id', $ecoleId)->count();
        $totalFrais       = Frais::where('ecole_id', $ecoleId)->count();
        $totalPlans       = Plan::whereHas('classe', function ($q) use ($ecoleId) {
                                $q->where('ecole_id', $ecoleId);
                            })->count();

        return view('proviseur.dashboard', compact(
            'totalAnnees', 'totalClasses', 'totalEleves', 'totalCours',
            'totalPeriodes', 'totalInscriptions', 'totalFrais', 'totalPlans'
        ));
    }

    /**
     * Récupère l'ID de l'école active
     */
    private function getEcoleId()
    {
        return session('ecole_id') ?? Auth::user()->ecole_id;
    }

    /**
     * Liste des options de l'école
     */
    public function indexOptions()
    {
        $ecoleId = $this->getEcoleId();
        $options = Option::where('ecole_id', $ecoleId)->get();

        return view('proviseur.options.index', compact('options'));
    }

    /**
     * Enregistrer une nouvelle option
     */
    public function storeOption(Request $request)
    {
        $request->validate([
            'nom_option'  => ['required', 'string', 'max:255'],
            'code_option' => ['nullable', 'string', 'max:10'],
        ]);

        $ecoleId = $this->getEcoleId();

        if (!$ecoleId) {
            return back()->withErrors(['erreur' => 'Impossible de détecter votre école. Veuillez vous reconnecter.']);
        }

        Option::create([
            'ecole_id'  => $ecoleId,
            'nomoption' => $request->nom_option,
            'sigle'     => $request->code_option ? strtoupper($request->code_option) : null,
        ]);

        return back()->with('success', 'Option ajoutée avec succès !');
    }

    /**
     * Liste des années scolaires de l'école
     */
    public function indexAnnees()
    {
        $ecoleId = $this->getEcoleId();
        $annees = Annee::where('ecole_id', $ecoleId)->latest('idAnnee')->get();

        return view('proviseur.annees.index', compact('annees'));
    }

    /**
     * Enregistrer une nouvelle année scolaire
     */
    public function storeAnnee(Request $request)
    {
        $request->validate([
            'anneescolaire' => ['required', 'string', 'max:255'],
        ]);

        $ecoleId = $this->getEcoleId();

        if (!$ecoleId) {
            return back()->withErrors(['erreur' => 'Impossible de détecter votre école. Veuillez vous reconnecter.']);
        }

        Annee::create([
            'ecole_id'     => $ecoleId,
            'anneescolaire' => $request->anneescolaire,
        ]);

        return back()->with('success', 'Année scolaire ajoutée avec succès !');
    }

    /**
     * Supprimer une année scolaire
     */
    public function destroyAnnee($id)
    {
        $annee = Annee::findOrFail($id);
        $annee->delete();

        return back()->with('success', 'Année scolaire supprimée avec succès !');
    }

    // ===================== GESTION DES CLASSES =====================

    /**
     * Liste des classes de l'école
     */
    public function indexClasses()
    {
        $ecoleId = $this->getEcoleId();
        $classes = Classe::with('option')
                        ->where('ecole_id', $ecoleId)
                        ->latest('id')
                        ->get();
        $options = Option::where('ecole_id', $ecoleId)->get();

        return view('proviseur.classes.index', compact('classes', 'options'));
    }

    /**
     * Enregistrer une nouvelle classe
     */
    public function storeClasse(Request $request)
    {
        $request->validate([
            'nom_classe'  => ['required', 'string', 'max:255'],
            'niveau'      => ['required', 'string', 'max:50'],
            'section'     => ['nullable', 'string', 'max:100'],
            'option_id'   => ['nullable', 'exists:options,idOption'],
            'effectif_max'=> ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $ecoleId = $this->getEcoleId();

        if (!$ecoleId) {
            return back()->withErrors(['erreur' => 'Impossible de détecter votre école. Veuillez vous reconnecter.']);
        }

        Classe::create([
            'ecole_id'     => $ecoleId,
            'option_id'    => $request->option_id,
            'nom_classe'   => $request->nom_classe,
            'niveau'       => $request->niveau,
            'section'      => $request->section,
            'effectif_max' => $request->effectif_max ?? 50,
        ]);

        return back()->with('success', 'Classe ajoutée avec succès !');
    }

    /**
     * Mettre à jour une classe
     */
    public function updateClasse(Request $request, $id)
    {
        $classe = Classe::findOrFail($id);

        $request->validate([
            'nom_classe'  => ['required', 'string', 'max:255'],
            'niveau'      => ['required', 'string', 'max:50'],
            'section'     => ['nullable', 'string', 'max:100'],
            'option_id'   => ['nullable', 'exists:options,idOption'],
            'effectif_max'=> ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $classe->update($request->only(['nom_classe', 'niveau', 'section', 'option_id', 'effectif_max']));

        return back()->with('success', 'Classe mise à jour avec succès !');
    }

    /**
     * Supprimer une classe
     */
    public function destroyClasse($id)
    {
        $classe = Classe::findOrFail($id);
        $classe->delete();

        return back()->with('success', 'Classe supprimée avec succès !');
    }
}
