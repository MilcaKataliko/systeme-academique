<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Classes — Directeur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-teal-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- Hero Banner -->
            <div class="bg-gradient-to-r from-teal-950 via-slate-950 to-slate-900 border border-teal-500/20 p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-500/20 text-teal-400 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-school"></i> Structure Pédagogique
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Gestion des Classes</h1>
                    <p class="text-slate-400 mt-1 text-sm">Créez, modifiez et organisez les classes de l'établissement par niveau et option.</p>
                </div>
            </div>

            <!-- Messages Flash -->
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
                    <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xl border border-teal-500/20">
                        <i class="fa-solid fa-school"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $stats->total }}</p>
                        <p class="text-xs text-slate-400">Total classes</p>
                    </div>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xl border border-blue-500/20">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $stats->total_eleves }}</p>
                        <p class="text-xs text-slate-400">Élèves inscrits</p>
                    </div>
                </div>

                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-xl border border-purple-500/20">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white">{{ $stats->options }}</p>
                        <p class="text-xs text-slate-400">Options disponibles</p>
                    </div>
                </div>
            </div>

            <!-- Grille 2 Colonnes : Création & Liste -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Formulaire Nouvelle Classe -->
                <div class="lg:col-span-5 bg-slate-950/80 border border-slate-800/90 rounded-2xl p-6 shadow-lg h-fit space-y-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-teal-400"></i> Nouvelle classe
                    </h2>
                    
                    <div class="p-3.5 rounded-xl bg-blue-950/40 border border-blue-500/20 text-xs text-slate-300 leading-relaxed">
                        <strong class="text-blue-300 block mb-1">Guide de saisie :</strong>
                        <i class="fa-solid fa-circle text-[5px] mr-1 align-middle"></i> <strong>Nom</strong> : ex: "1ère Commerciale A", "7ème EB"<br>
                        <i class="fa-solid fa-circle text-[5px] mr-1 align-middle"></i> <strong>Niveau</strong> : 7ème, 8ème, 1ère à 4ème humanités.<br>
                        <i class="fa-solid fa-circle text-[5px] mr-1 align-middle"></i> <strong>Option</strong> : Optionnel pour 7e/8e, obligatoire dès la 1ère.
                    </div>

                    <form action="{{ route('directeur.classes.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Nom de la classe <span class="text-red-400">*</span></label>
                            <input type="text" name="nom_classe" value="{{ old('nom_classe') }}" placeholder="Ex: 1ère Commerciale A" required
                                   class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-teal-500 transition">
                            @error('nom_classe')<div class="text-red-400 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Niveau <span class="text-red-400">*</span></label>
                                <select name="niveau" required onchange="toggleOption(this.value)"
                                        class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-teal-500 transition">
                                    <option value="">Choisir</option>
                                    @foreach([7,8,1,2,3,4] as $niv)
                                        <option value="{{ $niv }}" {{ old('niveau')==$niv?'selected':'' }}>{{ $niv }}{{ $niv==1?'ère':'ème' }}</option>
                                    @endforeach
                                </select>
                                @error('niveau')<div class="text-red-400 text-xs mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Option</label>
                                <select name="option_id" id="opt_sel"
                                        class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-teal-500 transition">
                                    <option value="">Pas d'option</option>
                                    @foreach($options as $opt)
                                        <option value="{{ $opt->idOption }}" {{ old('option_id')==$opt->idOption?'selected':'' }}>{{ $opt->nomoption }}</option>
                                    @endforeach
                                </select>
                                @error('option_id')<div class="text-red-400 text-xs mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-teal-600 hover:bg-teal-500 text-white py-2.5 rounded-xl text-xs font-bold transition shadow-lg shadow-teal-600/30 flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-check"></i> Créer la classe
                        </button>
                    </form>
                </div>

                <!-- Liste des Classes -->
                <div class="lg:col-span-7 bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg flex flex-col justify-between">
                    <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                        <h2 class="font-bold text-base text-white flex items-center gap-2">
                            <i class="fa-solid fa-list text-teal-400"></i> Classes existantes
                        </h2>
                        <span class="text-xs text-slate-400 font-semibold">{{ $classes->count() }} classe(s)</span>
                    </div>

                    <div class="divide-y divide-slate-800/60 max-h-[600px] overflow-y-auto custom-scrollbar">
                        @forelse($classes as $classe)
                            <div class="p-4 sm:p-5 hover:bg-slate-900/40 transition flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-base text-white">{{ $classe->nom_classe }}</span>
                                        @if($classe->option)
                                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                                {{ $classe->option->nomoption }}
                                            </span>
                                        @endif
                                        <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-300">
                                            Niveau {{ $classe->niveau }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4 mt-2 text-xs text-slate-400">
                                        <span><i class="fa-solid fa-user-graduate text-cyan-400 mr-1"></i><strong>{{ $classe->nb_inscriptions ?? 0 }}</strong> élève(s)</span>
                                        <span><i class="fa-solid fa-book text-indigo-400 mr-1"></i><strong>{{ $classe->nb_plans ?? 0 }}</strong> cours</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('directeur.classes.edit', $classe->id) }}" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-semibold transition" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('directeur.classes.destroy', $classe->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement la classe {{ $classe->nom_classe }} ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-500/10 hover:bg-rose-500 hover:text-white text-rose-400 rounded-lg text-xs font-semibold transition" title="Supprimer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-500 text-xs">
                                <i class="fa-solid fa-school-circle-exclamation text-3xl mb-2 block"></i>
                                Aucune classe enregistrée.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script>
        function toggleOption(val) {
            var s = document.getElementById('opt_sel');
            if (s) {
                if (val == '7' || val == '8') { s.removeAttribute('required'); }
                else { s.setAttribute('required', 'required'); }
            }
        }
    </script>
</body>
</html>
