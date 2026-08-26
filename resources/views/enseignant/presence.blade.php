<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Présences — {{ $cours->nom_cours }} — Enseignant</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-purple-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-full mx-auto">

            <!-- En-tête avec navigation de semaine -->
            <div class="bg-gradient-to-r from-purple-950 via-slate-950 to-slate-900 border border-purple-500/20 p-6 rounded-3xl shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-white">Feuille de Présence</h1>
                    <p class="text-slate-400 mt-1 text-sm">
                        <i class="fa-solid fa-book text-purple-400 mr-1.5"></i>{{ $cours->nom_cours }}
                        <span class="mx-2 text-slate-600">|</span>
                        <i class="fa-solid fa-school text-teal-400 mr-1.5"></i>{{ $classe->nom_classe }}
                    </p>
                </div>

                <!-- Navigation de semaine -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('enseignant.presence.form', [$classe->id, $plan->id, $semainePrecedente]) }}"
                       class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 px-3 py-2 rounded-xl text-xs font-bold transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-chevron-left"></i>Semaine préc.
                    </a>
                    <span class="text-xs text-slate-300 bg-purple-600/20 border border-purple-500/30 px-4 py-2 rounded-xl font-semibold whitespace-nowrap">
                        <i class="fa-solid fa-calendar-week text-purple-400 mr-2"></i>
                        Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
                    </span>
                    <a href="{{ route('enseignant.presence.form', [$classe->id, $plan->id, $semaineSuivante]) }}"
                       class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 px-3 py-2 rounded-xl text-xs font-bold transition inline-flex items-center gap-1.5">
                        Semaine suiv.<i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>

            <!-- Messages flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Formulaire des Présences -->
            <form method="POST" action="{{ route('enseignant.presence.store', [$classe->id, $plan->id]) }}" class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-xl">
                @csrf
                <input type="hidden" name="semaine_debut" value="{{ $dateDebut }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                            <tr>
                                <th class="p-4">Élève</th>
                                @foreach($joursDeSemaine as $j)
                                    <th class="p-4 text-center">
                                        <div>{{ $j['nom'] }}</div>
                                        <div class="text-[10px] text-slate-500 font-normal">{{ $j['date_formatee'] }}</div>
                                    </th>
                                @endforeach
                                <th class="p-4 text-center">% Présence</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            @forelse($inscriptions as $insc)
                                @php
                                    $statsSemaine = $inscriptionsStats[$insc->id] ?? ['p' => 0, 'total' => 0];
                                    $pourcSemaine = $statsSemaine['total'] > 0 ? round(($statsSemaine['p'] / $statsSemaine['total']) * 100) : null;
                                @endphp
                                <tr class="hover:bg-slate-900/40 transition">
                                    <td class="p-4">
                                        <p class="font-semibold text-white">{{ $insc->eleve->nom }} {{ $insc->eleve->postnom }} {{ $insc->eleve->prenom }}</p>
                                        <p class="text-xs text-purple-400 font-mono">{{ $insc->eleve->code_matricule }}</p>
                                    </td>
                                    @foreach($joursDeSemaine as $j)
                                        @php
                                            $cle = $insc->id . '_' . $j['date'];
                                            $statut = $presencesIndex[$cle] ?? 'present';
                                        @endphp
                                        <td class="p-3 text-center">
                                            <select name="presences[{{ $insc->id }}][{{ $j['date'] }}]"
                                                    class="text-xs rounded-lg px-2 py-1.5 font-semibold border outline-none cursor-pointer transition
                                                        {{ $statut === 'present' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : '' }}
                                                        {{ $statut === 'absent' ? 'bg-red-500/10 text-red-400 border-red-500/30' : '' }}
                                                        {{ $statut === 'retard' ? 'bg-amber-500/10 text-amber-400 border-amber-500/30' : '' }}
                                                        {{ $statut === 'justifie' ? 'bg-blue-500/10 text-blue-400 border-blue-500/30' : '' }}">
                                                <option value="present" {{ $statut === 'present' ? 'selected' : '' }}>P (Présent)</option>
                                                <option value="absent" {{ $statut === 'absent' ? 'selected' : '' }}>A (Absent)</option>
                                                <option value="retard" {{ $statut === 'retard' ? 'selected' : '' }}>R (Retard)</option>
                                                <option value="justifie" {{ $statut === 'justifie' ? 'selected' : '' }}>J (Justifié)</option>
                                            </select>
                                        </td>
                                    @endforeach
                                    <td class="p-4 text-center">
                                        @if($pourcSemaine !== null)
                                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-lg
                                                {{ $pourcSemaine >= 100 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($pourcSemaine >= 50 ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20') }}">
                                                <i class="fa-solid {{ $pourcSemaine >= 100 ? 'fa-circle-check' : 'fa-chart-line' }}"></i>
                                                {{ $pourcSemaine }}%
                                            </span>
                                        @else
                                            <span class="text-slate-600 text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-12 text-center text-slate-500 text-xs">
                                        Aucun élève inscrit dans cette classe.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-950/40">
                    <p class="text-xs text-slate-500">
                        <i class="fa-solid fa-circle-info text-purple-400 mr-1"></i>
                        Le % de présence est calculé sur les jours enregistrés. L'élève bénéficie de 5% de bonus si sa présence globale atteint 100%.
                    </p>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2.5 rounded-xl text-xs font-bold transition shadow-lg shadow-emerald-600/30 inline-flex items-center space-x-2 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Enregistrer les présences</span>
                    </button>
                </div>
            </form>

        </main>
    </div>

</body>
</html>
