<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Années Scolaires — Directeur</title>
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
                        <i class="fa-solid fa-calendar-days"></i> Calendrier Académique
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Années Scolaires</h1>
                    <p class="text-slate-400 mt-1 text-sm">Configurez et gérez les différents calendriers scolaires de l'établissement.</p>
                </div>
                <a href="{{ route('annees.create') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition inline-flex items-center space-x-2 shadow-lg shadow-emerald-600/30">
                    <i class="fa-solid fa-plus"></i>
                    <span>Nouvelle année</span>
                </a>
            </div>

            <!-- Messages flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Tableau des Années Scolaires -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="font-bold text-base text-white flex items-center gap-2">
                        <i class="fa-solid fa-list text-emerald-400"></i> Historique des années
                    </h2>
                    <span class="text-xs text-slate-400 font-semibold">{{ $annees->count() }} année(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                            <tr>
                                <th class="p-4">ID</th>
                                <th class="p-4">Année Scolaire</th>
                                <th class="p-4">Date de création</th>
                                <th class="p-4 text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            @forelse($annees as $annee)
                                <tr class="hover:bg-slate-900/50 transition">
                                    <td class="p-4 font-mono text-xs text-slate-500">{{ $annee->idAnnee }}</td>
                                    <td class="p-4 font-bold text-base text-white">{{ $annee->anneescolaire }}</td>
                                    <td class="p-4 text-xs text-slate-400">{{ $annee->created_at ? $annee->created_at->format('d/m/Y') : '—' }}</td>
                                    <td class="p-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-semibold">
                                            Active
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-calendar-xmark text-3xl mb-2 block"></i>
                                        Aucune année scolaire configurée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>