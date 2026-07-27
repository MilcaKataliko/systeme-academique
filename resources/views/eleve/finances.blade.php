<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situation Financière — Élève</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-xl font-black text-white">Situation Financière</h1>
                <p class="text-xs text-slate-400">{{ $eleve->nom_complet }}</p>
            </div>
            <a href="{{ route('eleve.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-xl transition flex items-center">
                <i class="fa-solid fa-arrow-left mr-1"></i> Retour
            </a>
        </div>

        <!-- Résumé -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($inscriptions as $inscription)
                <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl">
                    <h3 class="font-bold text-white">{{ $inscription->classe->nom_classe }} ({{ $inscription->annee_scolaire }})</h3>
                    <p class="text-xs text-slate-400 mt-1">{{ $inscription->classe->option->nomoption ?? 'Générale' }}</p>
                    <p class="text-xs text-slate-500 mt-2">Statut: 
                        <span class="{{ $inscription->statut == 'actif' ? 'text-emerald-400' : 'text-slate-400' }}">
                            {{ ucfirst($inscription->statut) }}
                        </span>
                    </p>
                    @php
                        $totalPaye = $paiements->where('inscription_id', $inscription->id)->sum('montant_paye');
                    @endphp
                    <p class="text-lg font-black text-emerald-400 mt-2">{{ number_format($totalPaye, 2) }} USD payés</p>
                </div>
            @endforeach
        </div>

        <!-- Historique des paiements -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
            <h2 class="text-base font-bold text-white mb-4">Historique des paiements</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                        <tr>
                            <th class="p-3">N° Reçu</th>
                            <th class="p-3">Frais</th>
                            <th class="p-3">Classe</th>
                            <th class="p-3">Montant</th>
                            <th class="p-3">Date</th>
                            <th class="p-3">Mode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($paiements as $paiement)
                            <tr class="hover:bg-slate-800/50">
                                <td class="p-3 font-mono text-xs text-emerald-400">{{ $paiement->numero_recu }}</td>
                                <td class="p-3 text-white font-semibold">{{ $paiement->frais->intitule_frais ?? 'N/A' }}</td>
                                <td class="p-3 text-slate-400">{{ $paiement->inscription->classe->nom_classe ?? 'N/A' }}</td>
                                <td class="p-3 font-bold text-emerald-400">{{ number_format($paiement->montant_paye, 2) }} {{ $paiement->frais->devise ?? '' }}</td>
                                <td class="p-3 text-xs text-slate-400">{{ $paiement->date_paiement }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-slate-800 text-slate-300">{{ ucfirst($paiement->mode_paiement) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-slate-500">Aucun paiement enregistré.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $paiements->links() }}</div>
        </div>

    </div>

</body>
</html>

