<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre des Élèves — Système Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-blue-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- En-tête -->
            <div class="bg-gradient-to-r from-amber-950 via-slate-950 to-slate-900 border border-amber-500/20 p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-users"></i> Scolarité
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Registre des Élèves</h1>
                    <p class="text-slate-400 mt-1 text-sm">Gérez les inscriptions, les fiches matricules et l'historique scolaire.</p>
                </div>
                <a href="{{ route('directeur.eleves.create') }}" class="bg-amber-600 hover:bg-amber-500 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition inline-flex items-center space-x-2 shadow-lg shadow-amber-600/30">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Nouvel élève</span>
                </a>
            </div>

            <!-- Messages Flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Statistiques Rapides -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg flex items-center space-x-4">
                    <div class="bg-amber-500/10 border border-amber-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-amber-400 text-xl"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $stats->total_eleves }}</p>
                        <p class="text-xs text-slate-400">Élèves enregistrés</p>
                    </div>
                </div>
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg flex items-center space-x-4">
                    <div class="bg-blue-500/10 border border-blue-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-blue-400 text-xl"><i class="fa-solid fa-pen-to-square"></i></div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $stats->total_inscriptions }}</p>
                        <p class="text-xs text-slate-400">Inscriptions enregistrées</p>
                    </div>
                </div>
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg flex items-center space-x-4">
                    <div class="bg-emerald-500/10 border border-emerald-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-emerald-400 text-xl"><i class="fa-solid fa-school"></i></div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $stats->classes_actives }}</p>
                        <p class="text-xs text-slate-400">Classes actives</p>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl p-5 sm:p-6 shadow-lg">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, postnom ou matricule..." 
                               class="bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 w-full">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Classe</label>
                        <select name="classe_id" class="bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 w-full">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom_classe }} ({{ $classe->option->nom_option ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                            <i class="fa-solid fa-filter mr-2"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tableau Élèves -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="font-bold text-base text-white flex items-center">
                        <i class="fa-solid fa-table text-amber-400 mr-2"></i> Liste des élèves
                    </h2>
                    <span class="text-xs text-slate-400">{{ $eleves->total() }} élève(s)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                            <tr>
                                <th class="py-3.5 px-4">Matricule</th>
                                <th class="py-3.5 px-4">Nom & Postnom</th>
                                <th class="py-3.5 px-4">Genre</th>
                                <th class="py-3.5 px-4">Classe(s)</th>
                                <th class="py-3.5 px-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            @forelse($eleves as $eleve)
                                <tr class="hover:bg-slate-900/50 transition">
                                    <td class="py-3.5 px-4 font-mono text-xs text-amber-400 font-semibold">{{ $eleve->code_matricule }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-white">{{ $eleve->nom }} {{ $eleve->postnom }} {{ $eleve->prenom }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="{{ $eleve->genre == 'M' ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20' : 'bg-pink-500/10 text-pink-400 border-pink-500/20' }} border px-2 py-0.5 rounded-full text-xs font-semibold">
                                            {{ $eleve->genre == 'M' ? 'Masculin' : 'Féminin' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @forelse($eleve->inscriptions as $insc)
                                            <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded-full mr-1">
                                                {{ $insc->classe->nom_classe ?? 'N/A' }} ({{ $insc->annee_scolaire }})
                                            </span>
                                        @empty
                                            <span class="text-amber-400 text-xs">Aucune inscription</span>
                                        @endforelse
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('directeur.eleves.show', $eleve->id) }}" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center space-x-1">
                                                <i class="fa-solid fa-eye"></i><span>Fiche</span>
                                            </a>
                                            <a href="{{ route('directeur.eleves.edit', $eleve->id) }}" class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center space-x-1">
                                                <i class="fa-solid fa-pen"></i><span>Modifier</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-users text-3xl mb-3 block"></i>
                                        <p>Aucun élève trouvé.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($eleves->hasPages())
                    <div class="px-6 py-4 border-t border-slate-800">
                        {{ $eleves->links() }}
                    </div>
                @endif
            </div>

        </main>
    </div>

</body>
</html>
