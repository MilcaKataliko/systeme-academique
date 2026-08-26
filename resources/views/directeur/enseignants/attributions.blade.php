<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attribution des cours —Directeur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-purple-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

        <!-- En-tête -->
        <div class="bg-gradient-to-r from-purple-900 to-slate-950 border border-purple-500/20 p-8 rounded-2xl shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white">Attribution des cours</h1>
                    <p class="text-slate-400 mt-2 text-sm">Assignez une matière et une classe à un enseignant.</p>
                </div>
                <a href="{{ route('directeur.enseignants') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Retour
                </a>
            </div>
        </div>

        <!-- Messages flash -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Contenu principal : Formulaire + Liste -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Formulaire d'attribution -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                <div class="px-6 py-4 border-b border-slate-800">
                    <h2 class="font-bold text-lg text-white flex items-center">
                        <i class="fa-solid fa-plus-circle text-purple-400 mr-3"></i>Nouvelle attribution
                    </h2>
                </div>

                <!-- Guide d'aide -->
                <div class="mx-6 mt-4 p-3 bg-blue-900/30 border border-blue-500/20 rounded-xl text-xs text-slate-300 space-y-1.5">
                    <p class="text-blue-400 font-semibold uppercase tracking-wider text-[10px]"><i class="fa-solid fa-lightbulb mr-1"></i>Comment attribuer un cours ?</p>
                    <p><span class="text-white font-medium">Enseignant</span> <i class="fa-solid fa-arrow-right mx-1"></i> Sélectionnez le professeur concerné</p>
                    <p><span class="text-white font-medium">Classe</span> <i class="fa-solid fa-arrow-right mx-1"></i> La classe qui recevra le cours</p>
                    <p><span class="text-white font-medium">Matière</span> <i class="fa-solid fa-arrow-right mx-1"></i> Le cours à attribuer (ex: Mathématiques, Français...)</p>
                    <p><span class="text-white font-medium">Notes max</span> <i class="fa-solid fa-arrow-right mx-1"></i> La note maximale pour les périodes et l'examen (généralement 20)</p>
                </div>

                <form action="{{ route('directeur.enseignants.attributions.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-chalkboard-user mr-2"></i>Enseignant <span class="text-red-400">*</span>
                        </label>
                        <select name="enseignant_id" required 
                                class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/40 w-full transition">
                            <option value="">Sélectionnez un enseignant...</option>
                            @foreach($enseignants as $ens)
                                <option value="{{ $ens->id }}" {{ $selectedEnseignant && $selectedEnseignant->id == $ens->id ? 'selected' : '' }}>
                                    {{ $ens->nom }} {{ $ens->postnom }} ({{ $ens->grade }})
                                </option>
                            @endforeach
                        </select>
                        @error('enseignant_id')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-school mr-2"></i>Classe <span class="text-red-400">*</span>
                        </label>
                        <select name="classe_id" required 
                                class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/40 w-full transition">
                            <option value="">Sélectionnez une classe...</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->nom_classe }} (Niv. {{ $classe->niveau }})</option>
                            @endforeach
                        </select>
                        @error('classe_id')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-book mr-2"></i>Cours / Matière <span class="text-red-400">*</span>
                        </label>
                        <select name="cours_id" required 
                                class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/40 w-full transition">
                            <option value="">Sélectionnez un cours...</option>
                            @foreach($cours as $cour)
                                <option value="{{ $cour->id }}">{{ $cour->nom_cours }} ({{ $cour->code_cours ?? 'Sans code' }})</option>
                            @endforeach
                        </select>
                        @error('cours_id')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                                <i class="fa-solid fa-chart-column mr-2"></i>Max note période <span class="text-red-400">*</span>
                            </label>
                            <input type="number" name="maxima_periode" value="{{ old('maxima_periode', 20) }}" min="1" max="100" required
                                   class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/40 w-full transition">
                            @error('maxima_periode')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                                <i class="fa-solid fa-chart-column mr-2"></i>Max note examen <span class="text-red-400">*</span>
                            </label>
                            <input type="number" name="maxima_examen" value="{{ old('maxima_examen', 20) }}" min="1" max="100" required
                                   class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/40 w-full transition">
                            @error('maxima_examen')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                            <i class="fa-solid fa-calendar-days mr-2"></i>Année scolaire <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', date('Y') . '-' . (date('Y') + 1)) }}" required
                               placeholder="Ex: 2024-2025"
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500/40 w-full transition">
                        @error('annee_scolaire')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="w-full bg-purple-600 hover:bg-purple-500 text-white py-2.5 rounded-xl text-sm font-bold transition-all duration-150 cursor-pointer">
                        <i class="fa-solid fa-check mr-2"></i> Attribuer le cours
                    </button>
                </form>
            </div>

            <!-- Affectations actuelles -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="font-bold text-lg text-white flex items-center">
                        <i class="fa-solid fa-list-check text-purple-400 mr-3"></i>Affectations en cours
                    </h2>
                    @if($selectedEnseignant)
                        <span class="text-xs text-slate-400">{{ $affectations->count() }} cours</span>
                    @endif
                </div>

                @if($selectedEnseignant)
                    <div class="px-6 py-3 bg-slate-900/50 border-b border-slate-800/50">
                        <p class="text-sm text-slate-300">
                            <i class="fa-solid fa-user text-purple-400 mr-2"></i>
                            {{ $selectedEnseignant->nom }} {{ $selectedEnseignant->postnom }} —
                            <span class="text-purple-400">{{ $selectedEnseignant->grade }}</span>
                        </p>
                    </div>
                @endif

                <div class="divide-y divide-slate-800/50 max-h-[450px] overflow-y-auto">
                    @forelse($affectations as $plan)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-900/40 transition">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-white flex items-center space-x-2">
                                    <span>{{ $plan->cours->nom_cours }}</span>
                                    @if($plan->cours->code_cours)
                                        <span class="text-[10px] bg-purple-500/10 text-purple-400 px-1.5 py-0.5 rounded border border-purple-500/20">
                                            {{ $plan->cours->code_cours }}
                                        </span>
                                    @endif
                                </p>
                                <p class="text-xs text-slate-400 mt-1 flex items-center space-x-2">
                                    <span><i class="fa-solid fa-school text-slate-500 mr-1"></i>{{ $plan->classe->nom_classe ?? 'N/A' }}</span>
                                    <i class="fa-solid fa-circle text-[5px] align-middle"></i>
                                    <span><i class="fa-solid fa-calendar text-slate-500 mr-1"></i>{{ $plan->annee_scolaire }}</span>
                                    <i class="fa-solid fa-circle text-[5px] align-middle"></i>
                                    <span><i class="fa-solid fa-star text-slate-500 mr-1"></i>{{ $plan->maxima_periode }}/{{ $plan->maxima_examen }}</span>
                                </p>
                            </div>
                            <form action="{{ route('directeur.enseignants.attributions.destroy', $plan->id) }}" method="POST" 
                                  onsubmit="return confirm('Supprimer cette attribution ?')" class="ml-4 shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-slate-800 hover:bg-red-600/20 text-slate-400 hover:text-red-400 border border-slate-700 px-3 py-1.5 rounded-lg text-xs transition cursor-pointer inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-trash-can"></i>
                                    <span>Retirer</span>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            <i class="fa-solid fa-book-open text-3xl mb-3 block"></i>
                            @if($selectedEnseignant)
                                <p class="text-sm">Cet enseignant n'a pas encore d'affectation.</p>
                                <p class="text-xs text-slate-600 mt-1">Utilisez le formulaire à gauche pour lui attribuer un cours.</p>
                            @else
                                <p class="text-sm">Sélectionnez un enseignant pour voir ses affectations.</p>
                                <p class="text-xs text-slate-600 mt-1">Utilisez la section ci-dessous pour choisir un enseignant.</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Sélection rapide d'un enseignant -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h3 class="font-bold text-white text-sm mb-3 flex items-center">
                <i class="fa-solid fa-arrow-pointer text-purple-400 mr-2"></i> Voir les affectations d'un enseignant
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($enseignants as $ens)
                    <a href="{{ route('directeur.enseignants.attributions', $ens->id) }}" 
                       class="bg-slate-900/60 border border-slate-700 hover:border-purple-500 hover:bg-purple-500/10 px-3 py-1.5 rounded-lg text-xs text-slate-300 hover:text-white transition-all duration-150 {{ $selectedEnseignant && $selectedEnseignant->id == $ens->id ? 'border-purple-500 bg-purple-500/10 text-purple-300' : '' }}">
                        <i class="fa-solid fa-user mr-1"></i>{{ $ens->nom }} {{ $ens->postnom }}
                    </a>
                @endforeach
            </div>
        </div>

    </main>
</div>

</body>
</html>
