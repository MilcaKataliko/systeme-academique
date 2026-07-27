<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Directeur — Système Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans">

    <!-- Barre de navigation -->
    <nav class="bg-slate-950 border-b border-slate-800 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="bg-blue-600 p-2 rounded-lg text-white font-black tracking-wider">EPST</div>
            <span class="font-bold text-lg tracking-tight">Système Académique</span>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-slate-400 bg-slate-800 px-3 py-1.5 rounded-full border border-slate-700">
                <i class="fa-solid fa-user-tie text-blue-400 mr-2"></i>Directeur : <span class="text-sm">Directeur : **{{ Auth::user()->name }}**</span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-500/10 hover:bg-red-500 hover:text-white text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Déconnexion
                </button>
            </form>
        </div>
    </nav>

    <!-- Conteneur principal -->
    <main class="max-w-7xl mx-auto p-6 md:p-8 space-y-8">
        
        <!-- En-tête de bienvenue -->
        <div class="bg-gradient-to-r from-blue-900 to-slate-950 border border-blue-500/20 p-8 rounded-2xl shadow-xl">
            <h1 class="text-3xl font-black tracking-tight text-white">Tableau de bord de direction</h1>
            <p class="text-slate-400 mt-2 text-sm">Gestion centralisée de l'établissement secondaire — Session active cloisonnée.</p>
        </div>

        <!-- Grille des modules de gestion -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Carte Option -->
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl hover:border-blue-500/40 transition duration-200 shadow-lg flex flex-col justify-between">
                <div>
                    <div class="bg-blue-500/10 border border-blue-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-blue-400 text-xl mb-4">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h3 class="font-bold text-lg text-white">Options d'Études</h3>
                    <p class="text-xs text-slate-400 mt-1">Configurez les sections et filières de ton école (Commerciale, Chimie-Bio...).</p>
                </div>
                <a href="#" class="mt-6 inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 transition">
                    Gérer les options <i class="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
                </a>
            </div>

            <!-- Carte Années Scolaires -->
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl hover:border-emerald-500/40 transition duration-200 shadow-lg flex flex-col justify-between">
                <div>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-emerald-400 text-xl mb-4">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <h3 class="font-bold text-lg text-white">Années Scolaires</h3>
                    <p class="text-xs text-slate-400 mt-1">Ouvrez, clôturez et gérez les différents calendriers académiques.</p>
                </div>
                <a href="#" class="mt-6 inline-flex items-center text-xs font-bold text-emerald-400 hover:text-emerald-300 transition">
                    Gérer les années <i class="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
                </a>
            </div>

            <!-- Carte Enseignants -->
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl hover:border-purple-500/40 transition duration-200 shadow-lg flex flex-col justify-between">
                <div>
                    <div class="bg-purple-500/10 border border-purple-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-purple-400 text-xl mb-4">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <h3 class="font-bold text-lg text-white">Corps Enseignant</h3>
                    <p class="text-xs text-slate-400 mt-1">Attribuez les classes et supervisez l'encodage des cotes.</p>
                </div>
                <a href="#" class="mt-6 inline-flex items-center text-xs font-bold text-purple-400 hover:text-purple-300 transition">
                    Voir les professeurs <i class="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
                </a>
            </div>

            <!-- Carte Élèves -->
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl hover:border-amber-500/40 transition duration-200 shadow-lg flex flex-col justify-between">
                <div>
                    <div class="bg-amber-500/10 border border-amber-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-amber-400 text-xl mb-4">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h3 class="font-bold text-lg text-white">Inscriptions Élèves</h3>
                    <p class="text-xs text-slate-400 mt-1">Gérez le registre matricule, les fiches d'inscription et les bulletins.</p>
                </div>
                <a href="#" class="mt-6 inline-flex items-center text-xs font-bold text-amber-400 hover:text-amber-300 transition">
                    Consulter le registre <i class="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
                </a>
            </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Carte Option -->
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl hover:border-blue-500/40 transition duration-200 shadow-lg flex flex-col justify-between">
                <div>
                    <div class="bg-blue-500/10 border border-blue-500/20 w-12 h-12 rounded-xl flex items-center justify-center text-blue-400 text-xl mb-4">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h3 class="font-bold text-lg text-white">Gerer les utilisateurs</h3>
                </div>
                    <a href="{{ url('/proviseur/eleves') }}" class="...">                        <i class="fa-solid fa-users mr-2"></i> Gérer le personnel & utilisateurs
                    </a>
            </div>

        </div>

    </main>

</body>
</html>