<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil — Enseignant — Système Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-purple-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-4xl w-full mx-auto">

            <!-- En-tête -->
            <div class="bg-gradient-to-r from-purple-950 via-slate-950 to-slate-900 border border-purple-500/20 p-6 sm:p-8 rounded-3xl shadow-xl">
                <div class="flex items-center space-x-5">
                    @if($user->photo)
                        <div class="w-16 h-16 rounded-2xl overflow-hidden border border-purple-500/40 flex items-center justify-center shrink-0 shadow-lg">
                            <img src="{{ asset('storage/photos/' . $user->photo) }}" alt="Photo de {{ $user->name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="bg-purple-600 w-16 h-16 rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-lg">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-black text-white">{{ $user->name }}</h1>
                        <p class="text-purple-400 text-xs font-mono mt-0.5">{{ $user->email }}</p>
                        <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-500/10 text-purple-400 border border-purple-500/20">
                            Enseignant Titulaire
                        </span>
                    </div>
                </div>
            </div>

            <!-- Messages flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Détails du profil -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl p-6 shadow-lg space-y-6">
                <h2 class="font-bold text-base text-white flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-purple-400"></i> Coordonnées & Affectation
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                        <p class="text-xs text-slate-400 uppercase font-bold">Matricule Enseignant</p>
                        <p class="text-base font-mono font-bold text-purple-300 mt-1">{{ $enseignant->matricule ?? '—' }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                        <p class="text-xs text-slate-400 uppercase font-bold">Grade / Titre</p>
                        <p class="text-base font-semibold text-white mt-1">{{ $enseignant->grade ?? 'Licencié en Pédagogie' }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                        <p class="text-xs text-slate-400 uppercase font-bold">Téléphone</p>
                        <p class="text-base font-semibold text-white mt-1">{{ $enseignant->telephone ?? 'Non renseigné' }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                        <p class="text-xs text-slate-400 uppercase font-bold">Compte Créé le</p>
                        <p class="text-base font-semibold text-slate-300 mt-1">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '—' }}</p>
                    </div>
                </div>
            </div>

        </main>
    </div>

</body>
</html>
