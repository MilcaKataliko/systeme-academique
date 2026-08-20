<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corps Enseignant — Directeur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-purple-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- En-tête -->
            <div class="bg-gradient-to-r from-purple-950 via-slate-950 to-slate-900 border border-purple-500/20 p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-chalkboard-user"></i> Pédagogie
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Corps Enseignant</h1>
                    <p class="text-slate-400 mt-1 text-sm">Gérez les professeurs, attribuez-leur des cours et supervisez l'encodage des cotes.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('directeur.enseignants.attributions') }}" class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition inline-flex items-center space-x-2 shadow-lg shadow-purple-600/30">
                        <i class="fa-solid fa-plus"></i>
                        <span>Attribuer un cours</span>
                    </a>
                    <a href="{{ route('directeur.enseignants.supervision') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 px-4 py-2.5 rounded-xl text-xs font-bold transition inline-flex items-center space-x-2">
                        <i class="fa-solid fa-pen-ruler text-pink-400"></i>
                        <span>Superviser cotes</span>
                    </a>
                </div>
            </div>

            <!-- Messages flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Statistiques -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-xl border border-purple-500/20">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $stats->total }}</p>
                        <p class="text-xs text-slate-400">Professeurs enregistrés</p>
                    </div>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xl border border-blue-500/20">
                        <i class="fa-solid fa-book-bookmark"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $stats->total_attributions }}</p>
                        <p class="text-xs text-slate-400">Attributions actives</p>
                    </div>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xl border border-teal-500/20">
                        <i class="fa-solid fa-school"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $stats->classes_couvertes }}</p>
                        <p class="text-xs text-slate-400">Classes couvertes</p>
                    </div>
                </div>
            </div>

            <!-- Tableau Enseignants -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="font-bold text-base text-white flex items-center gap-2">
                        <i class="fa-solid fa-list text-purple-400"></i> Liste des enseignants
                    </h2>
                    <span class="text-xs text-slate-400 font-semibold">{{ $enseignants->total() }} professeur(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                            <tr>
                                <th class="p-4">Matricule</th>
                                <th class="p-4">Nom & Postnom</th>
                                <th class="p-4">Grade</th>
                                <th class="p-4">Téléphone</th>
                                <th class="p-4">Cours / Classes</th>
                                <th class="p-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            @forelse($enseignants as $ens)
                                <tr class="hover:bg-slate-900/50 transition">
                                    <td class="p-4 font-mono text-xs text-purple-400 font-semibold">{{ $ens->matricule }}</td>
                                    <td class="p-4 font-semibold text-white">{{ $ens->nom }} {{ $ens->postnom }} {{ $ens->prenom }}</td>
                                    <td class="p-4 text-xs text-slate-300">{{ $ens->grade }}</td>
                                    <td class="p-4 text-xs text-slate-400">{{ $ens->telephone ?: '—' }}</td>
                                    <td class="p-4 text-xs text-slate-400">
                                        <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300">
                                            {{ $ens->plans_count ?? 0 }} cours attribué(s)
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('directeur.enseignants.attributions', ['enseignantId' => $ens->user_id]) }}" class="bg-purple-600 hover:bg-purple-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center space-x-1">
                                            <i class="fa-solid fa-arrow-right-arrow-left"></i><span>Gérer cours</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-chalkboard-user text-3xl mb-2 block"></i>
                                        Aucun enseignant enregistré.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($enseignants->hasPages())
                    <div class="px-6 py-4 border-t border-slate-800">
                        {{ $enseignants->links() }}
                    </div>
                @endif
            </div>

        </main>
    </div>

</body>
</html>
