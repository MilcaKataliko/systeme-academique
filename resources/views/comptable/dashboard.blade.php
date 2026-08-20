<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Comptable — Système Académique</title>
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
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-emerald-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-8 max-w-7xl w-full mx-auto">

            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Hero Banner Comptable -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-950 via-slate-950 to-teal-950 border border-emerald-500/20 p-6 sm:p-8 shadow-2xl">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-3">
                            <i class="fa-solid fa-calculator"></i> Finance & Trésorerie
                        </div>
                        <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">Tableau de bord Comptable</h1>
                        <p class="text-slate-400 text-sm mt-1.5 max-w-2xl leading-relaxed">
                            Gestion des encaissements, suivi du recouvrement des frais scolaires et relances automatisées.
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('comptable.paiements.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-lg shadow-emerald-600/30">
                            <i class="fa-solid fa-cash-register"></i> Encaisser un paiement
                        </a>
                        <a href="{{ route('comptable.rappels.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition">
                            <i class="fa-solid fa-bell text-amber-400"></i> Relances
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cartes KPIs Financiers -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Frais Facturés</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-sm border border-blue-500/20">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-white mt-3">{{ number_format($stats->total_frais, 0, ',', ' ') }} $</p>
                    <p class="text-[11px] text-slate-500 mt-1">{{ $stats->total_eleves }} élèves inscrits</p>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Recettes Perçues</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/20">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                    </div>
                    <div class="flex items-baseline justify-between mt-3">
                        <p class="text-2xl sm:text-3xl font-black text-emerald-400">{{ number_format($stats->total_paiements, 0, ',', ' ') }} $</p>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">{{ $stats->taux_recouvrement }}%</span>
                    </div>
                    <div class="w-full bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                        <div class="bg-emerald-400 h-full rounded-full" style="width: {{ $stats->taux_recouvrement }}%"></div>
                    </div>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Reste à Recouvrer</span>
                        <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-sm border border-rose-500/20">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-rose-400 mt-3">{{ number_format($stats->total_restant, 0, ',', ' ') }} $</p>
                    <p class="text-[11px] text-slate-500 mt-1"><span class="text-amber-400 font-semibold">{{ $stats->eleves_en_retard }}</span> élèves en retard</p>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase text-slate-400">Total Encaissements</span>
                        <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm border border-cyan-500/20">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-black text-cyan-400 mt-3">{{ $stats->nombre_paiements }}</p>
                    <p class="text-[11px] text-slate-500 mt-1"><span class="text-emerald-400 font-semibold">{{ $stats->eleves_en_regle }}</span> élèves en règle</p>
                </div>
            </div>

            <!-- Graphiques Financiers (Évolution + Répartition par Mode) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Évolution des encaissements par mois -->
                <div class="lg:col-span-2 bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-chart-line text-emerald-400"></i> Évolution des encaissements mensuels
                        </h3>
                        <span class="text-[11px] text-slate-500">6 derniers mois</span>
                    </div>
                    <div class="h-64 sm:h-72 w-full">
                        <canvas id="chartComptableEvolution"></canvas>
                    </div>
                </div>

                <!-- Répartition par mode de paiement -->
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2 mb-4">
                            <i class="fa-solid fa-chart-pie text-cyan-400"></i> Répartition par Canal
                        </h3>
                        <div class="h-52 w-full flex items-center justify-center">
                            <canvas id="chartComptableModes"></canvas>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-800/80 text-center">
                        <a href="{{ route('comptable.paiements.index') }}" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 transition">
                            Voir le grand livre des paiements <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Derniers Paiements Enregistrés -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                <div class="p-5 sm:p-6 border-b border-slate-800/80 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-white flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-amber-400"></i> Derniers Paiements Enregistrés
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">Historique en temps réel des transactions</p>
                    </div>
                    <a href="{{ route('comptable.paiements.create') }}" class="text-xs bg-emerald-600/10 hover:bg-emerald-600 hover:text-white text-emerald-400 border border-emerald-500/20 px-3 py-1.5 rounded-xl font-bold transition">
                        <i class="fa-solid fa-plus mr-1"></i> Nouveau
                    </a>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            @forelse($paiementsRecents as $p)
                                <tr class="hover:bg-slate-900/50 transition">
                                    <td class="p-4 font-mono text-xs text-blue-400 font-semibold">{{ $p->numero_recu }}</td>
                                    <td class="p-4 font-semibold text-white">
                                        {{ $p->inscription->eleve->nom ?? '—' }} {{ $p->inscription->eleve->postnom ?? '' }}
                                    </td>
                                    <td class="p-4 text-xs text-slate-400">{{ $p->inscription->classe->nom_classe ?? '—' }}</td>
                                    <td class="p-4 text-xs text-slate-300">{{ $p->frais->intitule_frais ?? 'Frais' }}</td>
                                    <td class="p-4 text-right font-bold text-emerald-400">+{{ number_format($p->montant_paye, 0, ',', ' ') }} $</td>
                                    <td class="p-4 text-center">
                                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700">
                                            {{ $p->mode_paiement ?: 'Espèces' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right text-xs text-slate-400">
                                        {{ $p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-xs text-slate-500">
                                        Aucun paiement enregistré pour l'instant.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <!-- GRAPHIQUES COMPTABLE CHART.JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.font.family = 'system-ui, sans-serif';

            // 1. Évolution des paiements
            const ctxEv = document.getElementById('chartComptableEvolution');
            if (ctxEv) {
                new Chart(ctxEv, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($evolutionPaiementsLabels) !!},
                        datasets: [{
                            label: 'Encaissements ($)',
                            data: {!! json_encode($evolutionPaiementsData) !!},
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointBackgroundColor: '#10b981',
                            pointRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(51, 65, 85, 0.3)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // 2. Modes de paiement
            const ctxModes = document.getElementById('chartComptableModes');
            if (ctxModes) {
                new Chart(ctxModes, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($modesLabels) !!},
                        datasets: [{
                            data: {!! json_encode($modesData) !!},
                            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'],
                            borderColor: '#020617',
                            borderWidth: 3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } } },
                        cutout: '65%'
                    }
                });
            }
        });
    </script>
</body>
</html>
