<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil — Espace Élève</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-cyan-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-4xl w-full mx-auto">

            <!-- En-tête / Carte d'identité scolaire -->
            <div class="bg-gradient-to-r from-cyan-950 via-slate-950 to-slate-900 border border-cyan-500/20 p-6 sm:p-8 rounded-3xl shadow-xl">
                <div class="flex items-center space-x-5">
                    @if($user->photo)
                        <div class="w-16 h-16 rounded-2xl overflow-hidden border border-cyan-500/40 flex items-center justify-center shrink-0 shadow-lg">
                            <img src="{{ asset('storage/photos/' . $user->photo) }}" alt="Photo de {{ $user->name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="bg-cyan-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg">
                            {{ substr($eleve->nom, 0, 1) }}{{ substr($eleve->postnom, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-black text-white">{{ $eleve->nom }} {{ $eleve->postnom }} {{ $eleve->prenom }}</h1>
                        <p class="text-cyan-400 text-xs font-mono mt-0.5"><i class="fa-solid fa-id-card mr-1"></i>{{ $eleve->code_matricule }}</p>
                        <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                            Élève Régulier
                        </span>
                    </div>
                </div>
            </div>

            <!-- Détails de la scolarité -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl p-6 shadow-lg space-y-6">
                <h2 class="font-bold text-base text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-graduate text-cyan-400"></i> Fiche d'Identité & Inscription
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                        <p class="text-xs text-slate-400 uppercase font-bold">Genre</p>
                        <p class="text-base font-semibold text-white mt-1">{{ $eleve->genre == 'M' ? 'Masculin' : 'Féminin' }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                        <p class="text-xs text-slate-400 uppercase font-bold">Date de Naissance</p>
                        <p class="text-base font-semibold text-white mt-1">{{ $eleve->date_naissance ? \Carbon\Carbon::parse($eleve->date_naissance)->format('d/m/Y') : 'Non renseignée' }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                        <p class="text-xs text-slate-400 uppercase font-bold">Lieu de Naissance</p>
                        <p class="text-base font-semibold text-white mt-1">{{ $eleve->lieu_naissance ?: 'Non renseigné' }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                        <p class="text-xs text-slate-400 uppercase font-bold">Classe Actuelle</p>
                        <p class="text-base font-semibold text-cyan-300 mt-1">{{ $inscriptions->first()?->classe?->nom_classe ?? 'Non assignée' }}</p>
                    </div>
                </div>
            </div>

        </main>
    </div>

</body>
</html>
