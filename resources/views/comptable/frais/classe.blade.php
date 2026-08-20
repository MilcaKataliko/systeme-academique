<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frais par classe — Comptable</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    <main class="max-w-7xl mx-auto p-6 md:p-8 space-y-8">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-white">Frais par classe</h1>
                <p class="text-slate-400 text-sm mt-1">Associer des frais spécifiques à chaque classe.</p>
            </div>
            <a href="{{ route('comptable.dashboard') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                <i class="fa-solid fa-arrow-left mr-2"></i> Tableau de bord
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Formulaire -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <h2 class="font-bold text-lg text-white mb-4">
                    <i class="fa-solid fa-plus-circle text-emerald-400 mr-3"></i>Associer un frais à une classe
                </h2>

                <form action="{{ route('comptable.frais.classe.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Classe</label>
                        <select name="classe_id" required
                                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                            <option value="">Sélectionnez...</option>
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}">{{ $classe->niveau }} {{ $classe->nom_classe }}@if($classe->option) ({{ $classe->option->nomoption }})@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Type de frais</label>
                        <select name="frais_id" required
                                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                            <option value="">Sélectionnez...</option>
                            @foreach($frais as $f)
                                <option value="{{ $f->id }}">{{ $f->intitule_frais }} ({{ number_format($f->montant_standard, 2) }} {{ $f->devise }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Montant spécifique</label>
                        <input type="number" name="montant_specifique" value="{{ old('montant_specifique') }}" step="0.01" min="0" required
                               placeholder="Montant pour cette classe"
                               class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Année scolaire</label>
                        <input type="text" name="annee_scolaire" value="{{ old('annee_scolaire', date('Y') . '-' . (date('Y') + 1)) }}" required
                               class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                        <i class="fa-solid fa-link mr-2"></i> Associer
                    </button>
                </form>
            </div>

            <!-- Liste des associations -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                <div class="px-6 py-4 border-b border-slate-800">
                    <h2 class="font-bold text-lg text-white">
                        <i class="fa-solid fa-list text-emerald-400 mr-3"></i>Associations en cours
                    </h2>
                </div>
                <div class="divide-y divide-slate-800/50 max-h-[500px] overflow-y-auto">
                    @forelse($fraisClasses as $fc)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-900/40 transition">
                            <div>
                                <p class="text-sm font-bold text-white">{{ $fc->classe->nom_classe }}</p>
                                <p class="text-xs text-slate-400">
                                    <span class="text-emerald-400">{{ $fc->frais->intitule_frais }}</span>
                                    — {{ number_format($fc->montant_specifique, 2) }} {{ $fc->frais->devise }}
                                    <span class="text-slate-600 mx-1">•</span>
                                    {{ $fc->annee_scolaire }}
                                </p>
                            </div>
                            <form action="{{ route('comptable.frais.classe.destroy', $fc->id) }}" method="POST"
                                  onsubmit="return confirm('Supprimer cette association ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-xs bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700 cursor-pointer">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Retirer
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            <i class="fa-solid fa-school text-3xl mb-3 block"></i>
                            <p>Aucune association.</p>
                            <p class="text-xs text-slate-600 mt-1">Utilisez le formulaire pour associer des frais.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </main>

</body>
</html>

