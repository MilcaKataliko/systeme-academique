<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Enseignant — Système Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.6); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-purple-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-8 max-w-7xl w-full mx-auto">

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

            <!-- Hero Banner Enseignant -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-950 via-slate-950 to-indigo-950 border border-purple-500/20 p-6 sm:p-8 shadow-2xl">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-purple-400 text-xs font-semibold uppercase tracking-wider mb-3">
                            <i class="fa-solid fa-chalkboard-user"></i> Espace Pédagogique
                        </div>
                        <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                            Bonjour, {{ $enseignant ? $enseignant->nom . ' ' . $enseignant->postnom : Auth::user()->name }}
                        </h1>
                        <p class="text-slate-400 text-sm mt-1.5 max-w-2xl leading-relaxed">
                            @if($enseignant)
                                Titulaire : <span class="text-purple-300 font-semibold">{{ $enseignant->grade }}</span> <i class="fa-solid fa-circle text-[5px] mx-2 align-middle"></i> Matricule : <span class="text-slate-300">{{ $enseignant->matricule }}</span>
                            @else
                                Gestion de vos cours, encodage des cotes d'évaluations et suivi de l'assiduité des élèves.
                            @endif
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('enseignant.profil') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition shadow-lg shadow-purple-600/30">
                            <i class="fa-solid fa-id-card"></i> Mon Profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cartes KPIs Pédagogiques -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Mes Cours</span>
                        <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-sm border border-purple-500/20">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-white mt-3">{{ $plans->count() }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Matières attribuées</p>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Total Élèves</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-sm border border-blue-500/20">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-blue-400 mt-3">{{ $totalEleves }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Élèves sous votre encadrement</p>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Moyenne Globale</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/20">
                            <i class="fa-solid fa-chart-simple"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-emerald-400 mt-3">
                        {{ $moyenneGlobale !== null ? number_format($moyenneGlobale, 2) . '/20' : '—' }}
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">Toutes vos classes confondues</p>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Taux Réussite</span>
                        <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-sm border border-amber-500/20">
                            <i class="fa-solid fa-award"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-3">
                        <p class="text-2xl sm:text-3xl font-black text-amber-400">
                            {{ $tauxReussiteGlobal !== null ? $tauxReussiteGlobal . '%' : '—' }}
                        </p>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Notes &ge; 10/20</p>
                </div>
            </div>

            <!-- Graphique Performances par Classe -->
            @if(count($classesChartLabels) > 0)
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-chart-column text-purple-400"></i> Moyennes par Classe & Matière Enseignée
                        </h3>
                        <span class="text-[11px] text-slate-500">Moyenne sur 20</span>
                    </div>
                    <div class="h-64 w-full">
                        <canvas id="chartEnseignantClasses"></canvas>
                    </div>
                </div>
            @endif

            <!-- Mes Cours & Classes Attribuées (Tableau d'actions rapides) -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                <div class="p-5 sm:p-6 border-b border-slate-800/80 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-white flex items-center gap-2">
                            <i class="fa-solid fa-chalkboard text-indigo-400"></i> Vos Attributions & Saisie des Cotes
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">Sélectionnez une classe pour encoder les notes ou gérer les présences</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-5 sm:p-6">
                    @forelse($plans as $p)
                        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-purple-500/40 transition flex flex-col justify-between group shadow-md">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-purple-500/10 text-purple-300 border border-purple-500/20">
                                        {{ $p->classe->option->code_option ?? 'Section' }}
                                    </span>
                                    <span class="text-xs font-mono text-slate-400">Max: {{ $p->maxima_periode ?? 10 }} pts</span>
                                </div>
                                <h4 class="font-bold text-lg text-white group-hover:text-purple-300 transition-colors">
                                    {{ $p->cours->nom_cours ?? 'Cours' }}
                                </h4>
                                <p class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                                    <i class="fa-solid fa-school text-teal-400"></i> Classe : <strong class="text-slate-200">{{ $p->classe->nom_classe ?? '—' }}</strong>
                                </p>
                            </div>

                            <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center gap-2">
                                <a href="{{ route('enseignant.eleves.classe', ['classeId' => $p->classe_id, 'planId' => $p->id]) }}" 
                                   class="flex-1 py-2 px-3 rounded-xl bg-purple-600/10 hover:bg-purple-600 hover:text-white text-purple-400 border border-purple-500/20 text-xs font-bold transition text-center flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-pen-to-square"></i> Cotes
                                </a>
                                <a href="{{ route('enseignant.presence.form', ['classeId' => $p->classe_id, 'planId' => $p->id]) }}" 
                                   class="py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-bold transition" title="Présences">
                                    <i class="fa-solid fa-calendar-check text-emerald-400"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-slate-500 text-sm">
                            <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                            Aucun cours ne vous a encore été attribué par la direction.
                        </div>
                    @endforelse
                </div>
            </div>

        </main>
    </div>

    <!-- GRAPHIQUE ENSEIGNANT CHART.JS -->
    @if(count($classesChartLabels) > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Chart.defaults.color = '#94a3b8';
                Chart.defaults.font.family = 'system-ui, sans-serif';

                const ctx = document.getElementById('chartEnseignantClasses');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($classesChartLabels) !!},
                            datasets: [{
                                label: 'Moyenne (/20)',
                                data: {!! json_encode($classesChartData) !!},
                                backgroundColor: '#a855f7',
                                borderRadius: 8,
                                borderWidth: 1,
                                borderColor: '#9333ea'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { min: 0, max: 20, grid: { color: 'rgba(51, 65, 85, 0.3)' } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
</body>
</html>
