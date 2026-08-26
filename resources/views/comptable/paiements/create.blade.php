<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau paiement — Comptable</title>
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

    <main class="max-w-2xl mx-auto p-6 md:p-8">

        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-8 shadow-xl">
            <h1 class="text-2xl font-black tracking-tight text-white mb-6">
                <i class="fa-solid fa-plus-circle text-emerald-400 mr-3"></i>Enregistrer un paiement
            </h1>

            @if($errors->any())
                <div class="mb-5 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Sélection de l'élève -->
            <form method="GET" action="{{ route('comptable.paiements.create') }}" class="mb-6 p-4 bg-slate-900/50 rounded-xl border border-slate-700/50">
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2"><i class="fa-solid fa-user-graduate mr-2"></i>Sélectionner un élève</label>
                <div class="flex space-x-2">
                    <select name="eleve_id" onchange="this.form.submit()"
                            class="flex-1 bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                        <option value="">Choisir un élève...</option>
                        @foreach($eleves as $eleve)
                            <option value="{{ $eleve->id }}" {{ $selectedEleve && $selectedEleve->id == $eleve->id ? 'selected' : '' }}>
                                {{ $eleve->nom }} {{ $eleve->postnom }} ({{ $eleve->code_matricule }})
                            </option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="bg-emerald-600 text-white px-3 py-2 rounded-xl text-sm">OK</button></noscript>
                </div>
            </form>

            @if($selectedEleve)
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl mb-6">
                    <p class="text-sm text-emerald-400 font-bold">
                        <i class="fa-solid fa-user-graduate mr-2"></i>{{ $selectedEleve->nom }} {{ $selectedEleve->postnom }}
                        <span class="text-xs text-slate-400 ml-2 font-normal">{{ $selectedEleve->code_matricule }}</span>
                    </p>
                    @if($inscriptions->isNotEmpty())
                        <p class="text-xs text-slate-400 mt-1">
                            @foreach($inscriptions as $ins)
                                <span class="mr-3"><i class="fa-solid fa-school mr-1"></i>{{ $ins->classe->nom_classe }} ({{ $ins->annee_scolaire }})</span>
                            @endforeach
                        </p>
                    @endif
                </div>

                <form action="{{ route('comptable.paiements.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Inscription (niveau, classe, année)</label>
                        <select name="inscription_id" required
                                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                            <option value="">Sélectionnez l'inscription...</option>
                            @foreach($inscriptions as $ins)
                                <option value="{{ $ins->id }}">
                                    {{ $ins->classe->niveau }} {{ $ins->classe->nom_classe }}@if($ins->classe->option) ({{ $ins->classe->option->nomoption }})@endif — {{ $ins->annee_scolaire }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Frais concerné</label>
                        <select name="frais_id" required
                                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                            <option value="">Sélectionnez...</option>
                            @foreach($frais as $f)
                                <option value="{{ $f->id }}">
                                    {{ $f->intitule_frais }} — {{ number_format($f->montant, 2) }} {{ $f->devise }}
                                    @if($f->classe)({{ $f->classe->nom_classe }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Montant payé</label>
                            <input type="number" name="montant_paye" value="{{ old('montant_paye') }}" step="0.01" min="0" required
                                   class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Date de paiement</label>
                            <input type="date" name="date_paiement" value="{{ old('date_paiement', date('Y-m-d')) }}" required
                                   class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Mode de paiement</label>
                        <select name="mode_paiement" required
                                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition">
                            <option value="especes">Espèces</option>
                            <option value="cheque">Chèque</option>
                            <option value="virement_bancaire">Virement bancaire</option>
                            <option value="depot_mobile">Dépôt mobile (M-Pesa, Airtel Money...)</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                        <i class="fa-solid fa-check mr-2"></i> Enregistrer le paiement
                    </button>
                </form>
            @else
                <div class="p-8 text-center text-slate-500">
                    <i class="fa-solid fa-hand-point-up text-3xl mb-3 block"></i>
                    <p>Sélectionnez un élève pour commencer.</p>
                </div>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('comptable.paiements.index') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Voir l'historique
                </a>
            </div>
        </div>

    </main>

</body>
</html>

