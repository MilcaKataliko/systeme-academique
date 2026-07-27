<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6">

    <div class="max-w-2xl mx-auto">
        <div class="bg-white text-slate-900 rounded-2xl p-8 shadow-2xl">
            <!-- En-tête -->
            <div class="text-center border-b border-slate-200 pb-4 mb-6">
                <h1 class="text-xl font-black">REÇU DE PAIEMENT</h1>
                <p class="text-xs text-slate-500">Système Académique — Établissement Secondaire</p>
            </div>

            <div class="space-y-4 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="font-bold text-slate-600">N° Reçu :</span>
                        <span class="ml-2 font-mono">{{ $paiement->numero_recu }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-slate-600">Date :</span>
                        <span class="ml-2">{{ $paiement->date_paiement }}</span>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-4">
                    <h3 class="font-bold text-slate-700 mb-2">Élève</h3>
                    <p>{{ $paiement->inscription->eleve->nom_complet ?? 'N/A' }}</p>
                    <p class="text-xs text-slate-500">Classe : {{ $paiement->inscription->classe->nom_classe ?? 'N/A' }}</p>
                </div>

                <div class="border-t border-slate-200 pt-4">
                    <h3 class="font-bold text-slate-700 mb-2">Détails du paiement</h3>
                    <div class="flex justify-between">
                        <span>Frais : {{ $paiement->frais->intitule_frais ?? 'N/A' }}</span>
                        <span class="font-bold text-emerald-600">{{ number_format($paiement->montant_paye, 2) }} {{ $paiement->frais->devise ?? '' }}</span>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span>Mode de paiement :</span>
                        <span class="font-bold">{{ ucfirst($paiement->mode_paiement) }}</span>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-4 flex justify-between text-xs text-slate-500">
                    <span>Enregistré par : {{ $paiement->comptable->name ?? 'N/A' }}</span>
                    <span>{{ $paiement->created_at ?? '' }}</span>
                </div>
            </div>

            <div class="mt-8 flex justify-center">
                <button onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 rounded-xl font-bold text-sm transition cursor-pointer">
                    <i class="fa-solid fa-print mr-2"></i> Imprimer le reçu
                </button>
            </div>
        </div>
    </div>

</body>
</html>

