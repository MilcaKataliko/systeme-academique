<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier élève — Directeur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans">

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
                <button type="submit" class="bg-red-500/10 hover:bg-red-500 hover:text-white text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-sm font-semibold transition cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Déconnexion
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto p-6 md:p-8 space-y-8">

        <!-- En-tête -->
        <div class="bg-gradient-to-r from-amber-900 to-slate-950 border border-amber-500/20 p-8 rounded-2xl shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white">Modifier l'élève</h1>
                    <p class="text-slate-400 mt-2 text-sm">{{ $eleve->nom }} {{ $eleve->postnom }} — {{ $eleve->code_matricule }}</p>
                </div>
                <a href="{{ route('directeur.eleves.show', $eleve->id) }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Retour
                </a>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl shadow-xl">
            <form action="{{ route('directeur.eleves.update', $eleve->id) }}" method="POST" class="p-6 space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Nom <span class="text-red-400">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom', $eleve->nom) }}" required 
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                        @error('nom') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Postnom <span class="text-red-400">*</span></label>
                        <input type="text" name="postnom" value="{{ old('postnom', $eleve->postnom) }}" required
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                        @error('postnom') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $eleve->prenom) }}"
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Genre <span class="text-red-400">*</span></label>
                        <select name="genre" required 
                                class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                            <option value="M" {{ $eleve->genre == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ $eleve->genre == 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Date naissance <span class="text-red-400">*</span></label>
                        <input type="date" name="date_naissance" value="{{ old('date_naissance', $eleve->date_naissance) }}" required
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                        @error('date_naissance') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Lieu naissance <span class="text-red-400">*</span></label>
                        <input type="text" name="lieu_naissance" value="{{ old('lieu_naissance', $eleve->lieu_naissance) }}" required
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                        @error('lieu_naissance') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-amber-600 hover:bg-amber-500 text-white py-3 rounded-xl text-sm font-bold transition-all duration-150 cursor-pointer">
                    <i class="fa-solid fa-save mr-2"></i> Enregistrer les modifications
                </button>
            </form>
        </div>

    </main>
</body>
</html>
