<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des cotes — {{ $classe->nom_classe }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans">

    <!-- Navigation -->
    <nav class="bg-slate-950 border-b border-slate-800 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="bg-blue-600 p-2 rounded-lg text-white font-black tracking-wider">EPST</div>
            <a href="{{ route('directeur.dashboard') }}" class="font-bold text-lg tracking-tight hover:text-blue-400 transition">Système Académique</a>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-slate-400 bg-slate-800 px-3 py-1.5 rounded-full border border-slate-700">
                <i class="fa-solid fa-user-tie text-blue-400 mr-2"></i>{{ Auth::user()->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-500/10 hover:bg-red-500 hover:text-white text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Déconnexion
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-full mx-auto p-6 md:p-8 space-y-8">

        <!-- En-tête -->
        <div class="bg-gradient-to-r from-pink-900 to-slate-950 border border-pink-500/20 p-8 rounded-2xl shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white">
                        <i class="fa-solid fa-pen-to-square text-pink-400 mr-3"></i>Gestion des cotes
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        {{ $classe->nom_classe }} 
                        @if($classe->option)<span class="ml-2 text-xs bg-blue-500/10 text-blue-400 px-2 py-0.5 rounded-full">{{ $classe->option->nomoption }}</span>@endif
                        <i class="fa-solid fa-circle text-[5px] mx-2 align-middle"></i>
                        {{ $inscriptions->count() }} élève(s)
                    </p>
                </div>
                <a href="{{ route('directeur.classes.index') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Retour aux classes
                </a>
                <a href="{{ route('directeur.bulletin.validation') }}" class="text-sm text-amber-400 hover:text-amber-300 transition inline-flex items-center ml-4">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> Validation des imports
                </a>
            </div>
        </div>

        <!-- Messages flash -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Sélecteur de cours -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="bg-pink-500/10 border border-pink-500/20 w-10 h-10 rounded-xl flex items-center justify-center text-pink-400">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Cours à noter</p>
                        <p class="text-white font-bold text-lg">{{ $plan->cours->nom_cours }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-400 mr-2">Changer de cours :</span>
                    <select onchange="window.location.href=this.value" 
                            class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-pink-500 transition min-w-[200px]">
                        @foreach($plans as $p)
                            <option value="{{ route('directeur.eleves.cotes.classe', [$classe->id, $p->id]) }}" 
                                    {{ $p->id == $plan->id ? 'selected' : '' }}>
                                {{ $p->cours->nom_cours }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if($plan->cours)
            <div class="mt-3 flex items-center space-x-4 text-xs text-slate-500 border-t border-slate-800 pt-3">
                <span><i class="fa-solid fa-arrow-up-wide-short text-emerald-400 mr-1"></i> Max période : <strong class="text-white">{{ $plan->maxima_periode }}</strong>/20</span>
                <span><i class="fa-solid fa-pen text-emerald-400 mr-1"></i> Max examen : <strong class="text-white">{{ $plan->maxima_examen }}</strong>/20</span>
            </div>
            @endif
        </div>

        <!-- Tableau des cotes pour le cours sélectionné -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="font-bold text-lg text-white flex items-center">
                    <i class="fa-solid fa-table text-pink-400 mr-3"></i>{{ $plan->cours->nom_cours }}
                </h2>
                <span class="text-xs text-slate-400">Cliquez sur une note pour la modifier</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 bg-slate-900/50">
                            <th class="text-left py-3 px-3 font-semibold text-slate-300 uppercase text-[10px] tracking-wider sticky left-0 bg-slate-900/50 min-w-[200px]">Élève</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[80px]">Int. S1</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[80px]">Dév. S1</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[70px]">P1</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[70px]">P2</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[70px]">P3</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[80px]">Ex. S1</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[80px]">Int. S2</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[80px]">Dév. S2</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[70px]">P4</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[70px]">P5</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[70px]">P6</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[80px]">Ex. S2</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[60px]">Total</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[60px]">Max</th>
<th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[70px]">%</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[60px]">Moy.</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[80px]">Présence %</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[60px]">Bonus</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[70px]">% Final</th>
                            <th class="text-center py-3 px-2 font-semibold text-slate-300 uppercase text-[10px] tracking-wider min-w-[70px]">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $champs = [
                                'interrogation_s1' => 'Int. S1',
                                'devoir_domicile_s1' => 'Dév. S1',
                                'periode_1' => 'P1',
                                'periode_2' => 'P2',
                                'periode_3' => 'P3',
                                'examen_s1' => 'Ex. S1',
                                'interrogation_s2' => 'Int. S2',
                                'devoir_domicile_s2' => 'Dév. S2',
                                'periode_4' => 'P4',
                                'periode_5' => 'P5',
                                'periode_6' => 'P6',
                                'examen_s2' => 'Ex. S2',
                            ];
                        @endphp
                        @forelse($inscriptions as $inscription)
                            @php
                                $cote = $inscription->cotes->firstWhere('plan_id', $plan->id);
                            @endphp
                            <tr class="border-b border-slate-800/50 hover:bg-slate-900/30 transition">
                                <td class="py-2 px-3 text-white font-medium sticky left-0 bg-slate-950">
                                    <span class="font-mono text-[10px] text-slate-500 mr-1">{{ $inscription->eleve->code_matricule }}</span>
                                    {{ $inscription->eleve->nom }} {{ $inscription->eleve->postnom }}
                                </td>

                                @foreach($champs as $champ => $label)
                                    @php
                                        $val = $cote ? $cote->{$champ} : null;
                                        $max = (in_array($champ, ['examen_s1', 'examen_s2'])) ? $plan->maxima_examen : $plan->maxima_periode;
                                    @endphp
                                    <td class="text-center py-1.5 px-1">
                                        <form action="{{ route('directeur.eleves.cotes.update') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                            <input type="hidden" name="champ" value="{{ $champ }}">
                                            @if($cote)<input type="hidden" name="cote_id" value="{{ $cote->id }}">@endif
                                            <input type="number" name="valeur" 
                                                   value="{{ $val }}"
                                                   step="0.5"
                                                   min="0"
                                                   max="{{ $max }}"
                                                   class="w-16 text-center bg-transparent border border-slate-700/40 hover:border-pink-500/50 focus:border-pink-500 text-slate-100 rounded-lg py-1.5 text-xs outline-none focus:ring-1 focus:ring-pink-500/30 transition"
                                                   onchange="this.form.submit()"
                                                   placeholder="—">
                                        </form>
                                    </td>
                                @endforeach

                                <!-- Total -->
                                <td class="text-center py-2 px-2 font-mono font-bold text-sm {{ $cote && $cote->total_points > 0 ? 'text-emerald-400' : 'text-slate-600' }}">
                                    {{ $cote ? number_format($cote->total_points, 1) : '—' }}
                                </td>
                                <!-- Max total -->
                                <td class="text-center py-2 px-2 font-mono text-sm text-slate-500">
                                    {{ $cote ? number_format($cote->max_total, 1) : '—' }}
                                </td>
                                <!-- Pourcentage -->
                                <td class="text-center py-2 px-2">
                                    @if($cote && $cote->pourcentage !== null)
                                        <span class="font-mono font-bold text-sm {{ $cote->pourcentage >= 50 ? 'text-emerald-400' : 'text-red-400' }}">
                                            {{ $cote->pourcentage }}%
                                        </span>
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
<!-- Moyenne -->
                                <td class="text-center py-2 px-2">
                                    @if($cote && $cote->moyenne !== null)
                                        <span class="font-mono font-bold text-sm {{ $cote->moyenne >= 10 ? 'text-emerald-400' : 'text-red-400' }}">
                                            {{ $cote->moyenne }}
                                        </span>
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                                <!-- Présence -->
                                <td class="text-center py-2 px-2">
                                    @if($cote && $cote->pourcentage_presence !== null)
                                        <span class="font-mono font-bold text-sm {{ $cote->pourcentage_presence >= 100 ? 'text-emerald-400' : ($cote->pourcentage_presence >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                                            {{ $cote->pourcentage_presence }}%
                                        </span>
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                                <!-- Bonus -->
                                <td class="text-center py-2 px-2">
                                    @if($cote && $cote->bonus_presence > 0)
                                        <span class="font-mono font-bold text-sm text-amber-400">+{{ $cote->bonus_presence }}%</span>
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                                <!-- % Final -->
                                <td class="text-center py-2 px-2">
                                    @if($cote && $cote->pourcentage_final !== null)
                                        <span class="font-mono font-bold text-sm text-white">{{ $cote->pourcentage_final }}%</span>
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                                <!-- Statut -->
                                <td class="text-center py-2 px-2">
                                    @if($cote && $cote->statut)
                                        @if($cote->statut === 'Réussi')
                                            <span class="inline-flex px-2 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-[10px] font-bold">
                                                <i class="fa-solid fa-check-circle mr-1"></i>Réussi
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 rounded-full bg-red-500/10 border border-red-500/30 text-red-400 text-[10px] font-bold">
                                                <i class="fa-solid fa-xmark-circle mr-1"></i>Échoué
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="21" class="py-12 text-center text-slate-500">
                                    <i class="fa-solid fa-users-slash text-3xl mb-3 block"></i>
                                    <p>Aucun élève inscrit dans cette classe.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Légende -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs text-slate-400">
                <div>
                    <p class="font-bold text-slate-300 mb-2 uppercase tracking-wider text-[10px]"><i class="fa-solid fa-book mr-1"></i>Légende</p>
                    <div class="space-y-1">
                        <p><span class="text-pink-400 font-medium">Int.</span> = Interrogation</p>
                        <p><span class="text-pink-400 font-medium">Dév.</span> = Devoir à domicile</p>
                        <p><span class="text-pink-400 font-medium">P</span> = Période</p>
                        <p><span class="text-pink-400 font-medium">Ex.</span> = Examen</p>
                    </div>
                </div>
                <div>
                    <p class="font-bold text-slate-300 mb-2 uppercase tracking-wider text-[10px]"><i class="fa-solid fa-chart-column mr-1"></i>Calculs</p>
                    <div class="space-y-1">
<p><span class="text-emerald-400">Total</span> = Somme de toutes les notes</p>
                        <p><span class="text-emerald-400">Max</span> = Point maximum du cours</p>
                        <p><span class="text-emerald-400">%</span> = (Total / Max) × 100</p>
                        <p><span class="text-emerald-400">Moy.</span> = Total / Nb évaluations</p>
                        <p><span class="text-emerald-400">Présence</span> = % de présence (100% = bonus 5%)</p>
                        <p><span class="text-emerald-400">% Final</span> = % obtenu + bonus</p>
                        <p><span class="text-emerald-400">Statut</span> = Réussi si % final ≥ 55%</p>
                    </div>
                </div>
                <div>
                    <p class="font-bold text-slate-300 mb-2 uppercase tracking-wider text-[10px]"><i class="fa-solid fa-lightbulb mr-1"></i>Astuces</p>
                    <div class="space-y-1">
                        <p>Cliquez sur une note pour modifier</p>
                        <p>Sauvegarde automatique</p>
                        <p>{{ $plan->cours->nom_cours }} uniquement</p>
                    </div>
                </div>
                <div>
                    <p class="font-bold text-slate-300 mb-2 uppercase tracking-wider text-[10px]"><i class="fa-solid fa-chart-line mr-1"></i>Stats</p>
                    <div class="space-y-1">
                        <p><i class="fa-solid fa-users text-slate-500 mr-1"></i> {{ $inscriptions->count() }} élèves</p>
                        <p><i class="fa-solid fa-book text-slate-500 mr-1"></i> {{ $plans->count() }} cours disponibles</p>
                        <a href="{{ route('directeur.eleves.index') }}" class="text-pink-400 hover:text-pink-300 transition inline-flex items-center mt-1">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Registre élèves
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </main>

</body>
</html>
