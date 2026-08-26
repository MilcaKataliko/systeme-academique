<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Notes — Espace Élève</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
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
                        <i class="fa-solid fa-list-check"></i> Relevé de Notes
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Mes Notes & Évaluations</h1>
                    <p class="text-slate-400 mt-1 text-sm">Détail de vos notes par matière, interrogation, devoir et examen.</p>
                </div>
            </div>

            @if($notesParCours->isEmpty())
                <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl p-12 text-center shadow-xl">
                    <i class="fa-solid fa-pen-to-square text-slate-600 text-4xl mb-3 block"></i>
                    <h2 class="text-lg font-bold text-white mb-1">Aucune note pour le moment</h2>
                    <p class="text-slate-400 text-xs">Les notes seront consultables dès que vos professeurs les auront encodées.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($notesParCours as $item)
                        <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                            <div class="p-4 sm:p-5 border-b border-slate-800 flex items-center justify-between">
                                <h2 class="font-bold text-base text-white flex items-center gap-2">
                                    <i class="fa-solid fa-book text-cyan-400"></i> {{ $item->cours->nom_cours }}
                                    <span class="text-xs text-slate-400 font-normal">({{ $item->classe->nom_classe ?? '—' }})</span>
                                </h2>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-400">Total :</span>
                                    <span class="text-sm font-black text-cyan-400 font-mono">{{ number_format($item->total_obtenu, 2) }} / {{ number_format($item->max_total, 2) }}</span>
                                    @if($item->pourcentage !== null)
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $item->pourcentage >= 50 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                            {{ number_format($item->pourcentage, 1) }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                                        <tr>
                                            <th class="p-3.5 px-4">Évaluation</th>
                                            <th class="p-3.5 px-4 text-center">Note</th>
                                            <th class="p-3.5 px-4 text-center">Maximum</th>
                                            <th class="p-3.5 px-4 text-center">Pourcentage</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                        @php
                                            $champs = [
                                                'interrogation_s1' => 'Interrogation S1',
                                                'devoir_domicile_s1' => 'Devoir Domicile S1',
                                                'periode_1' => '1ère Période',
                                                'periode_2' => '2ème Période',
                                                'periode_3' => '3ème Période',
                                                'examen_s1' => 'Examen S1',
                                                'interrogation_s2' => 'Interrogation S2',
                                                'devoir_domicile_s2' => 'Devoir Domicile S2',
                                                'periode_4' => '4ème Période',
                                                'periode_5' => '5ème Période',
                                                'periode_6' => '6ème Période',
                                                'examen_s2' => 'Examen S2',
                                            ];
                                            $maxPeriode = $item->max_periode;
                                            $maxExamen = $item->max_examen;
                                        @endphp
                                        @foreach($item->cotes as $cote)
                                            @foreach($champs as $champ => $libelle)
                                                @php
                                                    $max = in_array($champ, ['examen_s1', 'examen_s2']) ? $maxExamen : $maxPeriode;
                                                    $valeur = $cote->{$champ};
                                                @endphp
                                                @if($valeur !== null)
                                                    <tr class="hover:bg-slate-900/30 transition">
                                                        <td class="p-3 px-4 font-medium text-slate-200">{{ $libelle }}</td>
                                                        <td class="p-3 px-4 text-center font-mono font-bold text-white">{{ number_format($valeur, 2) }}</td>
                                                        <td class="p-3 px-4 text-center font-mono text-slate-500">{{ number_format($max, 0) }}</td>
                                                        <td class="p-3 px-4 text-center font-mono font-bold {{ ($valeur / $max) >= 0.5 ? 'text-emerald-400' : 'text-rose-400' }}">
                                                            {{ number_format(($valeur / $max) * 100, 1) }}%
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </main>
    </div>

</body>
</html>
