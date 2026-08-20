<?php

namespace App\Http\Controllers;

use App\Models\ConfigRappel;
use App\Models\RappelPaiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RappelController extends Controller
{
    /**
     * Afficher la configuration des rappels et les logs.
     */
    public function index()
    {
        $ecoleId = session('ecole_id');

        $config = ConfigRappel::firstOrCreate(
            ['ecole_id' => $ecoleId],
            [
                'actif' => true,
                'frequence' => 'hebdomadaire',
                'jour_envoi' => 'monday',
                'jour_du_mois' => 1,
                'heure_envoi' => 8,
                'email_actif' => true,
                'sms_actif' => false,
            ]
        );

        // Statistiques des rappels
        $stats = (object) [
            'total_rappels' => RappelPaiement::where('ecole_id', $ecoleId)->count(),
            'envoyes' => RappelPaiement::where('ecole_id', $ecoleId)->where('statut', 'envoye')->count(),
            'echoues' => RappelPaiement::where('ecole_id', $ecoleId)->where('statut', 'echoue')->count(),
            'en_attente' => RappelPaiement::where('ecole_id', $ecoleId)->where('statut', 'en_attente')->count(),
            'emails_envoyes' => RappelPaiement::where('ecole_id', $ecoleId)->where('email_envoye', true)->count(),
            'sms_envoyes' => RappelPaiement::where('ecole_id', $ecoleId)->where('sms_envoye', true)->count(),
        ];

        return view('comptable.rappels.index', compact('config', 'stats'));
    }

    /**
     * Mettre à jour la configuration des rappels.
     */
    public function updateConfig(Request $request)
    {
        $ecoleId = session('ecole_id');

        $validated = $request->validate([
            'actif' => 'boolean',
            'frequence' => 'required|in:hebdomadaire,mensuel,trimestriel,semestriel',
            'jour_envoi' => 'required_if:frequence,hebdomadaire|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'jour_du_mois' => 'required_if:frequence,mensuel|nullable|integer|min:1|max:31',
            'heure_envoi' => 'required|integer|min:0|max:23',
            'email_actif' => 'boolean',
            'sms_actif' => 'boolean',
            'message_personnalise' => 'nullable|string|max:500',
        ]);

        $config = ConfigRappel::where('ecole_id', $ecoleId)->firstOrFail();

        $config->update([
            'actif' => $request->boolean('actif', $config->actif),
            'frequence' => $validated['frequence'],
            'jour_envoi' => $validated['jour_envoi'] ?? $config->jour_envoi,
            'jour_du_mois' => $validated['jour_du_mois'] ?? $config->jour_du_mois,
            'heure_envoi' => $validated['heure_envoi'],
            'email_actif' => $request->boolean('email_actif', $config->email_actif),
            'sms_actif' => $request->boolean('sms_actif', $config->sms_actif),
            'message_personnalise' => $validated['message_personnalise'] ?? $config->message_personnalise,
        ]);

        return redirect()->route('comptable.rappels.index')
            ->with('success', 'Configuration des rappels mise à jour avec succès !');
    }

    /**
     * Afficher l'historique des rappels envoyés.
     */
    public function logs(Request $request)
    {
        $ecoleId = session('ecole_id');

        $query = RappelPaiement::where('ecole_id', $ecoleId)
            ->with(['inscription.eleve', 'inscription.classe', 'frais'])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('type_rappel')) {
            $query->where('type_rappel', $request->type_rappel);
        }
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => RappelPaiement::where('ecole_id', $ecoleId)->count(),
            'email' => RappelPaiement::where('ecole_id', $ecoleId)->where('email_envoye', true)->count(),
            'sms' => RappelPaiement::where('ecole_id', $ecoleId)->where('sms_envoye', true)->count(),
        ];

        return view('comptable.rappels.logs', compact('logs', 'stats'));
    }

    /**
     * Déclencher manuellement l'envoi des rappels.
     */
    public function declencher()
    {
        try {
            Artisan::call('rappels:envoyer');
            $output = Artisan::output();

            return redirect()->route('comptable.rappels.logs')
                ->with('success', 'Rappels envoyés avec succès ! Vérifiez les logs pour plus de détails.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'envoi des rappels : ' . $e->getMessage());
        }
    }
}

