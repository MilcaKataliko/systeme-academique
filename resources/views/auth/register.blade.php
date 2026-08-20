<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Compte — Système Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-slate-950 via-blue-950 to-slate-950 px-4 py-12">
    
    <div class="w-full max-w-md bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-2xl shadow-2xl">
        
        <div class="text-center mb-8">
            <div class="inline-flex bg-blue-600 p-3 rounded-xl text-white font-black tracking-wider text-xl mb-4 shadow-lg shadow-blue-600/20">
                EPST
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">Créer un compte personnel</h2>
            <p class="text-xs text-slate-400 mt-2">Ajouter un enseignant, comptable ou élève à l'établissement</p>
        </div>

        @if(session('success'))
            <div class="mb-5 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-xs text-center flex items-center justify-center space-x-2">
                <i class="fa-solid fa-circle-check text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf

            <!-- Nom complet -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Nom complet</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150" 
                        placeholder="Nom et prénom" />
                </div>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Adresse Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150" 
                        placeholder="email@exemple.com" />
                </div>
            </div>

            <!-- Rôle -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Rôle</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-user-tag"></i>
                    </span>
                    <select name="role" required
                        class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150 appearance-none">
                        <option value="" disabled selected>Choisir un rôle</option>
                        <option value="enseignant" {{ old('role') == 'enseignant' ? 'selected' : '' }}>Enseignant</option>
                        <option value="comptable" {{ old('role') == 'comptable' ? 'selected' : '' }}>Comptable</option>
                        <option value="eleve" {{ old('role') == 'eleve' ? 'selected' : '' }}>Élève</option>
                    </select>
                </div>
            </div>

            <!-- Mot de passe -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Mot de passe</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required
                        class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150" 
                        placeholder="Minimum 6 caractères" />
                </div>
            </div>

            <!-- Confirmation mot de passe -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Confirmer le mot de passe</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password_confirmation" required
                        class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150" 
                        placeholder="Répéter le mot de passe" />
                </div>
            </div>

            <!-- Bouton -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 active:scale-[0.98] transition-all duration-150 cursor-pointer flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Créer le compte</span>
                </button>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('directeur.dashboard') }}" class="text-xs text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour au tableau de bord
                </a>
            </div>
        </form>
    </div>
</body>
</html>
