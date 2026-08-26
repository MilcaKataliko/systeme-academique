<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Cours — Directeur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-indigo-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- En-tête -->
            <div class="bg-gradient-to-r from-indigo-950 via-slate-950 to-slate-900 border border-indigo-500/20 p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-book"></i> Programme Académique
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Gestion des Cours</h1>
                    <p class="text-slate-400 mt-1 text-sm">Créez et gérez les matières enseignées dans l'établissement.</p>
                </div>
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

            <!-- Statistiques -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xl border border-indigo-500/20">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $stats->total }}</p>
                        <p class="text-xs text-slate-400">Total cours</p>
                    </div>
                </div>
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-xl border border-purple-500/20">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $stats->attribues }}</p>
                        <p class="text-xs text-slate-400">Attribués aux classes</p>
                    </div>
                </div>
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xl border border-teal-500/20">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    </div>
                    <div>
                        <a href="{{ route('directeur.enseignants.attributions') }}" class="text-sm font-bold text-teal-400 hover:text-teal-300 transition block">
                            Attributions <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                        <p class="text-xs text-slate-400">Assigner aux profs</p>
                    </div>
                </div>
            </div>

            <!-- Grille Formulaire + Liste -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Formulaire Nouveau Cours -->
                <div class="lg:col-span-5 bg-slate-950/80 border border-slate-800/90 rounded-2xl p-6 shadow-lg h-fit space-y-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-indigo-400"></i> Nouveau cours
                    </h2>

                    <form action="{{ route('directeur.cours.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Intitulé du cours <span class="text-red-400">*</span></label>
                            <input type="text" name="nom_cours" value="{{ old('nom_cours') }}" placeholder="Ex: Mathématiques, Français..." required
                                   class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 transition">
                            @error('nom_cours')<div class="text-red-400 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Code du cours</label>
                            <input type="text" name="code_cours" value="{{ old('code_cours') }}" placeholder="Ex: MATH-01, FR-01"
                                   class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 transition">
                            <span class="text-[11px] text-slate-500 mt-1 block">Optionnel — identifiant court pour les bulletins</span>
                            @error('code_cours')<div class="text-red-400 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 rounded-xl text-xs font-bold transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-check"></i> Créer le cours
                        </button>
                    </form>
                </div>

                <!-- Liste des Cours -->
                <div class="lg:col-span-7 bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg flex flex-col justify-between">
                    <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                        <h2 class="font-bold text-base text-white flex items-center gap-2">
                            <i class="fa-solid fa-list text-indigo-400"></i> Cours existants
                        </h2>
                        <span class="text-xs text-slate-400 font-semibold">{{ $cours->count() }} cours</span>
                    </div>

                    <div class="divide-y divide-slate-800/60 max-h-[600px] overflow-y-auto custom-scrollbar">
                        @forelse($cours as $c)
                            <div class="p-4 sm:p-5 hover:bg-slate-900/40 transition flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-base text-white">{{ $c->nom_cours }}</span>
                                        @if($c->code_cours)
                                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                                {{ $c->code_cours }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-4 mt-2 text-xs text-slate-400">
                                        <span><i class="fa-solid fa-school text-teal-400 mr-1"></i>{{ $c->plans_count ?? 0 }} classe(s) associée(s)</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('directeur.cours.edit', $c->id) }}" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-semibold transition" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('directeur.cours.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement le cours {{ $c->nom_cours }} ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-500/10 hover:bg-rose-500 hover:text-white text-rose-400 rounded-lg text-xs font-semibold transition" title="Supprimer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-500 text-xs">
                                <i class="fa-solid fa-book-open text-3xl mb-2 block"></i>
                                Aucun cours enregistré pour l'instant.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </main>
    </div>

</body>
</html>
