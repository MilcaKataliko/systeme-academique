<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grille des Frais — Comptable</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-emerald-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- En-tête -->
            <div class="bg-gradient-to-r from-emerald-950 via-slate-950 to-slate-900 border border-emerald-500/20 p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Tarification
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Gestion de la Grille des Frais</h1>
                    <p class="text-slate-400 mt-1 text-sm">Définissez les frais scolaires par classe, par montant et par calendrier académique.</p>
                </div>
            </div>

            <!-- Messages flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Formulaire Création Frais -->
                <div class="lg:col-span-5 bg-slate-950/80 border border-slate-800/90 rounded-2xl p-6 shadow-lg h-fit space-y-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-emerald-400"></i> Nouveau frais
                    </h2>

                    <div class="p-3.5 rounded-xl bg-blue-950/40 border border-blue-500/20 text-xs text-slate-300 leading-relaxed">
                        <strong class="text-blue-300 block mb-1"><i class="fa-solid fa-lightbulb mr-1"></i>Exemple :</strong>
                        Créez <span class="text-emerald-400 font-semibold">"Minerval 1ère Commerciale A"</span> → montant <span class="text-emerald-400 font-bold">50$</span> → classe ciblée → année active.
                    </div>

                    <form action="{{ route('comptable.frais.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Intitulé du frais <span class="text-red-400">*</span></label>
                            <input type="text" name="intitule_frais" value="{{ old('intitule_frais') }}" required
                                   placeholder="Ex: Minerval, Frais d'inscription..."
                                   class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Montant <span class="text-red-400">*</span></label>
                                <input type="number" name="montant" value="{{ old('montant') }}" step="0.01" min="0" required
                                       class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Devise</label>
                                <select name="devise" required
                                        class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                                    <option value="USD">USD ($)</option>
                                    <option value="CDF">CDF (FC)</option>
                                    <option value="EUR">EUR (€)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Classe concernée <span class="text-red-400">*</span></label>
                            <select name="classe_id" required
                                    class="w-full bg-slate-900 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                                <option value="">Choisir une classe...</option>
                                @foreach($classes as $classe)
                                    <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                        {{ $classe->niveau }} {{ $classe->nom_classe }}@if($classe->option) ({{ $classe->option->nomoption }})@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-2.5 rounded-xl text-xs font-bold transition shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-check"></i> Créer le frais
                        </button>
                    </form>
                </div>

                <!-- Liste des Frais Existants -->
                <div class="lg:col-span-7 bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg flex flex-col justify-between">
                    <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                        <h2 class="font-bold text-base text-white flex items-center gap-2">
                            <i class="fa-solid fa-list text-emerald-400"></i> Frais enregistrés
                        </h2>
                        <span class="text-xs text-slate-400 font-semibold">{{ $frais->count() }} frais</span>
                    </div>

                    <div class="divide-y divide-slate-800/60 max-h-[600px] overflow-y-auto custom-scrollbar">
                        @forelse($frais as $f)
                            <div class="p-4 sm:p-5 hover:bg-slate-900/40 transition flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-bold text-base text-white">{{ $f->intitule_frais }}</p>
                                    <div class="flex items-center gap-3 mt-1.5 text-xs text-slate-400">
                                        <span class="text-emerald-400 font-mono font-bold">{{ number_format($f->montant, 2) }} {{ $f->devise }}</span>
                                        <span>•</span>
                                        <span><i class="fa-solid fa-school text-teal-400 mr-1"></i>{{ $f->classe->nom_classe ?? 'Toutes classes' }}</span>
                                        <span>•</span>
                                        <span><i class="fa-solid fa-calendar text-slate-500 mr-1"></i>{{ $f->annee_scolaire }}</span>
                                    </div>
                                </div>

                                <form action="{{ route('comptable.frais.destroy', $f->id) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce frais ?')" class="shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-500/10 hover:bg-rose-500 hover:text-white text-rose-400 rounded-lg text-xs font-semibold transition" title="Supprimer">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-500 text-xs">
                                <i class="fa-solid fa-coins text-3xl mb-2 block"></i>
                                Aucun frais configuré.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </main>
    </div>

</body>
</html>
