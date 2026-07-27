<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie des Cotes — Enseignant</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-xl font-black text-white">Saisie des Cotes</h1>
                <p class="text-xs text-slate-400">Sélectionnez un cours et une période pour encoder les notes</p>
            </div>
            <a href="{{ route('enseignant.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-xl transition flex items-center">
                <i class="fa-solid fa-arrow-left mr-1"></i> Retour
            </a>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-sm mb-4">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <!-- Sélection du cours et période -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
            <form method="GET" action="{{ route('enseignant.cotes.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <select name="plan_id" required class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="">Choisir un cours</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ $planId == $plan->id ? 'selected' : '' }}>
                            {{ $plan->cour->nom_cours ?? 'N/A' }} — {{ $plan->classe->nom_classe ?? 'N/A' }} ({{ $plan->annee_scolaire }})
                        </option>
                    @endforeach
                </select>
                <select name="periode_id" required class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="">Choisir une période</option>
                    @foreach($periodes as $periode)
                        <option value="{{ $periode->id }}" {{ $periodeId == $periode->id ? 'selected' : '' }}>
                            {{ $periode->nom_periode }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-xl text-sm transition cursor-pointer">
                    <i class="fa-solid fa-table mr-1"></i> Afficher les élèves
                </button>
            </form>
        </div>

        @if($selectedPlan && $selectedPeriode)
            <!-- Tableau des cotes -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-white">
                        {{ $selectedPlan->cour->nom_cours }} — {{ $selectedPlan->classe->nom_classe }}
                        <span class="text-xs text-slate-400 ml-2">({{ $selectedPeriode->nom_periode }})</span>
                    </h2>
                    <span class="text-xs text-slate-400">Max: {{ $selectedPlan->maxima_periode }}(P) / {{ $selectedPlan->maxima_examen }}(E)</span>
                </div>

                <form action="{{ route('enseignant.cotes.store-multiple') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $planId }}">
                    <input type="hidden" name="periode_id" value="{{ $periodeId }}">

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                                <tr>
                                    <th class="p-3">#</th>
                                    <th class="p-3">Élève</th>
                                    <th class="p-3 w-32">Points obtenus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse($cotes as $index => $cote)
                                    <tr class="hover:bg-slate-800/50">
                                        <td class="p-3 font-mono text-slate-500">{{ $index + 1 }}</td>
                                        <td class="p-3 font-semibold text-white">{{ $cote->eleve_nom }}</td>
                                        <td class="p-3">
                                            <input type="hidden" name="cotes[{{ $index }}][inscription_id]" value="{{ $cote->inscription_id }}">
                                            <input type="number" name="cotes[{{ $index }}][points_obtenus]" 
                                                value="{{ $cote->points_obtenus }}"
                                                min="0" max="100" step="0.25"
                                                class="w-24 bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white text-center focus:border-blue-500">
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="p-4 text-center text-slate-500">Aucun élève trouvé pour ce cours.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($cotes->isNotEmpty())
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 px-6 rounded-xl text-sm transition cursor-pointer">
                                <i class="fa-solid fa-save mr-1"></i> Enregistrer toutes les cotes
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        @endif

    </div>

</body>
</html>

