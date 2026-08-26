<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Élève — Système Académique</title>
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
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-cyan-600 selection:text-white">

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

            <!-- Hero Banner Élève -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-cyan-950 via-slate-950 to-blue-950 border border-cyan-500/20 p-6 sm:p-8 shadow-2xl">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-semibold uppercase tracking-wider mb-3">
                            <i class="fa-solid fa-user-graduate"></i> Mon Espace d'Études
                        </div>
                        <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                            Bonjour, {{ $eleve->prenom ? $eleve->prenom . ' ' : '' }}{{ $eleve->nom }} {{ $eleve->postnom }}
                        </h1>
                        <p class="text-slate-400 text-sm mt-1.5 max-w-2xl leading-relaxed">
                            Matricule : <span class="font-mono text-cyan-300 font-bold">{{ $eleve->code_matricule }}</span>
                            @if($inscriptions->first() && $inscriptions->first()->classe)
                                <i class="fa-solid fa-circle text-[5px] mx-2 align-middle"></i> Classe : <span class="text-slate-200 font-semibold">{{ $inscriptions->first()->classe->nom_classe }}</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('eleve.notes') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold transition shadow-lg shadow-cyan-600/30">
                            <i class="fa-solid fa-list-check"></i> Mes Notes
                        </a>
                        <a href="{{ route('eleve.bulletins') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition">
                            <i class="fa-solid fa-award text-amber-400"></i> Bulletins
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cartes Statistiques Élève -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Moyenne Générale</span>
                        <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm border border-cyan-500/20">
                            <i class="fa-solid fa-award"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black {{ ($moyenneGenerale !== null && $moyenneGenerale >= 10) ? 'text-emerald-400' : 'text-rose-400' }} mt-3">
                        {{ $moyenneGenerale !== null ? number_format($moyenneGenerale, 2) . '/20' : '—' }}
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">
                        {{ ($moyenneGenerale !== null && $moyenneGenerale >= 10) ? 'Félicitations, vous êtes en réussite' : 'Poursuivez vos efforts' }}
                    </p>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Cours Suivis</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-sm border border-blue-500/20">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-blue-400 mt-3">{{ $coursSuivis }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Matières au programme</p>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Solde Financier</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/20">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black {{ $solde > 0 ? 'text-amber-400' : 'text-emerald-400' }} mt-3">
                        {{ number_format($solde, 0, ',', ' ') }} $
                    </p>
                    <p class="text-[11px] text-slate-500 mt-1">
                        {{ $solde > 0 ? 'Reste à régulariser' : 'En ordre de paiement' }}
                    </p>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Statut Académique</span>
                        <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-sm border border-purple-500/20">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-purple-400 mt-3">{{ $niveauRisque }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Score de risque : {{ $scoreRisque }}%</p>
                </div>
            </div>

            <!-- Graphique Notes par Matière & Suivi Financier Personnel -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Graphique Notes par Matière -->
                <div class="lg:col-span-2 bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-chart-column text-cyan-400"></i> Mes Performances par Matière
                        </h3>
                        <span class="text-[11px] text-slate-500">Moyenne sur 20</span>
                    </div>
                    <div class="h-64 sm:h-72 w-full">
                        <canvas id="chartEleveMatieres"></canvas>
                    </div>
                </div>

                <!-- Bilan Financier Personnel -->
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2 mb-4">
                            <i class="fa-solid fa-receipt text-emerald-400"></i> Statut des Frais Scolaires
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
                                <span class="text-xs text-slate-400">Total Frais Fixés</span>
                                <strong class="text-sm text-white">{{ number_format($totalDu, 0, ',', ' ') }} $</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-emerald-500/5 border border-emerald-500/10 flex items-center justify-between">
                                <span class="text-xs text-emerald-400">Total Payé</span>
                                <strong class="text-sm text-emerald-400">{{ number_format($totalPaye, 0, ',', ' ') }} $</strong>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
                                <span class="text-xs text-slate-400">Solde Restant</span>
                                <strong class="text-sm {{ $solde > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                                    {{ number_format($solde, 0, ',', ' ') }} $
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800/80 text-center">
                        <a href="{{ route('eleve.finances') }}" class="text-xs font-bold text-cyan-400 hover:text-cyan-300 transition">
                            Voir le détail de mes reçus <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recommandations & Conseils Pédagogiques -->
            @if(count($recommandations) > 0)
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg">
                    <h3 class="font-bold text-sm text-white flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-lightbulb text-yellow-400"></i> Conseils & Recommandations
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($recommandations as $recom)
                            <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800 text-xs text-slate-300 flex items-start gap-2">
                                <i class="fa-solid fa-check text-cyan-400 mt-0.5"></i>
                                <span>{{ $recom }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </main>
    </div>

    <!-- GRAPHIQUE ÉLÈVE CHART.JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.font.family = 'system-ui, sans-serif';

            const ctx = document.getElementById('chartEleveMatieres');
            if (ctx) {
                const labels = {!! json_encode($matieresLabels) !!};
                const data = {!! json_encode($matieresNotes) !!};

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels.length > 0 ? labels : ['Aucune note'],
                        datasets: [{
                            label: 'Ma note (/20)',
                            data: data.length > 0 ? data : [0],
                            backgroundColor: '#06b6d4',
                            borderRadius: 8,
                            borderWidth: 1,
                            borderColor: '#0891b2'
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
</body>
</html>
