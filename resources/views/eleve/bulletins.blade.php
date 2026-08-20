<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Bulletins — Espace Élève</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-cyan-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- En-tête -->
            <div class="bg-gradient-to-r from-cyan-950 via-slate-950 to-slate-900 border border-cyan-500/20 p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-award"></i> Synthèse Pédagogique
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Mes Bulletins Scolaires</h1>
                    <p class="text-slate-400 mt-1 text-sm">Consultez vos résultats certifiés par période et vos moyennes globales.</p>
                </div>
            </div>

            <!-- Synthèse Élève -->
            <section class="bg-slate-950/80 border border-slate-800/90 rounded-2xl p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 shadow-lg">
                <div><p class="text-xs uppercase tracking-wider text-slate-500 font-bold">Élève</p><p class="mt-1 font-bold text-white">{{ $eleve->nom }} {{ $eleve->postnom }} {{ $eleve->prenom }}</p></div>
                <div><p class="text-xs uppercase tracking-wider text-slate-500 font-bold">Matricule</p><p class="mt-1 font-mono font-bold text-cyan-300">{{ $eleve->code_matricule }}</p></div>
                <div><p class="text-xs uppercase tracking-wider text-slate-500 font-bold">Classe</p><p class="mt-1 font-bold text-white">{{ $inscriptions->first()?->classe?->nom_classe ?? 'Non renseignée' }}</p></div>
                <div><p class="text-xs uppercase tracking-wider text-slate-500 font-bold">Moyenne générale</p><p class="mt-1 font-mono text-xl font-black {{ ($resumeBulletin['moyenne'] ?? 0) >= 10 ? 'text-emerald-400' : 'text-rose-400' }}">{{ $resumeBulletin['moyenne'] !== null ? number_format($resumeBulletin['moyenne'], 2) . ' / 20' : '—' }}</p></div>
            </section>

            @if($bulletinRows->isNotEmpty())
                <section class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                    <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="font-bold text-base text-white flex items-center gap-2">
                                <i class="fa-solid fa-file-lines text-cyan-400"></i> Bulletin en cours
                            </h2>
                            <p class="text-xs text-slate-400 mt-0.5">Mis à jour dès l’enregistrement d’une cote par l’enseignant.</p>
                        </div>
                        <span class="text-xs bg-amber-500/10 text-amber-300 border border-amber-500/20 px-3 py-1 rounded-full font-semibold">Résultats provisoires</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                                <tr>
                                    <th class="p-3.5 px-4">Cours</th>
                                    <th class="p-3.5 px-4">Cotes enregistrées</th>
                                    <th class="p-3.5 px-4 text-center">Moyenne /20</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                @foreach($bulletinRows as $row)
                                    <tr class="hover:bg-slate-900/40 transition">
                                        <td class="py-3.5 px-4 text-white font-semibold">{{ $row->plan->cours->nom_cours }}</td>
                                        <td class="py-3.5 px-4">
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($row->notes as $note)
                                                    <span class="rounded-md border border-cyan-500/20 bg-cyan-500/10 px-2 py-0.5 font-mono text-xs text-cyan-200">
                                                        {{ $note['libelle'] }} : {{ $note['note'] }}/{{ $note['maximum'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-mono font-black {{ $row->moyenne >= 10 ? 'text-emerald-400' : 'text-rose-400' }}">
                                            {{ number_format($row->moyenne, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            @if($periodes->isEmpty() && $bulletinRows->isEmpty())
                <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl p-12 text-center shadow-xl">
                    <i class="fa-solid fa-file-pdf text-slate-600 text-4xl mb-3 block"></i>
                    <h2 class="text-base font-bold text-white mb-1">Aucun bulletin disponible</h2>
                    <p class="text-slate-400 text-xs">Aucune période avec note validée n’est encore disponible pour vous.</p>
                </div>
            @else
                @foreach($periodes as $periode)
                    <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                            <h2 class="font-bold text-base text-white flex items-center gap-2">
                                <i class="fa-solid fa-file-lines text-amber-400"></i> Bulletin — {{ $periode->nom_periode }}
                            </h2>
                            <span class="text-xs bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3 py-1 rounded-full font-semibold">Période clôturée</span>
                        </div>

                        @php
                            $cotesPeriode = collect();
                            foreach ($inscriptions as $inscription) {
                                foreach ($inscription->cotes->where('periode_id', $periode->id) as $cote) {
                                    $cotesPeriode->push($cote);
                                }
                            }
                        @endphp

                        @if($cotesPeriode->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                                        <tr>
                                            <th class="p-3.5 px-4">Cours</th>
                                            <th class="p-3.5 px-4 text-center">Total</th>
                                            <th class="p-3.5 px-4 text-center">Max</th>
                                            <th class="p-3.5 px-4 text-center">Moyenne /20</th>
                                            <th class="p-3.5 px-4 text-center">%</th>
                                            <th class="p-3.5 px-4 text-center">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                        @foreach($cotesPeriode->groupBy('plan_id') as $planId => $groupeCotes)
                                            @php
                                                $plan = $groupeCotes->first()->plan;
                                                $total = $groupeCotes->sum('total_points');
                                                $max = $groupeCotes->sum('max_total');
                                                $moy = $max > 0 ? round(($total / $max) * 20, 2) : null;
                                                $pourcent = $max > 0 ? round(($total / $max) * 100, 1) : null;
                                                $statut = $pourcent !== null && $pourcent >= 55 ? 'Réussi' : ($pourcent !== null ? 'Échoué' : null);
                                            @endphp
                                            <tr class="hover:bg-slate-900/30 transition">
                                                <td class="p-3.5 px-4 text-white font-medium">{{ $plan->cours->nom_cours }}</td>
                                                <td class="p-3.5 px-4 text-center font-mono font-bold text-white">{{ number_format($total, 2) }}</td>
                                                <td class="p-3.5 px-4 text-center text-slate-500">{{ number_format($max, 2) }}</td>
                                                <td class="p-3.5 px-4 text-center font-mono font-bold {{ $moy && $moy >= 10 ? 'text-emerald-400' : ($moy ? 'text-rose-400' : 'text-slate-500') }}">
                                                    {{ $moy ? number_format($moy, 2) . '/20' : '—' }}
                                                </td>
                                                <td class="p-3.5 px-4 text-center font-mono {{ $pourcent !== null && $pourcent >= 55 ? 'text-emerald-400' : ($pourcent !== null ? 'text-rose-400' : 'text-slate-500') }}">
                                                    {{ $pourcent !== null ? number_format($pourcent, 1) . '%' : '—' }}
                                                </td>
                                                <td class="p-3.5 px-4 text-center">
                                                    @if($statut === 'Réussi')
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                            <i class="fa-solid fa-circle-check mr-1.5"></i>Réussi
                                                        </span>
                                                    @elseif($statut === 'Échoué')
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                            <i class="fa-solid fa-circle-xmark mr-1.5"></i>Échoué
                                                        </span>
                                                    @else
                                                        <span class="text-slate-500">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif

        </main>
    </div>

</body>
</html>
