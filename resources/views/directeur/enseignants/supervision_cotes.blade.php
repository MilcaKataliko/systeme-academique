<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervision des cotes — Directeur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-amber-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

        <!-- En-tête -->
        <div class="bg-gradient-to-r from-amber-900 to-slate-950 border border-amber-500/20 p-8 rounded-2xl shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white">Supervision des cotes</h1>
                    <p class="text-slate-400 mt-2 text-sm">Consultez et vérifiez les notes encodées par les enseignants.</p>
                </div>
                <a href="{{ route('directeur.enseignants') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Retour
                </a>
            </div>
        </div>

        <!-- Messages flash -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Filtres -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Recherche</label>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Élève, matricule ou cours" class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 w-full">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Enseignant</label>
                    <select name="enseignant_id" class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 w-full">
                        <option value="">Tous les enseignants</option>
                        @foreach($enseignants as $ens)
                            <option value="{{ $ens->id }}" {{ request('enseignant_id') == $ens->id ? 'selected' : '' }}>
                                {{ $ens->nom }} {{ $ens->postnom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Période</label>
                    <select name="periode_id" class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 w-full">
                        <option value="">Toutes les périodes</option>
                        @foreach($periodes as $periode)
                            <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                                {{ $periode->nom_periode }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Classe</label>
                    <select name="classe_id" class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 w-full">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom_classe }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-500 text-white py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                            <i class="fa-solid fa-filter mr-2"></i> Filtrer
                        </button>
                        <a href="{{ route('directeur.enseignants.supervision') }}" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 transition" title="Réinitialiser les filtres" aria-label="Réinitialiser les filtres">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

<!-- Cartes de statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Cotes encodées</p>
                        <p class="text-2xl font-black text-white mt-1">{{ number_format($stats->total_cotes, 0) }}</p>
                            <p class="text-[11px] text-slate-500 mt-1">{{ $stats->eleves_concernes }} élève(s), {{ $stats->notes_saisies }} avec note</p>
                    </div>
                    <div class="bg-blue-500/10 p-3 rounded-xl text-blue-400">
                        <i class="fa-solid fa-pen-to-square text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Moyenne globale</p>
                        <p class="text-2xl font-black text-white mt-1">
                            {{ $stats->moyenne_globale !== null ? number_format($stats->moyenne_globale, 2) . ' / 20' : '—' }}
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1">Toutes cotes confondues</p>
                    </div>
                    <div class="bg-cyan-500/10 p-3 rounded-xl text-cyan-400">
                        <i class="fa-solid fa-chart-line text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Taux de réussite</p>
                        <p class="text-2xl font-black text-white mt-1">
                            {{ $stats->taux_reussite !== null ? number_format($stats->taux_reussite, 1) . '%' : '—' }}
                        </p>
                        <p class="text-[11px] mt-1">
                            <span class="text-emerald-400">{{ $stats->reussis }} réussi</span>
                            <span class="text-red-400 ml-2">{{ $stats->echoues }} échoué</span>
                        </p>
                    </div>
                    <div class="bg-emerald-500/10 p-3 rounded-xl text-emerald-400">
                        <i class="fa-solid fa-medal text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Présence moyenne</p>
                        <p class="text-2xl font-black text-white mt-1">
                            {{ $stats->presence_moyenne !== null ? number_format($stats->presence_moyenne, 1) . '%' : '—' }}
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1">Assiduité des élèves</p>
                    </div>
                    <div class="bg-amber-500/10 p-3 rounded-xl text-amber-400">
                        <i class="fa-solid fa-user-check text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des cotes -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="font-bold text-lg text-white flex items-center">
                    <i class="fa-solid fa-table text-amber-400 mr-3"></i>Cotes encodées
                    <span class="ml-3 text-sm font-normal text-slate-400">({{ $cotes->total() }} entrées)</span>
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
<thead>
                        <tr class="border-b border-slate-800 bg-slate-900/50">
                            <th class="text-left py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs tracking-wider">Élève</th>
                            <th class="text-left py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs tracking-wider">Cours</th>
                            <th class="text-left py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs tracking-wider">Classe</th>
                            <th class="text-left py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs tracking-wider">Période</th>
                            <th class="text-center py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs tracking-wider">Total</th>
                            <th class="text-center py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs tracking-wider">%</th>
                            <th class="text-center py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs tracking-wider">Statut</th>
                            <th class="text-center py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs tracking-wider">Présence</th>
                            <th class="text-left py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs tracking-wider">Encodé par</th>
                            <th class="text-left py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cotes as $cote)
                            @php
                                $pourcentage = $cote->pourcentage;
                                $statut = $cote->statut;
                            @endphp
                            <tr class="border-b border-slate-800/50 hover:bg-slate-900/30 transition duration-100">
                                <td class="py-3.5 px-4 text-white font-medium">
                                    {{ $cote->inscription?->eleve?->nom ?? 'Élève supprimé' }} {{ $cote->inscription?->eleve?->postnom ?? '' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-300">{{ $cote->plan?->cours?->nom_cours ?? 'Cours supprimé' }}</td>
                                <td class="py-3.5 px-4 text-slate-400">{{ $cote->plan?->classe?->nom_classe ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-slate-400">{{ $cote->periode?->nom_periode ?? 'Évaluations' }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-mono font-bold text-slate-100">{{ number_format($cote->total_points, 2) }}</span>
                                    <span class="text-slate-500 text-xs">/ {{ number_format($cote->max_total, 0) }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($pourcentage !== null)
                                        <span class="font-mono font-bold {{ $pourcentage >= 50 ? 'text-emerald-400' : 'text-red-400' }}">
                                            {{ number_format($pourcentage, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-slate-500">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($statut === 'Réussi')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <i class="fa-solid fa-circle-check mr-1.5"></i>{{ $statut }}
                                        </span>
                                    @elseif($statut === 'Échoué')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20">
                                            <i class="fa-solid fa-circle-xmark mr-1.5"></i>{{ $statut }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($cote->pourcentage_presence !== null)
                                        <span class="font-mono text-xs {{ $cote->pourcentage_presence >= 100 ? 'text-emerald-400' : 'text-slate-300' }}">
                                            <i class="fa-solid fa-user-check mr-1"></i>{{ number_format($cote->pourcentage_presence, 0) }}%
                                        </span>
                                    @else
                                        <span class="text-slate-600 text-xs">Non saisie</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">{{ $cote->encodeur?->name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-slate-500 text-xs">{{ $cote->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-500">
                                    <i class="fa-solid fa-pen-to-square text-3xl mb-3 block"></i>
                                    <p>Aucune cote encodée pour le moment.</p>
                                    <p class="text-xs mt-1">Les notes apparaîtront ici une fois que les enseignants les auront saisies.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($cotes->hasPages())
                <div class="px-6 py-4 border-t border-slate-800">
                    {{ $cotes->links() }}
                </div>
            @endif
        </div>

    </main>
</div>

</body>
</html>
