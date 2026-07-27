<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Inscription;
use App\Models\Frais;
use App\Models\Eleve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaiementController extends Controller
{
    private function getEcoleId()
    {
        return session('ecole_id') ?? Auth::user()->ecole_id;
    }

    public function index(Request $request)
    {
        $ecoleId = $this->getEcoleId();
        $search = $request->input('search');

        $paiements = Paiement::with(['inscription.eleve', 'frais', 'comptable'])
            ->where('ecole_id', $ecoleId)
            ->when($search, function ($query, $search) {
                return $query->where('numero_recu', 'LIKE', "%{$search}%")
                             ->orWhereHas('inscription.eleve', function ($q) use ($search) {
                                 $q->where('nom', 'LIKE', "%{$search}%")
                                   ->orWhere('postnom', 'LIKE', "%{$search}%")
                                   ->orWhere('prenom', 'LIKE', "%{$search}%");
                             });
            })
            ->latest('id')
            ->paginate(10);

        $inscriptions = Inscription::with('eleve', 'classe')
            ->where('ecole_id', $ecoleId)
            ->where('statut', 'actif')
            ->get();

        $frais = Frais::where('ecole_id', $ecoleId)->get();

        return view('comptable.paiements.index', compact('paiements', 'inscriptions', 'frais', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'frais_id'       => 'required|exists:frais,id',
            'montant_paye'   => 'required|numeric|min:0',
            'date_paiement'  => 'required|date',
            'mode_paiement'  => 'required|string|in:especes,cheque,virement,carte',
        ]);

        // Générer un numéro de reçu unique
        $numeroRecu = 'RECU-' . strtoupper(uniqid());

        Paiement::create([
            'ecole_id'       => $this->getEcoleId(),
            'inscription_id' => $request->inscription_id,
            'frais_id'       => $request->frais_id,
            'comptable_id'   => Auth::id(),
            'montant_paye'   => $request->montant_paye,
            'date_paiement'  => $request->date_paiement,
            'numero_recu'    => $numeroRecu,
            'mode_paiement'  => $request->mode_paiement,
        ]);

        return back()->with('success', 'Paiement enregistré avec succès ! N° Reçu : ' . $numeroRecu);
    }

    public function update(Request $request, $id)
    {
        $paiement = Paiement::findOrFail($id);

        $request->validate([
            'montant_paye'  => 'required|numeric|min:0',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|string|in:especes,cheque,virement,carte',
        ]);

        $paiement->update($request->only(['montant_paye', 'date_paiement', 'mode_paiement']));

        return back()->with('success', 'Paiement mis à jour avec succès !');
    }

    public function destroy($id)
    {
        $paiement = Paiement::findOrFail($id);
        $paiement->delete();

        return back()->with('success', 'Paiement supprimé avec succès !');
    }

    /**
     * Reçu d'un paiement
     */
    public function recu($id)
    {
        $paiement = Paiement::with(['inscription.eleve', 'inscription.classe', 'frais', 'comptable'])
            ->findOrFail($id);

        return view('comptable.paiements.recu', compact('paiement'));
    }
}

