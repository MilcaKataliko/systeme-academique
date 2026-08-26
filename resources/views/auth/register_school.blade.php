<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Établissement — Système Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-slate-950 via-blue-950 to-slate-950 px-4 py-12">
    
    <div class="w-full max-w-2xl bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-2xl shadow-2xl">
        
        <div class="text-center mb-8">
            <span class="text-xs font-semibold tracking-widest text-blue-400 uppercase bg-blue-500/10 px-3 py-1 rounded-full border border-blue-500/20">
                Agrément & Ouverture de Session Locale
            </span>
            <h2 class="text-3xl font-black text-white mt-4 tracking-tight">Inscrire votre Établissement</h2>
            <p class="text-sm text-slate-400 mt-2">Enregistrez votre école pour initialiser l'espace de direction</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('school.register.store') }}" class="space-y-6">
            @csrf

            <!-- SECTION 1 : L'ÉCOLE -->
            <div class="border-b border-white/10 pb-6">
                <h3 class="text-sm font-bold text-blue-400 uppercase tracking-wider mb-4 flex items-center">
                    <i class="fa-solid fa-school mr-2"></i> Information sur l'Établissement
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Nom de l'école</label>
                        <input class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" type="text" name="nom_ecole" value="{{ old('nom_ecole') }}" required placeholder="ex: Institut de Goma" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Code National EPST</label>
                        <input class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" type="text" name="code_national_epst" value="{{ old('code_national_epst') }}" required placeholder="ex: EPST-9901" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Province Éducationnelle</label>
                        <input class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" type="text" name="province_educationnelle" value="{{ old('province_educationnelle') }}" required placeholder="ex: Nord-Kivu 1" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Adresse Physique</label>
                        <input class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" type="text" name="adresse" value="{{ old('adresse') }}" required placeholder="ex: Av. du Lac, Goma" />
                    </div>
                </div>
            </div>

            <!-- SECTION 2 : LE DIRECTEUR -->
            <div>
                <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider mb-4 flex items-center">
                    <i class="fa-solid fa-user-tie mr-2"></i> Identifiants du Directeur (Administrateur)
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Nom Complet du Directeur</label>
                        <input class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" type="text" name="name" value="{{ old('name') }}" required placeholder="ex: Milca Kataliko" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Adresse Email Professionnelle</label>
                        <input class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" type="email" name="email" value="{{ old('email') }}" required placeholder="ex: directeur@ecole.cd" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Mot de passe</label>
                            <input class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" type="password" name="password" required placeholder="Votre mot de passe" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase mb-2">Confirmer le mot de passe</label>
                            <input class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" type="password" name="password_confirmation" required placeholder="Confirmez le mot de passe" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOUTON D'ACTION -->
            <div class="pt-4">
                <button type="submit" class="w-full py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 active:scale-[0.98] transition-all duration-150 cursor-pointer">
                    Valider et Initialiser le Système
                </button>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour à la page de connexion
                </a>
            </div>
        </form>
    </div>
</body>
</html>