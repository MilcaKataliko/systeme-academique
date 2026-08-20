<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un cours — Directeur</title>
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

    <main class="max-w-2xl mx-auto p-6 md:p-8 space-y-8">

        <!-- En-tête -->
        <div class="bg-gradient-to-r from-indigo-900 to-slate-950 border border-indigo-500/20 p-8 rounded-2xl shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white">Modifier le cours</h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        <span class="text-white font-semibold">{{ $cours->nom_cours }}</span>
                        @if($cours->code_cours)
                            · <span class="text-indigo-400 font-mono">{{ $cours->code_cours }}</span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('directeur.cours.index') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Retour
                </a>
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
        @if($errors->any())
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire de modification -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-6 py-4 border-b border-slate-800">
                <h2 class="font-bold text-lg text-white">
                    <i class="fa-solid fa-pen text-indigo-400 mr-3"></i>Modifier les informations
                </h2>
            </div>

            <form action="{{ route('directeur.cours.update', $cours->id) }}" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-book mr-2"></i>Nom du cours <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="nom_cours" value="{{ old('nom_cours', $cours->nom_cours) }}" 
                           required maxlength="255"
                           class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/40 w-full transition">
                    <p class="text-slate-500 text-[10px] mt-1">Le nom complet de la matière enseignée</p>
                    @error('nom_cours')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-tag mr-2"></i>Code du cours <span class="text-slate-500">(optionnel)</span>
                    </label>
                    <input type="text" name="code_cours" value="{{ old('code_cours', $cours->code_cours) }}" 
                           placeholder="Ex: MATH" maxlength="20"
                           class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/40 w-full transition">
                    <p class="text-slate-500 text-[10px] mt-1">Abréviation courte pour identifier la matière</p>
                    @error('code_cours')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex space-x-3 pt-2">
                    <button type="submit" 
                            class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white py-2.5 rounded-xl text-sm font-bold transition-all duration-150 cursor-pointer">
                        <i class="fa-solid fa-save mr-2"></i> Enregistrer
                    </button>
                    <a href="{{ route('directeur.cours.index') }}" 
                       class="bg-slate-800 hover:bg-slate-700 text-slate-300 py-2.5 px-6 rounded-xl text-sm font-bold transition inline-flex items-center">
                        Annuler
                    </a>
                </div>
            </form>
        </div>

    </main>

</body>
</html>
