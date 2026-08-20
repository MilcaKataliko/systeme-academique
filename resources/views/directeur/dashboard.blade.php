<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord de direction — Système Académique</title>
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
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-blue-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-8 max-w-7xl w-full mx-auto">
            
            <!-- Hero Banner Direction -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-950 via-slate-950 to-indigo-950 border border-blue-500/20 p-6 sm:p-8 shadow-2xl">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-semibold uppercase tracking-wider mb-3">
                            <i class="fa-solid fa-gauge-high"></i> Pilotage Stratégique
                        </div>
                        <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">Tableau de bord de Direction</h1>
                        <p class="text-slate-400 text-sm mt-1.5 max-w-2xl leading-relaxed">
                            Supervision globale en temps réel des effectifs, des performances académiques et du recouvrement financier de l'établissement.
                        </p>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('directeur.eleves.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition shadow-lg shadow-blue-600/30">
                            <i class="fa-solid fa-user-plus"></i> Inscrire un élève
                        </a>
                        <a href="{{ route('directeur.classes.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition">
                            <i class="fa-solid fa-pen-to-square text-pink-400"></i> Saisir cotes
                        </a>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECTION 1 : DEMOGRAPHIE & INSCRIPTIONS     -->
            <!-- ========================================== -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-users text-amber-400"></i> Inscriptions & Effectifs
                    </h2>
                    <span class="text-xs text-slate-400">Année en cours</span>
                </div>

                <!-- 4 Cartes Inscriptions & Effectifs -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl relative overflow-hidden shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase text-slate-400">Effectif Total</span>
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-sm border border-amber-500/20">
                                <i class="fa-solid fa-user-group"></i>
                            </div>
                        </div>
                        <p class="text-2xl sm:text-3xl font-black text-white mt-3">{{ number_format($totalEleves, 0, ',', ' ') }}</p>
                        <p class="text-[11px] text-slate-500 mt-1">Élèves enregistrés dans l'école</p>
                    </div>

                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl relative overflow-hidden shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase text-slate-400">Inscriptions récentes</span>
                            <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-sm border border-blue-500/20">
                                <i class="fa-solid fa-calendar-day"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2 mt-3">
                            <p class="text-2xl sm:text-3xl font-black text-blue-400">{{ $inscriptionsCeMois }}</p>
                            <span class="text-xs text-slate-400">ce mois</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1"><span class="text-emerald-400 font-bold">+{{ $inscriptionsAujourdhui }}</span> aujourd'hui</p>
                    </div>

                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl relative overflow-hidden shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase text-slate-400">Garçons</span>
                            <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-sm border border-cyan-500/20">
                                <i class="fa-solid fa-mars"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline justify-between mt-3">
                            <p class="text-2xl sm:text-3xl font-black text-cyan-400">{{ $totalGarcons }}</p>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">{{ $pctGarcons }}%</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                            <div class="bg-cyan-400 h-full rounded-full" style="width: {{ $pctGarcons }}%"></div>
                        </div>
                    </div>

                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl relative overflow-hidden shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase text-slate-400">Filles</span>
                            <div class="w-9 h-9 rounded-xl bg-pink-500/10 text-pink-400 flex items-center justify-center text-sm border border-pink-500/20">
                                <i class="fa-solid fa-venus"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline justify-between mt-3">
                            <p class="text-2xl sm:text-3xl font-black text-pink-400">{{ $totalFilles }}</p>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-pink-500/10 text-pink-400 border border-pink-500/20">{{ $pctFilles }}%</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                            <div class="bg-pink-400 h-full rounded-full" style="width: {{ $pctFilles }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Graphiques Démographie (2 Colonnes) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Évolution des inscriptions par mois (Ligne) -->
                    <div class="lg:col-span-2 bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-chart-line text-blue-400"></i> Évolution des inscriptions par mois
                            </h3>
                            <span class="text-[11px] text-slate-500">12 derniers mois</span>
                        </div>
                        <div class="h-64 sm:h-72 w-full">
                            <canvas id="chartInscriptionsMois"></canvas>
                        </div>
                    </div>

                    <!-- Répartition Filles / Garçons (Doughnut) -->
                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2 mb-4">
                                <i class="fa-solid fa-chart-pie text-pink-400"></i> Répartition par Genre
                            </h3>
                            <div class="h-48 w-full flex items-center justify-center">
                                <canvas id="chartGenre"></canvas>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-slate-800/80 text-center">
                            <div class="p-2 rounded-xl bg-cyan-500/5 border border-cyan-500/10">
                                <span class="text-[11px] text-slate-400 block">Garçons</span>
                                <strong class="text-sm text-cyan-400">{{ $totalGarcons }} ({{ $pctGarcons }}%)</strong>
                            </div>
                            <div class="p-2 rounded-xl bg-pink-500/5 border border-pink-500/10">
                                <span class="text-[11px] text-slate-400 block">Filles</span>
                                <strong class="text-sm text-pink-400">{{ $totalFilles }} ({{ $pctFilles }}%)</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Effectif par Classe & Répartition par Option -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Effectifs par classe (Bar chart) -->
                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-chart-column text-teal-400"></i> Effectif par Classe
                            </h3>
                            <span class="text-[11px] text-slate-500">{{ count($classesLabels) }} classes</span>
                        </div>
                        <div class="h-64 w-full">
                            <canvas id="chartEffectifClasse"></canvas>
                        </div>
                    </div>

                    <!-- Répartition des élèves par option (Tableau + Cartes) -->
                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                                    <i class="fa-solid fa-layer-group text-cyan-400"></i> Répartition par Option
                                </h3>
                                <a href="{{ route('options.index') }}" class="text-xs text-blue-400 hover:underline">Gérer</a>
                            </div>
                            <div class="space-y-3 max-h-56 overflow-y-auto custom-scrollbar pr-1">
                                @forelse($repartitionOptions as $opt)
                                    <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <strong class="text-sm text-slate-100">{{ $opt['nom'] }}</strong>
                                                @if($opt['code'])
                                                    <span class="text-[10px] uppercase px-1.5 py-0.5 rounded bg-slate-800 text-slate-400 font-bold">{{ $opt['code'] }}</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-3">
                                                <span><i class="fa-solid fa-mars text-cyan-400 mr-1"></i>{{ $opt['garcons'] }} G</span>
                                                <span><i class="fa-solid fa-venus text-pink-400 mr-1"></i>{{ $opt['filles'] }} F</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-base font-black text-white">{{ $opt['total'] }}</span>
                                            <span class="text-xs text-slate-400 block">{{ $opt['pct'] }}%</span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-500 py-6 text-center">Aucune option enregistrée.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECTION 2 : RESULTATS ACADEMIQUES          -->
            <!-- ========================================== -->
            <div class="space-y-4 pt-4 border-t border-slate-800/80">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-graduation-cap text-indigo-400"></i> Résultats Académiques & Pédagogie
                    </h2>
                    <span class="text-xs text-slate-400">{{ $totalEvalues }} élèves évalués</span>
                </div>

                <!-- 4 Cartes Performances Académiques -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase text-slate-400">Moyenne Générale</span>
                            <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-sm border border-indigo-500/20">
                                <i class="fa-solid fa-award"></i>
                            </div>
                        </div>
                        <p class="text-2xl sm:text-3xl font-black {{ $moyenneGeneraleEcole >= 10 ? 'text-emerald-400' : 'text-rose-400' }} mt-3">
                            {{ $moyenneGeneraleEcole }}/20
                        </p>
                        <p class="text-[11px] text-slate-500 mt-1">Moyenne de l'ensemble de l'école</p>
                    </div>

                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase text-slate-400">Taux de Réussite</span>
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/20">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline justify-between mt-3">
                            <p class="text-2xl sm:text-3xl font-black text-emerald-400">{{ $tauxReussite }}%</p>
                            <span class="text-xs text-slate-400 font-semibold">{{ $nombreReussis }} élèves</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                            <div class="bg-emerald-400 h-full rounded-full" style="width: {{ $tauxReussite }}%"></div>
                        </div>
                    </div>

                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase text-slate-400">Taux d'Échec</span>
                            <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-sm border border-rose-500/20">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                        </div>
                        <div class="flex items-baseline justify-between mt-3">
                            <p class="text-2xl sm:text-3xl font-black text-rose-400">{{ $tauxEchec }}%</p>
                            <span class="text-xs text-slate-400 font-semibold">{{ $nombreDifficulte }} élèves</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                            <div class="bg-rose-400 h-full rounded-full" style="width: {{ $tauxEchec }}%"></div>
                        </div>
                    </div>

                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase text-slate-400">Élèves en Difficulté</span>
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-sm border border-amber-500/20">
                                <i class="fa-solid fa-hand-holding-hand"></i>
                            </div>
                        </div>
                        <p class="text-2xl sm:text-3xl font-black text-amber-400 mt-3">{{ $nombreDifficulte }}</p>
                        <p class="text-[11px] text-slate-500 mt-1">Moyenne &lt; 10/20 à soutenir</p>
                    </div>
                </div>

                <!-- Palmarès d'Excellence & Élèves en Difficulté -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Meilleures Moyennes (Palmarès) -->
                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-crown text-yellow-400"></i> Palmarès d'Excellence (Top Moyennes)
                            </h3>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-yellow-500/10 text-yellow-400 border border-yellow-500/20 font-semibold">Excellence</span>
                        </div>
                        <div class="space-y-2.5 max-h-64 overflow-y-auto custom-scrollbar">
                            @forelse($meilleuresMoyennes as $idx => $e)
                                <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between hover:border-yellow-500/30 transition">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-7 h-7 rounded-lg {{ $idx === 0 ? 'bg-yellow-500 text-slate-950 font-black' : ($idx === 1 ? 'bg-slate-300 text-slate-950 font-bold' : ($idx === 2 ? 'bg-amber-700 text-white font-bold' : 'bg-slate-800 text-slate-300 font-medium')) }} flex items-center justify-center text-xs">
                                            {{ $idx + 1 }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-white">{{ $e['nom'] }} {{ $e['postnom'] }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $e['classe'] }} • {{ $e['option'] }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-black text-emerald-400 px-2 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                                        {{ number_format($e['moyenne'], 2) }}/20
                                    </span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500 py-6 text-center">Aucune cote disponible pour établir le palmarès.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Moyennes les plus faibles (À Soutenir) -->
                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-heart-pulse text-rose-400"></i> Élèves nécessitant un accompagnement
                            </h3>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 font-semibold">Suivi Pédagogique</span>
                        </div>
                        <div class="space-y-2.5 max-h-64 overflow-y-auto custom-scrollbar">
                            @forelse($moyennesPlusFaibles as $idx => $e)
                                <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between hover:border-rose-500/30 transition">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-400 border border-rose-500/20 flex items-center justify-center text-xs font-bold">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-white">{{ $e['nom'] }} {{ $e['postnom'] }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $e['classe'] }} • {{ $e['option'] }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-black text-rose-400 px-2 py-1 rounded-lg bg-rose-500/10 border border-rose-500/20">
                                        {{ number_format($e['moyenne'], 2) }}/20
                                    </span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-500 py-6 text-center">Aucun élève en difficulté identifié.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Résultats par Matière & Évolution par Période (Graphiques) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Évolution par Période (Line chart) -->
                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-chart-area text-indigo-400"></i> Évolution des résultats par période/semestre
                            </h3>
                            <span class="text-[11px] text-slate-500">Moyenne sur 20</span>
                        </div>
                        <div class="h-64 w-full">
                            <canvas id="chartEvolutionPeriodes"></canvas>
                        </div>
                    </div>

                    <!-- Résultats par Matière (Bar horizontal) -->
                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-book-open text-purple-400"></i> Résultats moyens par Matière
                            </h3>
                            <span class="text-[11px] text-slate-500">Moyenne / 20</span>
                        </div>
                        <div class="h-64 w-full">
                            <canvas id="chartResultatsMatieres"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECTION 3 : FINANCES & RECOUVREMENT        -->
            <!-- ========================================== -->
            <div class="space-y-4 pt-4 border-t border-slate-800/80">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg sm:text-xl font-black text-white flex items-center gap-2">
                        <i class="fa-solid fa-wallet text-emerald-400"></i> Finances & Recouvrement des Frais
                    </h2>
                    <span class="text-xs text-slate-400">Trésorerie globale</span>
                </div>

                <!-- 5 Cartes Financières -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="bg-slate-950/80 border border-slate-800/90 p-4 rounded-2xl shadow-lg">
                        <span class="text-[11px] font-bold uppercase text-slate-400 block"><i class="fa-solid fa-file-invoice-dollar mr-1"></i>Total Facturé</span>
                        <p class="text-xl sm:text-2xl font-black text-white mt-2">{{ number_format($totalFacture, 0, ',', ' ') }} $</p>
                        <p class="text-[10px] text-slate-500 mt-1">Montant global dû</p>
                    </div>

                    <div class="bg-slate-950/80 border border-slate-800/90 p-4 rounded-2xl shadow-lg">
                        <span class="text-[11px] font-bold uppercase text-slate-400 block"><i class="fa-solid fa-circle-check mr-1"></i>Déjà Payé</span>
                        <p class="text-xl sm:text-2xl font-black text-emerald-400 mt-2">{{ number_format($totalPaye, 0, ',', ' ') }} $</p>
                        <p class="text-[10px] text-emerald-500/80 mt-1 font-semibold">{{ $tauxRecouvrement }}% recouvré</p>
                    </div>

                    <div class="bg-slate-950/80 border border-slate-800/90 p-4 rounded-2xl shadow-lg">
                        <span class="text-[11px] font-bold uppercase text-slate-400 block"><i class="fa-solid fa-circle-exclamation mr-1"></i>Total Restant</span>
                        <p class="text-xl sm:text-2xl font-black text-rose-400 mt-2">{{ number_format($totalRestant, 0, ',', ' ') }} $</p>
                        <p class="text-[10px] text-slate-500 mt-1">Reste à encaisser</p>
                    </div>

                    <div class="bg-slate-950/80 border border-slate-800/90 p-4 rounded-2xl shadow-lg">
                        <span class="text-[11px] font-bold uppercase text-slate-400 block"><i class="fa-solid fa-receipt mr-1"></i>Paiements</span>
                        <p class="text-xl sm:text-2xl font-black text-cyan-400 mt-2">{{ $nombrePaiements }}</p>
                        <p class="text-[10px] text-slate-500 mt-1">Reçus enregistrés</p>
                    </div>

                    <div class="bg-slate-950/80 border border-slate-800/90 p-4 rounded-2xl shadow-lg col-span-2 sm:col-span-1">
                        <span class="text-[11px] font-bold uppercase text-slate-400 block"><i class="fa-solid fa-clock mr-1"></i>En retard</span>
                        <p class="text-xl sm:text-2xl font-black text-amber-400 mt-2">{{ $elevesEnRetard }}</p>
                        <p class="text-[10px] text-slate-500 mt-1">Élèves avec solde dû</p>
                    </div>
                </div>

                <!-- Graphique Évolution des Paiements & Derniers Encaissements -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Évolution des paiements (Ligne / Aire) -->
                    <div class="lg:col-span-2 bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                                <i class="fa-solid fa-chart-line text-emerald-400"></i> Évolution des encaissements par mois
                            </h3>
                            <span class="text-[11px] text-slate-500">Recettes en USD</span>
                        </div>
                        <div class="h-64 w-full">
                            <canvas id="chartEvolutionPaiements"></canvas>
                        </div>
                    </div>

                    <!-- Derniers paiements enregistrés -->
                    <div class="bg-slate-950/80 border border-slate-800/90 p-5 sm:p-6 rounded-2xl shadow-lg flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                                    <i class="fa-solid fa-receipt text-cyan-400"></i> Derniers Paiements
                                </h3>
                                <a href="{{ route('comptable.paiements.index') }}" class="text-xs text-blue-400 hover:underline">Journal</a>
                            </div>
                            <div class="space-y-2.5 max-h-56 overflow-y-auto custom-scrollbar">
                                @forelse($paiementsRecents as $p)
                                    <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-bold text-white">{{ $p->inscription->eleve->nom ?? 'Élève' }} {{ $p->inscription->eleve->postnom ?? '' }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $p->frais->intitule_frais ?? 'Frais' }} • {{ $p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '' }}</p>
                                        </div>
                                        <span class="text-xs font-black text-emerald-400 px-2 py-1 rounded bg-emerald-500/10 border border-emerald-500/20">
                                            +{{ number_format($p->montant_paye, 0, ',', ' ') }} $
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-500 py-6 text-center">Aucun paiement récent.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SECTION 4 : ACTIONS RAPIDES DE DIRECTION   -->
            <!-- ========================================== -->
            <div class="bg-slate-950/80 border border-slate-800/90 p-6 rounded-2xl shadow-lg">
                <h3 class="font-bold text-base text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-yellow-400"></i> Accès & Actions Rapides
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    <a href="{{ route('directeur.eleves.create') }}" class="p-3 rounded-xl bg-slate-900 hover:bg-slate-850 border border-slate-800 hover:border-amber-500/40 transition text-center group">
                        <i class="fa-solid fa-user-plus text-amber-400 text-lg mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-semibold text-slate-200 block">Nouvel élève</span>
                    </a>
                    <a href="{{ route('directeur.classes.index') }}" class="p-3 rounded-xl bg-slate-900 hover:bg-slate-850 border border-slate-800 hover:border-teal-500/40 transition text-center group">
                        <i class="fa-solid fa-school text-teal-400 text-lg mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-semibold text-slate-200 block">Gérer classes</span>
                    </a>
                    <a href="{{ route('options.create') }}" class="p-3 rounded-xl bg-slate-900 hover:bg-slate-850 border border-slate-800 hover:border-cyan-500/40 transition text-center group">
                        <i class="fa-solid fa-layer-group text-cyan-400 text-lg mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-semibold text-slate-200 block">Nouvelle option</span>
                    </a>
                    <a href="{{ route('directeur.cours.index') }}" class="p-3 rounded-xl bg-slate-900 hover:bg-slate-850 border border-slate-800 hover:border-indigo-500/40 transition text-center group">
                        <i class="fa-solid fa-book-bookmark text-indigo-400 text-lg mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-semibold text-slate-200 block">Gérer cours</span>
                    </a>
                    <a href="{{ route('directeur.enseignants') }}" class="p-3 rounded-xl bg-slate-900 hover:bg-slate-850 border border-slate-800 hover:border-purple-500/40 transition text-center group">
                        <i class="fa-solid fa-chalkboard-user text-purple-400 text-lg mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-semibold text-slate-200 block">Enseignants</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="p-3 rounded-xl bg-slate-900 hover:bg-slate-850 border border-slate-800 hover:border-rose-500/40 transition text-center group">
                        <i class="fa-solid fa-users-gear text-rose-400 text-lg mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-semibold text-slate-200 block">Utilisateurs</span>
                    </a>
                </div>
            </div>

        </main>
    </div>

    <!-- INITIALISATION DES GRAPHIQUES CHART.JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Configuration globale Chart.js
            Chart.defaults.color = '#94a3b8';
            Chart.defaults.font.family = 'system-ui, sans-serif';

            // 1. Graphique Évolution des Inscriptions par Mois
            const ctxInsc = document.getElementById('chartInscriptionsMois');
            if (ctxInsc) {
                new Chart(ctxInsc, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($inscriptionsParMoisLabels) !!},
                        datasets: [{
                            label: 'Inscriptions',
                            data: {!! json_encode($inscriptionsParMoisData) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.15)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointBackgroundColor: '#3b82f6',
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

            // 2. Graphique Répartition Genre (Doughnut)
            const ctxGenre = document.getElementById('chartGenre');
            if (ctxGenre) {
                new Chart(ctxGenre, {
                    type: 'doughnut',
                    data: {
                        labels: ['Garçons', 'Filles'],
                        datasets: [{
                            data: [{{ $totalGarcons }}, {{ $totalFilles }}],
                            backgroundColor: ['#06b6d4', '#ec4899'],
                            borderColor: '#020617',
                            borderWidth: 3,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } } },
                        cutout: '70%'
                    }
                });
            }

            // 3. Graphique Effectif par Classe (Bar)
            const ctxClasse = document.getElementById('chartEffectifClasse');
            if (ctxClasse) {
                new Chart(ctxClasse, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($classesLabels) !!},
                        datasets: [
                            {
                                label: 'Garçons',
                                data: {!! json_encode($classesGarcons) !!},
                                backgroundColor: '#06b6d4',
                                borderRadius: 6
                            },
                            {
                                label: 'Filles',
                                data: {!! json_encode($classesFilles) !!},
                                backgroundColor: '#ec4899',
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top', labels: { boxWidth: 12 } } },
                        scales: {
                            x: { stacked: true, grid: { display: false } },
                            y: { stacked: true, beginAtZero: true, grid: { color: 'rgba(51, 65, 85, 0.3)' } }
                        }
                    }
                });
            }

            // 4. Graphique Évolution par Période (Line)
            const ctxPeriodes = document.getElementById('chartEvolutionPeriodes');
            if (ctxPeriodes) {
                new Chart(ctxPeriodes, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($evolutionPeriodesLabels) !!},
                        datasets: [{
                            label: 'Moyenne générale (/20)',
                            data: {!! json_encode($evolutionPeriodesData) !!},
                            borderColor: '#818cf8',
                            backgroundColor: 'rgba(129, 140, 248, 0.15)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointBackgroundColor: '#818cf8',
                            pointRadius: 5
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

            // 5. Graphique Résultats par Matière (Bar Horizontal)
            const ctxMatieres = document.getElementById('chartResultatsMatieres');
            if (ctxMatieres) {
                const matLabels = {!! json_encode(array_slice(array_column($resultatsParMatiere, 'nom'), 0, 7)) !!};
                const matData = {!! json_encode(array_slice(array_column($resultatsParMatiere, 'moyenne'), 0, 7)) !!};
                new Chart(ctxMatieres, {
                    type: 'bar',
                    data: {
                        labels: matLabels,
                        datasets: [{
                            label: 'Moyenne (/20)',
                            data: matData,
                            backgroundColor: '#a855f7',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { min: 0, max: 20, grid: { color: 'rgba(51, 65, 85, 0.3)' } },
                            y: { grid: { display: false } }
                        }
                    }
                });
            }

            // 6. Graphique Évolution des Paiements (Line)
            const ctxPaiements = document.getElementById('chartEvolutionPaiements');
            if (ctxPaiements) {
                new Chart(ctxPaiements, {
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
                            pointRadius: 4
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
        });
    </script>
</body>
</html>
