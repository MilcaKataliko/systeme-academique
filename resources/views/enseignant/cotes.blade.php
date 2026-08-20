<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encoder les cotes — {{ $cours->nom_cours }} — {{ $classe->nom_classe }}</title>
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

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-full mx-auto">

            <!-- En-tête -->
            <div class="bg-gradient-to-r from-purple-950 via-slate-950 to-slate-900 border border-purple-500/20 p-6 rounded-3xl shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-white">{{ $cours->nom_cours }}</h1>
                    <p class="text-slate-400 text-sm mt-1">
                        <i class="fa-solid fa-graduation-cap text-teal-400 mr-1.5"></i>{{ $classe->nom_classe }}
                        <span class="mx-2 text-slate-600">•</span>
                        <i class="fa-solid fa-users text-cyan-400 mr-1"></i>{{ $inscriptions->count() }} élèves
                        <span class="mx-2 text-slate-600">•</span>
                        <i class="fa-solid fa-book text-indigo-400 mr-1"></i>Max : {{ $plan->maxima_periode }}/{{ $plan->maxima_examen }}
                    </p>
                </div>
                <a href="{{ route('enseignant.dashboard') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 px-4 py-2 rounded-xl text-xs font-bold transition inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Tableau de bord
                </a>
            </div>

            <!-- Messages flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Sélecteur de cours (si plusieurs plans/cours pour cette classe) -->
            <div class="{{ $matiereValidee ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : 'bg-amber-500/10 border-amber-500/30 text-amber-200' }} border rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="font-bold"><i class="fa-solid {{ $matiereValidee ? 'fa-circle-check' : 'fa-clock' }} mr-2"></i>{{ $matiereValidee ? 'Matière validée pédagogiquement' : 'Bulletins en attente de validation' }}</p>
                    <p class="text-sm mt-1 opacity-80">Étape 1 : classe {{ $classe->nom_classe }}. Étape 2 : matière {{ $cours->nom_cours }}. Toute modification remet les bulletins en attente.</p>
                </div>
                @if(! $matiereValidee && $inscriptions->isNotEmpty())
                    <form method="POST" action="{{ route('enseignant.bulletins.matiere.valider', [$classe->id, $plan->id]) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Valider pédagogiquement cette matière pour toute la classe ?')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap cursor-pointer"><i class="fa-solid fa-check-double mr-2"></i>Valider la matière</button>
                    </form>
                @endif
            </div>

            @if($plans->count() > 1)
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl p-5 shadow-lg flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-purple-500/10 border border-purple-500/20 w-10 h-10 rounded-xl flex items-center justify-center text-purple-400">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cours actuel</p>
                        <p class="text-white font-bold text-base">{{ $cours->nom_cours }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-400 mr-2">Changer :</span>
                    <select onchange="window.location.href=this.value" 
                            class="bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-4 py-2 text-sm outline-none focus:border-purple-500 transition min-w-[200px]">
                        @foreach($plans as $p)
                            <option value="{{ route('enseignant.eleves.classe', [$classe->id, $p->id]) }}" 
                                    {{ $p->id == $plan->id ? 'selected' : '' }}>
                                {{ $p->cours->nom_cours }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            <!-- Formulaire d'encodage des cotes -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-xl">
                <form method="POST" action="{{ route('enseignant.cotes.store', $classe->id) }}">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                    <div class="p-6 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center space-x-3">
                            <label class="text-xs font-bold text-slate-300 uppercase tracking-wider whitespace-nowrap">Champ d'évaluation :</label>
                            <select name="champ" id="champ_select" onchange="changerChamp(this.value)"
                                    class="bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-4 py-2 text-sm outline-none focus:border-purple-500 transition">
                                <optgroup label="Semestre 1">
                                    <option value="interrogation_s1">Interrogation S1</option>
                                    <option value="devoir_domicile_s1">Devoir à domicile S1</option>
                                    <option value="periode_1">1ère Période</option>
                                    <option value="periode_2">2ème Période</option>
                                    <option value="periode_3">3ème Période</option>
                                    <option value="examen_s1">Examen S1</option>
                                </optgroup>
                                <optgroup label="Semestre 2">
                                    <option value="interrogation_s2">Interrogation S2</option>
                                    <option value="devoir_domicile_s2">Devoir à domicile S2</option>
                                    <option value="periode_4">4ème Période</option>
                                    <option value="periode_5">5ème Période</option>
                                    <option value="periode_6">6ème Période</option>
                                    <option value="examen_s2">Examen S2</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="text-xs text-slate-400">
                            Maximum : <strong id="max_info" class="text-purple-400 text-sm font-mono">{{ $plan->maxima_periode }} pts</strong>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                                <tr>
                                    <th class="p-4">Matricule</th>
                                    <th class="p-4">Nom complet</th>
                                    <th class="p-4">Note à saisir</th>
                                    <th class="p-4 text-center">Total pts</th>
                                    <th class="p-4 text-center">% Final</th>
                                    <th class="p-4 text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                @forelse($inscriptions as $insc)
                                    @php
                                        $cote = $insc->cotes->first();
                                    @endphp
                                    <tr class="hover:bg-slate-900/50 transition">
                                        <td class="p-4 font-mono text-xs text-purple-400">{{ $insc->eleve->code_matricule }}</td>
                                        <td class="p-4 font-semibold text-white">{{ $insc->eleve->nom }} {{ $insc->eleve->postnom }} {{ $insc->eleve->prenom }}</td>
                                        <td class="p-4">
                                            <input type="number" name="notes[{{ $insc->id }}]" step="0.5" min="0" max="100"
                                                   placeholder="—"
                                                   class="w-24 bg-slate-900 border border-slate-700 text-slate-100 rounded-lg px-3 py-1.5 text-sm font-mono outline-none focus:border-purple-500 transition">
                                        </td>
                                        <td class="p-4 text-center font-mono font-bold text-white">
                                            {{ $cote ? $cote->total_points : 0 }}
                                        </td>
                                        <td class="p-4 text-center font-mono font-bold text-emerald-400">
                                            {{ $cote && $cote->pourcentage !== null ? $cote->pourcentage . '%' : '—' }}
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($cote && $cote->pourcentage !== null)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $cote->pourcentage >= 50 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                                    {{ $cote->pourcentage >= 50 ? 'Réussi' : 'Échec' }}
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-600">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-12 text-center text-slate-500 text-xs">
                                            Aucun élève inscrit dans cette classe.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($inscriptions->isNotEmpty())
                    <div class="p-6 border-t border-slate-800 flex justify-between items-center bg-slate-950/40">
                        <p class="text-xs text-slate-400">
                            <i class="fa-solid fa-info-circle mr-1"></i> Les notes vides ne modifieront pas les évaluations déjà existantes.
                        </p>
                        <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white px-6 py-2.5 rounded-xl text-xs font-bold transition shadow-lg shadow-purple-600/30 inline-flex items-center space-x-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Enregistrer les notes</span>
                        </button>
                    </div>
                    @endif
                </form>
            </div>

        </main>
    </div>

    <script>
        function changerChamp(val) {
            const maxEl = document.getElementById('max_info');
            if (val.includes('examen')) {
                maxEl.textContent = '{{ $plan->maxima_examen }} pts';
            } else {
                maxEl.textContent = '{{ $plan->maxima_periode }} pts';
            }
        }
    </script>
</body>
</html>
