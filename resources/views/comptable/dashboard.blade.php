<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Comptable — Système Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Barre de Navigation -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div class="flex items-center space-x-3">
                <div class="bg-amber-600 p-2.5 rounded-xl text-white font-black text-lg">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-white">Espace Comptable</h1>
                    <p class="text-xs text-slate-400">Gestion des paiements et finances</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-xs bg-amber-500/10 border border-amber-500/20 text-amber-400 px-3 py-1.5 rounded-xl font-bold">
                    <i class="fa-solid fa-user mr-1"></i> {{ Auth::user()->name }}
                </span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 px-3 py-1.5 rounded-xl transition cursor-pointer">
                        <i class="fa-solid fa-power-off mr-1"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>

        <!-- Message de bienvenue -->
        <div class="bg-amber-500/5 border border-amber-500/20 p-6 rounded-2xl">
            <h2 class="text-lg font-bold text-white">Bienvenue, {{ Auth::user()->name }}</h2>
            <p class="text-xs text-slate-400 mt-1">Gérez les paiements des frais scolaires des élèves inscrits.</p>
        </div>

        <!-- Menu principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Card Paiements -->
            <a href="{{ route('comptable.paiements.index') }}" class="bg-slate-900 border border-slate-800 p-6 rounded-2xl hover:border-amber-500/50 transition block">
                <div class="bg-amber-500/10 text-amber-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-money-bill-wave text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Gestion des Paiements</h3>
                <p class="text-xs text-slate-400">Enregistrer, modifier, supprimer et rechercher des paiements effectués par les élèves.</p>
                <span class="inline-flex items-center text-xs font-bold text-amber-400 hover:text-amber-300 mt-4">
                    Accéder aux paiements <i class="fa-solid fa-arrow-right ml-2"></i>
                </span>
            </a>

            <!-- Card Reçus -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
                <div class="bg-emerald-500/10 text-emerald-400 w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-receipt text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Reçus & Rapports</h3>
                <p class="text-xs text-slate-400">Consultez l'historique des transactions et imprimez les reçus de paiement.</p>
            </div>
        </div>

    </div>

</body>
</html>

