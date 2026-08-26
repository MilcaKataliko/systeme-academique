<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Système Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-slate-950 via-blue-950 to-slate-950 px-4 py-12">
    
    <div class="w-full max-w-md bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-2xl shadow-2xl">
        
        <!-- En-tête du Formulaire -->
        <div class="text-center mb-8">
            <div class="inline-flex bg-blue-600 p-3 rounded-xl text-white font-black tracking-wider text-xl mb-4 shadow-lg shadow-blue-600/20">
                EPST
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">Système Académique</h2>
            <p class="text-xs text-slate-400 mt-2">Authentifiez-vous pour accéder à votre espace de travail</p>
        </div>

        <!-- Affichage des Erreurs de Connexion -->
        @if($errors->any())
            <div class="mb-5 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs text-center flex items-center justify-center space-x-2">
                <i class="fa-solid fa-circle-exclamation text-sm"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Formulaire de Connexion -->
        <form method="POST" action="{{ url('/login') }}" class="space-y-5">
            @csrf <!-- Protège contre l'erreur 419 | Page Expired -->

            <!-- Champ Email -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Identifiant (Email)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150" 
                        placeholder="directeur@ecole.cd" />
                </div>
            </div>

            <!-- Champ Mot de passe -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Mot de passe</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required
                        class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150" 
                        placeholder="Votre mot de passe" />
                </div>
            </div>

            <!-- Option Se souvenir de moi -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-0 focus:ring-offset-0 cursor-pointer">
                    <span class="ml-2 text-xs text-slate-400 hover:text-slate-300">Se souvenir de moi</span>
                </label>
            </div>

            <!-- Bouton de Soumission -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 active:scale-[0.98] transition-all duration-150 cursor-pointer flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>S'authentifier sur l'espace</span>
                </button>
            </div>

            <!-- Section : Inscription d'un nouvel établissement -->
            <div class="text-center mt-6 border-t border-white/10 pt-4">
                <a href="{{ route('school.register.show') }}" class="text-xs text-blue-400 hover:text-blue-300 hover:underline transition inline-flex items-center">
                    <i class="fa-solid fa-school-flag mr-2"></i> Inscrire un nouvel Établissement Secondaire
                </a>
            </div>

        </form>
    </div>

</body>
</html>