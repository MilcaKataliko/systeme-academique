<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relevé de {{ $eleve->nom }} {{ $eleve->postnom }}</title>
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

    <main class="max-w-5xl mx-auto p-6 md:p-8 space-y-8">

        <div class="bg-gradient-to-r from-emerald-900 to-slate-950 border border-emerald-500/20 p-8 rounded-2xl shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-white">{{ $eleve->nom }} {{ $eleve->postnom }}</h1>
                    <p class="text-slate-400 text-sm mt-1">
                        <i class="fa-solid fa-id-card mr-2"></i>{{ $eleve->code_matricule }}
                        <span class="mx-2">•</span>
                        <i class="fa-solid fa-venus-mars mr-1"></i>{{ $eleve->genre == 'M' ? 'Masculin' : 'Féminin' }}
                    </p>
                </div>
                <a href="{{ route('comptable.paiements.index') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Retour
                </a>
            </div>
        </div>

        <!-- Résumé financier -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl shadow-lg">
                <p class="text-xs text-slate-400 uppercase tracking-wider">Total dû</p>
                <p class="text-2xl font-black text-white mt-1">{{ number_format($totalDu, 2) }} $</p>
            </div>
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl shadow-lg">
                <p class="text-xs text-slate-400 uppercase tracking-wider">Total payé</p>
                <p class="text-2xl font-black text-emerald-400 mt-1">{{ number_format($totalPaye, 2) }} $</p>
            </div>
            <div class="bg-slate-950 border border-slate-800 p-6 rounded-2xl shadow-lg">
                <p class="text-xs text-slate-400 uppercase tracking-wider">Solde</p>
                <p class="text-2xl font-black mt-1 {{ $solde <= 0 ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ number_format($solde, 2) }} $
                </p>
            </div>
        </div>

        <!-- Détail par inscription -->
        @foreach($eleve->inscriptions as $inscription)
            <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="font-bold text-lg text-white">
                        <i class="fa-solid fa-school text-emerald-400 mr-3"></i>{{ $inscription->classe->nom_classe }}
                        <span class="text-sm text-slate-400 font-normal ml-2">({{ $inscription->annee_scolaire }})</span>
                    </h2>
                    <span class="text-xs bg-{{ $inscription->statut == 'actif' ? 'emerald' : 'slate' }}-500/10 text-{{ $inscription->statut == 'actif' ? 'emerald' : 'slate' }}-400 px-2 py-1 rounded-full">
                        {{ $inscription->statut }}
                    </span>
                </div>

                @php
                    $paiementsInscription = $inscription->paiements;
                @endphp

                @if($paiementsInscription->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-800 bg-slate-900/50">
                                    <th class="text-left py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs">Reçu</th>
                                    <th class="text-left py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs">Frais</th>
                                    <th class="text-right py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs">Montant</th>
                                    <th class="text-center py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs">Date</th>
                                    <th class="text-center py-3.5 px-4 font-semibold text-slate-400 uppercase text-xs">Mode</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paiementsInscription as $p)
                                    <tr class="border-b border-slate-800/50 hover:bg-slate-900/30 transition">
                                        <td class="py-3.5 px-4 font-mono text-xs text-slate-300">{{ $p->numero_recu }}</td>
                                        <td class="py-3.5 px-4 text-slate-300">{{ $p->frais->intitule_frais }}</td>
                                        <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-400">{{ number_format($p->montant_paye, 2) }} $</td>
                                        <td class="py-3.5 px-4 text-center text-slate-400">{{ $p->date_paiement->format('d/m/Y') }}</td>
                                        <td class="py-3.5 px-4 text-center text-slate-400">{{ $p->mode_paiement }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center text-slate-500">
                        <p>Aucun paiement pour cette inscription.</p>
                    </div>
                @endif
            </div>
        @endforeach

    </main>

</body>
</html>

