<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Options — Directeur</title>
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
                        <i class="fa-solid fa-layer-group"></i> Filières & Sections
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Options d'Études</h1>
                    <p class="text-slate-400 mt-1 text-sm">Configurez les sections et filières de l'établissement (Commerciale, Scientifique, Pédagogique...).</p>
                </div>
                <a href="{{ route('options.create') }}" class="bg-cyan-600 hover:bg-cyan-500 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition inline-flex items-center space-x-2 shadow-lg shadow-cyan-600/30">
                    <i class="fa-solid fa-plus"></i>
                    <span>Ajouter une option</span>
                </a>
            </div>

            <!-- Messages flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Tableau des Options -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="font-bold text-base text-white flex items-center gap-2">
                        <i class="fa-solid fa-list text-cyan-400"></i> Options configurées
                    </h2>
                    <span class="text-xs text-slate-400 font-semibold">{{ $options->count() }} option(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                            <tr>
                                <th class="p-4">Nom de l'Option</th>
                                <th class="p-4">Code / Sigle</th>
                                <th class="p-4">Classes liées</th>
                                <th class="p-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            @forelse($options as $option)
                                <tr class="hover:bg-slate-900/50 transition">
                                    <td class="p-4 font-semibold text-white">{{ $option->nomoption }}</td>
                                    <td class="p-4 font-mono text-xs text-cyan-400 font-bold uppercase">
                                        <span class="px-2 py-0.5 rounded bg-cyan-500/10 border border-cyan-500/20">
                                            {{ $option->sigle ?: '—' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-xs text-slate-400">
                                        @if($option->classes_count > 0)
                                            <span class="px-2.5 py-1 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 font-semibold">
                                                {{ $option->classes_count }} classe(s)
                                            </span>
                                        @else
                                            <span class="text-slate-600">Aucune classe liée</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <form action="{{ route('options.destroy', $option->idOption) }}" method="POST"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer l\'option « {{ $option->nomoption }} » ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-500/10 hover:bg-rose-500 hover:text-white text-rose-400 rounded-lg text-xs font-semibold transition" title="Supprimer">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-layer-group text-3xl mb-2 block"></i>
                                        Aucune option enregistrée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>
