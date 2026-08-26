<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Finances — Espace Élève</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-cyan-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- En-tête -->
            <div class="bg-gradient-to-r from-cyan-950 via-slate-950 to-slate-900 border border-cyan-500/20 p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-wallet"></i> Situation Financière
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Mes Frais & Paiements</h1>
                    <p class="text-slate-400 mt-1 text-sm">Consultez l'état de vos frais de scolarité, vos reçus et votre solde restant.</p>
                </div>
            </div>

            <!-- Résumé financier -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-bold">Total des Frais</p>
                    <p class="text-2xl font-black text-white mt-1.5">{{ number_format($totalDu, 2) }} $</p>
                </div>
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-bold">Total Déjà Payé</p>
                    <p class="text-2xl font-black text-emerald-400 mt-1.5">{{ number_format($totalPaye, 2) }} $</p>
                </div>
                <div class="bg-slate-950/80 border border-slate-800/90 p-5 rounded-2xl shadow-lg">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-bold">Solde Restant</p>
                    <p class="text-2xl font-black mt-1.5 {{ $solde <= 0 ? 'text-emerald-400' : 'text-amber-400' }}">
                        {{ number_format($solde, 2) }} $
                    </p>
                </div>
            </div>

            <!-- Détail par frais -->
            @forelse($detailsFrais as $detail)
                <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                    <div class="p-4 sm:p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h2 class="font-bold text-base text-white flex items-center gap-2">
                            <i class="fa-solid fa-coins text-emerald-400"></i> {{ $detail->frais->intitule_frais }}
                            <span class="text-xs text-slate-400 font-normal">
                                ({{ $detail->inscription->classe->nom_classe }} — {{ $detail->inscription->annee_scolaire }})
                            </span>
                        </h2>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="text-slate-400">Montant : <strong class="text-white font-mono">{{ number_format($detail->montant_du, 2) }} $</strong></span>
                            <span class="text-slate-600">|</span>
                            <span class="text-emerald-400">Payé : <strong class="font-mono">{{ number_format($detail->montant_paye, 2) }} $</strong></span>
                            <span class="text-slate-600">|</span>
                            <span class="{{ $detail->solde <= 0 ? 'text-emerald-400' : 'text-amber-400' }}">
                                Solde : <strong class="font-mono">{{ number_format($detail->solde, 2) }} $</strong>
                            </span>
                        </div>
                    </div>

                    @if($detail->paiements->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                                    <tr>
                                        <th class="p-3.5 px-4">N° Reçu</th>
                                        <th class="p-3.5 px-4 text-right">Montant Versé</th>
                                        <th class="p-3.5 px-4 text-center">Canal</th>
                                        <th class="p-3.5 px-4 text-center">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                    @foreach($detail->paiements as $paiement)
                                        <tr class="hover:bg-slate-900/30 transition">
                                            <td class="p-3.5 px-4 font-mono text-xs text-cyan-400 font-bold">{{ $paiement->numero_recu }}</td>
                                            <td class="p-3.5 px-4 text-right font-mono font-bold text-emerald-400">+{{ number_format($paiement->montant_paye, 2) }} $</td>
                                            <td class="p-3.5 px-4 text-center text-xs text-slate-400">{{ str_replace('_', ' ', $paiement->mode_paiement) }}</td>
                                            <td class="p-3.5 px-4 text-center text-xs text-slate-400">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-6 text-center text-slate-500 text-xs">
                            Aucun versement enregistré pour ce frais.
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl p-12 text-center shadow-xl">
                    <i class="fa-solid fa-coins text-slate-600 text-4xl mb-3 block"></i>
                    <h2 class="text-base font-bold text-white mb-1">Aucun frais configuré</h2>
                    <p class="text-slate-400 text-xs">Aucun frais n'a été configuré pour votre classe pour l'instant.</p>
                </div>
            @endforelse

        </main>
    </div>

</body>
</html>
