<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrire un élève — Directeur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-amber-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-4xl w-full mx-auto">

        <!-- En-tête -->
        <div class="bg-gradient-to-r from-amber-900 to-slate-950 border border-amber-500/20 p-8 rounded-2xl shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white">Nouvel élève</h1>
                    <p class="text-slate-400 mt-2 text-sm">Inscrire un nouvel élève dans l'établissement.</p>
                </div>
                <a href="{{ route('directeur.eleves.index') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Retour
                </a>
            </div>
        </div>

        <!-- Messages flash -->
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

        <!-- Formulaire -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl shadow-xl">
            <form action="{{ route('directeur.eleves.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Nom <span class="text-red-400">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required maxlength="100"
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                        @error('nom') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Postnom <span class="text-red-400">*</span></label>
                        <input type="text" name="postnom" value="{{ old('postnom') }}" required maxlength="100"
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                        @error('postnom') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" maxlength="100"
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Genre <span class="text-red-400">*</span></label>
                        <select name="genre" required 
                                class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                            <option value="">Sélectionnez...</option>
                            <option value="M" {{ old('genre') == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('genre') == 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                        @error('genre') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Date de naissance <span class="text-red-400">*</span></label>
                        <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" required
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                        @error('date_naissance') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Lieu de naissance <span class="text-red-400">*</span></label>
                        <input type="text" name="lieu_naissance" value="{{ old('lieu_naissance') }}" required maxlength="150"
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                        @error('lieu_naissance') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Classe <span class="text-red-400">*</span></label>
                        <select name="classe_id" required 
                                class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                            <option value="">Sélectionnez...</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                    {{ $classe->nom_classe }} ({{ $classe->option->nomoption ?? 'N/A' }} — Niveau {{ $classe->niveau }})
                                </option>
                            @endforeach
                        </select>
                        @error('classe_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Année scolaire <span class="text-red-400">*</span></label>
                        <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', date('Y').'-'.(date('Y')+1)) }}" required
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                        @error('annee_scolaire') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <hr class="border-slate-800">

                <p class="text-xs text-slate-400 flex items-center">
                    <i class="fa-solid fa-info-circle mr-2 text-amber-400"></i> Optionnel : créer un compte utilisateur pour l'élève
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Email (compte élève)</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="eleve@exemple.com"
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                        @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Mot de passe</label>
                        <input type="password" name="password" placeholder="Laisser vide pour mot de passe par défaut"
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/40 w-full transition">
                        @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-amber-600 hover:bg-amber-500 text-white py-3 rounded-xl text-sm font-bold transition-all duration-150 cursor-pointer">
                    <i class="fa-solid fa-check mr-2"></i> Inscrire l'élève
                </button>
            </form>
        </div>

    </main>
</div>
</body>
</html>
