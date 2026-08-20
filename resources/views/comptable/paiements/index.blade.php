<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal des Paiements — Comptable</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-emerald-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- En-tête -->
            <div class="bg-gradient-to-r from-emerald-950 via-slate-950 to-slate-900 border border-emerald-500/20 p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-receipt"></i> Journal de Trésorerie
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Journal des Paiements</h1>
                    <p class="text-slate-400 mt-1 text-sm">Consultez, filtrez et imprimez les reçus de paiement de l'établissement.</p>
                </div>
                <a href="{{ route('comptable.paiements.create') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition inline-flex items-center space-x-2 shadow-lg shadow-emerald-600/30">
                    <i class="fa-solid fa-plus"></i>
                    <span>Encaisser un paiement</span>
                </a>
            </div>

            <!-- Messages flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filtres -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl p-5 shadow-lg">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Classe</label>
                        <select name="classe_id" class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-3 py-2 text-sm outline-none focus:border-emerald-500 transition">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>{{ $classe->nom_classe }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Date début</label>
                        <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-3 py-2 text-sm outline-none focus:border-emerald-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Date fin</label>
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-3 py-2 text-sm outline-none focus:border-emerald-500 transition">
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-2 rounded-xl text-xs font-bold transition shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-filter"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tableau Paiements -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="font-bold text-base text-white flex items-center gap-2">
                        <i class="fa-solid fa-list text-emerald-400"></i> Historique des encaissements
                    </h2>
                    <span class="text-xs text-slate-400 font-semibold">{{ $paiements->total() }} transaction(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                            <tr>
                                <th class="p-4">N° Reçu</th>
                                <th class="p-4">Élève</th>
                                <th class="p-4">Classe</th>
                                <th class="p-4">Frais</th>
                                <th class="p-4 text-right">Montant</th>
                                <th class="p-4 text-center">Canal</th>
                                <th class="p-4 text-right">Date</th>
                                <th class="p-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            @forelse($paiements as $p)
                                <tr class="hover:bg-slate-900/50 transition">
                                    <td class="p-4 font-mono text-xs text-emerald-400 font-bold">{{ $p->numero_recu }}</td>
                                    <td class="p-4 font-semibold text-white">
                                        {{ $p->inscription->eleve->nom ?? '—' }} {{ $p->inscription->eleve->postnom ?? '' }}
                                    </td>
                                    <td class="p-4 text-xs text-slate-400">{{ $p->inscription->classe->nom_classe ?? '—' }}</td>
                                    <td class="p-4 text-xs text-slate-300">{{ $p->frais->intitule_frais ?? 'Frais' }}</td>
                                    <td class="p-4 text-right font-black text-emerald-400">+{{ number_format($p->montant_paye, 0, ',', ' ') }} $</td>
                                    <td class="p-4 text-center">
                                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-300">
                                            {{ $p->mode_paiement ?: 'Espèces' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right text-xs text-slate-400">
                                        {{ $p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('comptable.paiements.show', $p->id) }}" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-semibold transition" title="Voir le reçu">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-12 text-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-receipt text-3xl mb-2 block"></i>
                                        Aucun paiement enregistré pour l'instant.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($paiements->hasPages())
                    <div class="px-6 py-4 border-t border-slate-800">
                        {{ $paiements->links() }}
                    </div>
                @endif
            </div>

        </main>
    </div>

</body>
</html>
