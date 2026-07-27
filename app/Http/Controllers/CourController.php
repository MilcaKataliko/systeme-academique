<?php

namespace App\Http\Controllers;

use App\Models\Cour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourController extends Controller
{
    private function getEcoleId()
    {
        return session('ecole_id') ?? Auth::user()->ecole_id;
    }

    public function index(Request $request)
    {
        $ecoleId = $this->getEcoleId();
        $search = $request->input('search');

        $cours = Cour::where('ecole_id', $ecoleId)
            ->when($search, function ($query, $search) {
                return $query->where('nom_cours', 'LIKE', "%{$search}%")
                             ->orWhere('code_cours', 'LIKE', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10);

        return view('proviseur.cours.index', compact('cours', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_cours'  => 'required|string|max:255',
            'code_cours' => 'nullable|string|max:50',
        ]);

        Cour::create([
            'ecole_id'   => $this->getEcoleId(),
            'nom_cours'  => $request->nom_cours,
            'code_cours' => $request->code_cours,
        ]);

        return back()->with('success', 'Cours ajouté avec succès !');
    }

    public function update(Request $request, $id)
    {
        $cour = Cour::findOrFail($id);

        $request->validate([
            'nom_cours'  => 'required|string|max:255',
            'code_cours' => 'nullable|string|max:50',
        ]);

        $cour->update($request->only(['nom_cours', 'code_cours']));

        return back()->with('success', 'Cours mis à jour avec succès !');
    }

    public function destroy($id)
    {
        $cour = Cour::findOrFail($id);
        $cour->delete();

        return back()->with('success', 'Cours supprimé avec succès !');
    }
}

