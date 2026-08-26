<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de paiement — {{ $paiement->numero_recu }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans">

    <nav class="bg-slate-950 border-b border-slate-800 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="bg-blue-600 p-2 rounded-lg text-white font-black tracking-wider">EPST</div>
            <a href="{{ route('comptable.dashboard') }}" class="font-bold text-lg tracking-tight hover:text-emerald-400 transition">Comptabilité</a>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-slate-400 bg-slate-800 px-3 py-1.5 rounded-full border border-slate-700">
                <i class="fa-solid fa-calculator text-green-400 mr-2"></i>{{ Auth::user()->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-500/10 hover:bg-red-500 hover:text-white text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Déconnexion
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto p-6 md:p-8">

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Reçu -->
        <div class="bg-slate-950 border border-emerald-500/20 rounded-2xl overflow-hidden shadow-xl">
            <!-- En-tête du reçu -->
            <div class="bg-gradient-to-r from-emerald-900 to-slate-950 p-8 text-center border-b border-emerald-500/20">
                <div class="inline-flex bg-emerald-600 p-3 rounded-xl text-white font-black tracking-wider text-xl mb-4 shadow-lg">EPST</div>
                <h1 class="text-2xl font-black text-white">REÇU DE PAIEMENT</h1>
                <p class="text-emerald-400 text-sm mt-1">Système Académique</p>
                <p class="text-slate-400 text-xs mt-1">
                    <i class="fa-regular fa-calendar mr-1"></i> {{ $paiement->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>

            <div class="p-8 space-y-6">
                <!-- Numéro de reçu -->
                <div class="text-center">
                    <span class="text-xs text-slate-500 uppercase tracking-wider">Numéro de reçu</span>
                    <p class="text-xl font-mono font-black text-white tracking-wider">{{ $paiement->numero_recu }}</p>
                </div>

                <!-- Infos élève -->
                <div class="bg-slate-900/50 rounded-xl p-5 border border-slate-700/50">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Élève</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-slate-500 text-xs">Nom complet</p>
                            <p class="text-white font-medium">{{ $paiement->inscription->eleve->nom }} {{ $paiement->inscription->eleve->postnom }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">Matricule</p>
                            <p class="text-white font-mono">{{ $paiement->inscription->eleve->code_matricule }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">Classe</p>
                            <p class="text-white">{{ $paiement->inscription->classe->nom_classe }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">Année scolaire</p>
                            <p class="text-white">{{ $paiement->inscription->annee_scolaire }}</p>
                        </div>
                    </div>
                </div>

                <!-- Détails du paiement -->
                <div class="bg-slate-900/50 rounded-xl p-5 border border-slate-700/50">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Détails du paiement</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-slate-500 text-xs">Type de frais</p>
                            <p class="text-white font-medium">{{ $paiement->frais->intitule_frais }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">Montant payé</p>
                            <p class="text-emerald-400 font-bold text-lg">{{ number_format($paiement->montant_paye, 2) }} USD</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">Mode de paiement</p>
                            <p class="text-white capitalize">{{ str_replace('_', ' ', $paiement->mode_paiement) }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">Date de paiement</p>
                            <p class="text-white">{{ $paiement->date_paiement->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 text-xs">Enregistré par</p>
                            <p class="text-white">{{ $paiement->comptable->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-4 border-t border-slate-800">
                    <a href="{{ route('comptable.paiements.index') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Retour à la liste
                    </a>
                    <div class="flex space-x-2">
                        <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-xl text-sm font-semibold transition cursor-pointer">
                            <i class="fa-solid fa-print mr-2"></i> Imprimer
                        </button>
                        <form action="{{ route('comptable.paiements.destroy', $paiement->id) }}" method="POST"
                              onsubmit="return confirm('Supprimer ce paiement ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-500/10 hover:bg-red-500 hover:text-white text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-sm font-semibold transition cursor-pointer">
                                <i class="fa-solid fa-trash-can mr-2"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <style>
        @media print {
            nav, .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            main { max-width: 100% !important; }
        }
    </style>

</body>
</html>

