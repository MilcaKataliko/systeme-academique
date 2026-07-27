<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin — Élève</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-xl font-black text-white">Bulletin / Résultats</h1>
                <p class="text-xs text-slate-400">{{ $eleve->nom_complet }}</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('eleve.bulletin') }}" class="flex gap-2">
                    <select name="annee_scolaire" class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                        <option value="">Toutes les années</option>
                        @foreach($anneesDisponibles as $annee)
                            <option value="{{ $annee }}" {{ $anneeScolaire == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-xs px-3 py-2 rounded-xl font-bold">Filtrer</button>
                </form>
                <a href="{{ route('eleve.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-xl transition flex items-center">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                </a>
            </div>
        </div>

        <!-- Résultats -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
            @if($bulletins->isEmpty())
                <div class="text-center text-slate-500 py-8">
                    <i class="fa-solid fa-scroll text-4xl mb-4"></i>
                    <p>Aucun résultat disponible pour le moment.</p>
                </div>
            @else
                @php
                    $grouped = $bulletins->groupBy(fn($item) => $item->classe . ' - ' . $item->annee_scolaire);
                @endphp
                @foreach($grouped as $groupe => $items)
                    <div class="mb-8 last:mb-0">
                        <h2 class="text-base font-bold text-white mb-4 border-b border-slate-800 pb-2">{{ $groupe }}</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-300">
                                <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                                    <tr>
                                        <th class="p-3">Cours</th>
                                        <th class="p-3">Option</th>
                                        <th class="p-3">Période</th>
                                        <th class="p-3">Max Période</th>
                                        <th class="p-3">Max Examen</th>
                                        <th class="p-3 text-right">Points obtenus</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800">
                                    @foreach($items as $item)
                                        <tr class="hover:bg-slate-800/50">
                                            <td class="p-3 font-semibold text-white">{{ $item->cours }}</td>
                                            <td class="p-3 text-slate-400">{{ $item->option }}</td>
                                            <td class="p-3 text-slate-400">{{ $item->periode }}</td>
                                            <td class="p-3 text-xs text-slate-500">{{ $item->max_periode }}</td>
                                            <td class="p-3 text-xs text-slate-500">{{ $item->max_examen }}</td>
                                            <td class="p-3 text-right">
                                                <span class="px-3 py-1 rounded-lg text-sm font-bold 
                                                    {{ $item->points >= 50 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                                    {{ $item->points }} / {{ $item->max_periode + $item->max_examen }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

    </div>

</body>
</html>

