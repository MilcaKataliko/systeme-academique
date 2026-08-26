<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche élève — Directeur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-amber-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

        <!-- En-tête avec infos élève -->
        <div class="bg-gradient-to-r from-amber-900 to-slate-950 border border-amber-500/20 p-6 sm:p-8 rounded-2xl shadow-xl">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 {{ $eleve->genre == 'M' ? 'bg-blue-500/20 border-blue-500/30' : 'bg-pink-500/20 border-pink-500/30' }} border rounded-2xl flex items-center justify-center text-2xl">
                        <i class="fa-solid {{ $eleve->genre == 'M' ? 'fa-user' : 'fa-user-tie' }} text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black tracking-tight text-white">{{ $eleve->nom }} {{ $eleve->postnom }} {{ $eleve->prenom }}</h1>
                        <p class="text-slate-400 mt-1 text-sm">
                            <span class="font-mono bg-slate-800 px-2 py-0.5 rounded">{{ $eleve->code_matricule }}</span>
                            <span class="ml-3">Né(e) le {{ \Carbon\Carbon::parse($eleve->date_naissance)->format('d/m/Y') }} à {{ $eleve->lieu_naissance }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex space-x-2 shrink-0">
                    <a href="{{ route('directeur.eleves.edit', $eleve->id) }}" class="bg-amber-600 hover:bg-amber-500 text-white px-4 py-2 rounded-xl text-sm font-bold transition inline-flex items-center space-x-1">
                        <i class="fa-solid fa-pen"></i><span>Modifier</span>
                    </a>
                    <a href="{{ route('directeur.eleves.index') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center ml-2">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Messages flash -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Inscriptions et bulletins -->
        @forelse($eleve->inscriptions as $inscription)
            <div class="bg-slate-950 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="font-bold text-lg text-white">
                        <i class="fa-solid fa-school text-amber-400 mr-2"></i>
                        {{ $inscription->classe->nom_classe ?? 'N/A' }} 
                        <span class="text-sm font-normal text-slate-400">— {{ $inscription->annee_scolaire }}</span>
                        @if($inscription->classe->option)
                            <span class="text-xs bg-slate-800 text-slate-300 px-2 py-0.5 rounded-full ml-2">{{ $inscription->classe->option->nomoption }}</span>
                        @endif
                    </h2>
                    <a href="{{ route('directeur.eleves.bulletin', $inscription->id) }}" 
                       class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-xs font-bold transition inline-flex items-center space-x-1">
                        <i class="fa-solid fa-file-lines"></i><span>Bulletin</span>
                    </a>
                </div>

                @php
                    $cotesGrouped = $inscription->cotes->groupBy(function($c) {
                        return $c->periode->nom_periode ?? 'Inconnue';
                    });
                @endphp

                @if($cotesGrouped->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-900/50 border-b border-slate-800">
                                    <th class="text-left py-3 px-4 font-semibold text-xs text-slate-400 uppercase">Cours</th>
                                    @foreach($periodes as $periode)
                                        <th class="text-center py-3 px-3 font-semibold text-xs text-slate-400 uppercase">{{ $periode->nom_periode }}</th>
                                    @endforeach
                                    <th class="text-center py-3 px-3 font-semibold text-xs text-slate-400 uppercase">Moyenne</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $coursList = $inscription->cotes->groupBy(fn($c) => $c->plan->cours->nom_cours ?? 'N/A');
                                @endphp
                                @foreach($coursList as $coursNom => $cotesCours)
                                    <tr class="border-b border-slate-800/50">
                                        <td class="py-3 px-4 text-white">{{ $coursNom }}</td>
                                        @foreach($periodes as $periode)
                                            @php
                                                $cote = $cotesCours->firstWhere('periode_id', $periode->id);
                                            @endphp
                                            <td class="text-center py-3 px-3">
                                                @if($cote)
                                                    <span class="font-mono font-bold {{ $cote->points_obtenus >= 10 ? 'text-emerald-400' : 'text-red-400' }}">
                                                        {{ $cote->points_obtenus }}/{{ $cote->plan->maxima_periode ?? 20 }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-600">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        @php
                                            $cotesPeriodes = $cotesCours->filter(fn($c) => in_array($c->periode_id, $periodes->pluck('id')->toArray()));
                                            $moyenne = $cotesPeriodes->avg('points_obtenus');
                                        @endphp
                                        <td class="text-center py-3 px-3">
                                            <span class="font-mono font-bold {{ $moyenne && $moyenne >= 10 ? 'text-emerald-400' : 'text-red-400' }}">
                                                {{ $moyenne ? number_format($moyenne, 1) : '—' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-slate-500">
                        <i class="fa-solid fa-file-circle-minus text-2xl mb-2 block"></i>
                        <p class="text-sm">Aucune cote encodée pour cette inscription.</p>
                        <p class="text-xs text-slate-600 mt-1">Les notes apparaîtront ici après que l'enseignant les aura saisies.</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-8 text-center text-slate-500">
                <i class="fa-solid fa-user-slash text-3xl mb-3 block"></i>
                <p>Aucune inscription trouvée pour cet élève.</p>
            </div>
        @endforelse

        <!-- Zone dangereuse - Suppression -->
        <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-red-400 text-sm flex items-center">
                        <i class="fa-solid fa-triangle-exclamation mr-2"></i> Zone dangereuse
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Supprimer définitivement cet élève et toutes ses données (inscriptions, cotes, compte).</p>
                </div>
                <form action="{{ route('directeur.eleves.destroy', $eleve->id) }}" method="POST" 
                        onsubmit="return confirm('Supprimer {{ $eleve->nom }} {{ $eleve->postnom }} ? Toutes ses données (inscriptions, cotes, compte) seront perdues.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-600/20 hover:bg-red-600 hover:text-white text-red-400 border border-red-500/30 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer">
                        <i class="fa-solid fa-trash-can mr-1"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>

    </main>
</body>
</html>
