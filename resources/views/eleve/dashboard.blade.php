<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Élève — Système Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Barre de Navigation -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div class="flex items-center space-x-3">
                <div class="bg-emerald-600 p-2.5 rounded-xl text-white font-black text-lg">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-white">Espace Élève</h1>
                    <p class="text-xs text-slate-400">Consultez votre situation financière et vos résultats</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-xs bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-3 py-1.5 rounded-xl font-bold">
                    <i class="fa-solid fa-user mr-1"></i> {{ Auth::user()->name }}
                </span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 px-3 py-1.5 rounded-xl transition cursor-pointer">
                        <i class="fa-solid fa-power-off mr-1"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>

        <!-- Information élève -->
        <div class="bg-emerald-500/5 border border-emerald-500/20 p-6 rounded-2xl">
            <h2 class="text-lg font-bold text-white">{{ $eleve->nom_complet }}</h2>
            <p class="text-xs text-slate-400 mt-1">
                @foreach($inscriptions as $inscription)
                    {{ $inscription->classe->nom_classe }} ({{ $inscription->classe->option->nomoption ?? 'Générale' }}) — {{ $inscription->annee_scolaire }}
                @endforeach
            </p>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Total payé</p>
                    <h3 class="text-2xl font-black text-white mt-1">{{ number_format($totalPaiements, 2) }} USD</h3>
                </div>
                <div class="bg-emerald-500/10 text-emerald-400 p-3 rounded-xl">
                    <i class="fa-solid fa-coins text-xl"></i>
                </div>
            </div>
            <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Inscriptions actives</p>
                    <h3 class="text-2xl font-black text-white mt-1">{{ $inscriptions->count() }}</h3>
                </div>
                <div class="bg-blue-500/10 text-blue-400 p-3 rounded-xl">
                    <i class="fa-solid fa-file-signature text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Menus -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('eleve.finances') }}" class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-emerald-500/50 transition block">
                <div class="bg-emerald-500/10 text-emerald-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Situation Financière</h3>
                <p class="text-xs text-slate-400">Consultez l'historique de vos paiements et votre situation financière.</p>
                <span class="inline-flex items-center text-xs font-bold text-emerald-400 hover:text-emerald-300 mt-4">
                    Voir mes finances <i class="fa-solid fa-arrow-right ml-2"></i>
                </span>
            </a>

            <a href="{{ route('eleve.bulletin') }}" class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-purple-500/50 transition block">
                <div class="bg-purple-500/10 text-purple-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-scroll text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Bulletin / Résultats</h3>
                <p class="text-xs text-slate-400">Consultez vos cotes et vos bulletins par année scolaire.</p>
                <span class="inline-flex items-center text-xs font-bold text-purple-400 hover:text-purple-300 mt-4">
                    Voir mon bulletin <i class="fa-solid fa-arrow-right ml-2"></i>
                </span>
            </a>
        </div>

        <!-- Derniers paiements -->
        @if($derniersPaiements->isNotEmpty())
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
            <h3 class="text-base font-bold text-white mb-4">Derniers paiements</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                        <tr><th class="p-3">N° Reçu</th><th class="p-3">Frais</th><th class="p-3">Montant</th><th class="p-3">Date</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($derniersPaiements as $p)
                            <tr class="hover:bg-slate-800/50">
                                <td class="p-3 font-mono text-xs text-emerald-400">{{ $p->numero_recu }}</td>
                                <td class="p-3">{{ $p->frais->intitule_frais ?? 'N/A' }}</td>
                                <td class="p-3 font-bold text-emerald-400">{{ number_format($p->montant_paye, 2) }}</td>
                                <td class="p-3 text-xs">{{ $p->date_paiement }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

</body>
</html>

