<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriodeController extends Controller
{
    private function getEcoleId()
    {
        return session('ecole_id') ?? Auth::user()->ecole_id;
    }

    public function index(Request $request)
    {
        $ecoleId = $this->getEcoleId();
        $search = $request->input('search');

        $periodes = Periode::where('ecole_id', $ecoleId)
            ->when($search, function ($query, $search) {
                return $query->where('nom_periode', 'LIKE', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10);

        return view('proviseur.periodes.index', compact('periodes', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_periode' => 'required|string|max:255',
        ]);

        Periode::create([
            'ecole_id'     => $this->getEcoleId(),
            'nom_periode'  => $request->nom_periode,
            'est_cloturee' => $request->boolean('est_cloturee'),
        ]);

        return back()->with('success', 'Période ajoutée avec succès !');
    }

    public function update(Request $request, $id)
    {
        $periode = Periode::findOrFail($id);

        $request->validate([
            'nom_periode' => 'required|string|max:255',
        ]);

        $periode->update([
            'nom_periode'  => $request->nom_periode,
            'est_cloturee' => $request->boolean('est_cloturee'),
        ]);

        return back()->with('success', 'Période mise à jour avec succès !');
    }

    public function destroy($id)
    {
        $periode = Periode::findOrFail($id);
        $periode->delete();

        return back()->with('success', 'Période supprimée avec succès !');
    }
}

