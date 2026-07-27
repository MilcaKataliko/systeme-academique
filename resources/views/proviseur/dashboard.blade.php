<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Proviseur — Direction Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6">

    <div class="max-w-7xl mx-auto space-y-6">

        <!-- Barre de Navigation Supérieure -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div class="flex items-center space-x-3">
                <div class="bg-indigo-600 p-2.5 rounded-xl text-white font-black text-lg">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-white">Espace Proviseur / Directeur des Études</h1>
                    <p class="text-xs text-slate-400">Gestion académique centralisée de l'établissement</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-xs bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 px-3 py-1.5 rounded-xl font-bold">
                    <i class="fa-solid fa-user-shield mr-1"></i> {{ Auth::user()->name }}
                </span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 px-3 py-1.5 rounded-xl transition cursor-pointer">
                        <i class="fa-solid fa-power-off mr-1"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>

        <!-- Bannières & Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Années Scolaires</p>
                    <h3 class="text-2xl font-black text-white mt-1">{{ $totalAnnees }}</h3>
                </div>
                <div class="bg-cyan-500/10 text-cyan-400 p-3 rounded-xl">
                    <i class="fa-solid fa-calendar-days text-xl"></i>
                </div>
            </div>
            <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Classes Configurées</p>
                    <h3 class="text-2xl font-black text-white mt-1">{{ $totalClasses }}</h3>
                </div>
                <div class="bg-blue-500/10 text-blue-400 p-3 rounded-xl">
                    <i class="fa-solid fa-chalkboard-user text-xl"></i>
                </div>
            </div>
            <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Élèves Enregistrés</p>
                    <h3 class="text-2xl font-black text-white mt-1">{{ $totalEleves }}</h3>
                </div>
                <div class="bg-emerald-500/10 text-emerald-400 p-3 rounded-xl">
                    <i class="fa-solid fa-user-graduate text-xl"></i>
                </div>
            </div>
            <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Cours</p>
                    <h3 class="text-2xl font-black text-white mt-1">{{ $totalCours }}</h3>
                </div>
                <div class="bg-purple-500/10 text-purple-400 p-3 rounded-xl">
                    <i class="fa-solid fa-book-open text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Grille des Modules de Gestion Académique -->
        <h2 class="text-base font-bold text-slate-300 uppercase tracking-wider pt-2">Modules Académiques</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Card: Années Scolaires -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-cyan-500/50 transition">
                <div class="bg-cyan-500/10 text-cyan-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-calendar-days text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Années Scolaires</h3>
                <p class="text-xs text-slate-400 mb-4">Définissez les années scolaires de l'établissement.</p>
                <a href="{{ route('proviseur.annees.index') }}" class="inline-flex items-center text-xs font-bold text-cyan-400 hover:text-cyan-300">
                    Gérer les années <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Card: Options -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-indigo-500/50 transition">
                <div class="bg-indigo-500/10 text-indigo-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-layer-group text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Options</h3>
                <p class="text-xs text-slate-400 mb-4">Commerciale & Gestion, Chimie-Biologie, Littéraire...</p>
                <a href="{{ route('proviseur.options.index') }}" class="inline-flex items-center text-xs font-bold text-indigo-400 hover:text-indigo-300">
                    Gérer les options <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Card: Classes -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-blue-500/50 transition">
                <div class="bg-blue-500/10 text-blue-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-door-open text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Classes</h3>
                <p class="text-xs text-slate-400 mb-4">Configurez les promotions (7ème, 8ème, 1ère, 2ème, 3ème, 4ème).</p>
                <a href="{{ route('proviseur.classes.index') }}" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300">
                    Gérer les classes <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Card: Élèves -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-emerald-500/50 transition">
                <div class="bg-emerald-500/10 text-emerald-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-id-card text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Registre des Élèves</h3>
                <p class="text-xs text-slate-400 mb-4">Enregistrez et gérez les fiches individuelles des élèves.</p>
                <a href="{{ route('proviseur.eleves.index') }}" class="inline-flex items-center text-xs font-bold text-emerald-400 hover:text-emerald-300">
                    Gérer les élèves <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Card: Périodes -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-rose-500/50 transition">
                <div class="bg-rose-500/10 text-rose-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-clock text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Périodes</h3>
                <p class="text-xs text-slate-400 mb-4">Définissez les périodes d'évaluation (1ère période, Examen...).</p>
                <a href="{{ route('proviseur.periodes.index') }}" class="inline-flex items-center text-xs font-bold text-rose-400 hover:text-rose-300">
                    Gérer les périodes <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Card: Cours -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-purple-500/50 transition">
                <div class="bg-purple-500/10 text-purple-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-book text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Cours / Matières</h3>
                <p class="text-xs text-slate-400 mb-4">Définissez les matières enseignées dans l'établissement.</p>
                <a href="{{ route('proviseur.cours.index') }}" class="inline-flex items-center text-xs font-bold text-purple-400 hover:text-purple-300">
                    Gérer les cours <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Card: Inscriptions -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-amber-500/50 transition">
                <div class="bg-amber-500/10 text-amber-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-file-signature text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Inscriptions</h3>
                <p class="text-xs text-slate-400 mb-4">Affectez les élèves dans leurs classes respectives.</p>
                <a href="{{ route('proviseur.inscriptions.index') }}" class="inline-flex items-center text-xs font-bold text-amber-400 hover:text-amber-300">
                    Gérer les inscriptions <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Card: Frais -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-green-500/50 transition">
                <div class="bg-green-500/10 text-green-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-money-bill text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Frais Scolaires</h3>
                <p class="text-xs text-slate-400 mb-4">Définissez les frais et associez-les aux classes.</p>
                <a href="{{ route('proviseur.frais.index') }}" class="inline-flex items-center text-xs font-bold text-green-400 hover:text-green-300">
                    Gérer les frais <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Card: Plans (Cours par classe) -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-yellow-500/50 transition">
                <div class="bg-yellow-500/10 text-yellow-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-table-cells-large text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Planification</h3>
                <p class="text-xs text-slate-400 mb-4">Attribuez les cours aux classes et aux enseignants.</p>
                <a href="{{ route('proviseur.plans.index') }}" class="inline-flex items-center text-xs font-bold text-yellow-400 hover:text-yellow-300">
                    Gérer les plans <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>

        </div>
    </div>

</body>
</html>

