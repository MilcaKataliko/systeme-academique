<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Paiements — Comptable</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6" x-data="{ modalAjout: false, modalEdit: false, paiementEdit: {} }">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-xl font-black text-white">Paiements & Transactions</h1>
                <p class="text-xs text-slate-400">Enregistrez les paiements des frais scolaires</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('comptable.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-xl transition flex items-center">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                </a>
                <button @click="modalAjout = true" class="text-xs bg-amber-600 hover:bg-amber-500 text-white font-bold px-4 py-2 rounded-xl transition cursor-pointer">
                    <i class="fa-solid fa-plus mr-1"></i> Nouveau Paiement
                </button>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-sm mb-4">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <!-- Recherche -->
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl">
            <form method="GET" action="{{ route('comptable.paiements.index') }}" class="flex gap-4">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-500 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par N° reçu ou nom d'élève..." class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3 py-2 text-sm text-white">
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-xs px-4 py-2 rounded-xl font-bold">Rechercher</button>
                @if($search)<a href="{{ route('comptable.paiements.index') }}" class="bg-slate-800/50 hover:bg-slate-800 text-slate-400 text-xs px-3 py-2 rounded-xl flex items-center">Réinitialiser</a>@endif
            </form>
        </div>

        <!-- Liste -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                        <tr>
                            <th class="p-3">N° Reçu</th>
                            <th class="p-3">Élève</th>
                            <th class="p-3">Frais</th>
                            <th class="p-3">Montant</th>
                            <th class="p-3">Date</th>
                            <th class="p-3">Mode</th>
                            <th class="p-3">Comptable</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($paiements as $paiement)
                            <tr class="hover:bg-slate-800/50">
                                <td class="p-3 font-mono text-amber-400 font-bold text-xs">{{ $paiement->numero_recu }}</td>
                                <td class="p-3 font-semibold text-white">{{ $paiement->inscription->eleve->nom_complet ?? 'N/A' }}</td>
                                <td class="p-3 text-slate-400">{{ $paiement->frais->intitule_frais ?? 'N/A' }}</td>
                                <td class="p-3 font-bold text-emerald-400">{{ number_format($paiement->montant_paye, 2) }} {{ $paiement->frais->devise ?? '' }}</td>
                                <td class="p-3 text-xs text-slate-400">{{ $paiement->date_paiement }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold 
                                        {{ $paiement->mode_paiement == 'especes' ? 'bg-emerald-500/10 text-emerald-400' : '' }}
                                        {{ $paiement->mode_paiement == 'cheque' ? 'bg-blue-500/10 text-blue-400' : '' }}
                                        {{ $paiement->mode_paiement == 'virement' ? 'bg-purple-500/10 text-purple-400' : '' }}
                                        {{ $paiement->mode_paiement == 'carte' ? 'bg-cyan-500/10 text-cyan-400' : '' }}">
                                        {{ ucfirst($paiement->mode_paiement) }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-slate-400">{{ $paiement->comptable->name ?? 'N/A' }}</td>
                                <td class="p-3 text-right space-x-2">
                                    <button @click="paiementEdit = {{ json_encode($paiement) }}; modalEdit = true" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-2.5 py-1.5 rounded-lg"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('comptable.paiements.destroy', $paiement->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement ce paiement ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs bg-slate-800 hover:bg-slate-700 text-rose-400 px-2.5 py-1.5 rounded-lg"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="p-4 text-center text-slate-500">Aucun paiement trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $paiements->links() }}</div>
        </div>
    </div>

    <!-- MODAL AJOUT -->
    <div x-show="modalAjout" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Nouveau paiement</h2>
            <form action="{{ route('comptable.paiements.store') }}" method="POST" class="space-y-3">
                @csrf
                <select name="inscription_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="">Sélectionnez un élève inscrit</option>
                    @foreach($inscriptions as $inscription)
                        <option value="{{ $inscription->id }}">{{ $inscription->eleve->nom_complet }} — {{ $inscription->classe->nom_classe }} ({{ $inscription->annee_scolaire }})</option>
                    @endforeach
                </select>
                <select name="frais_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="">Type de frais</option>
                    @foreach($frais as $f)
                        <option value="{{ $f->id }}">{{ $f->intitule_frais }} ({{ number_format($f->montant_standard, 2) }} {{ $f->devise }})</option>
                    @endforeach
                </select>
                <input type="number" name="montant_paye" placeholder="Montant payé" required step="0.01" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <input type="date" name="date_paiement" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-300">
                <select name="mode_paiement" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="especes">Espèces</option>
                    <option value="cheque">Chèque</option>
                    <option value="virement">Virement</option>
                    <option value="carte">Carte bancaire</option>
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalAjout = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-amber-600 text-white rounded-xl font-bold">Enregistrer le paiement</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div x-show="modalEdit" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Modifier le paiement</h2>
            <form :action="'{{ route('comptable.paiements.update', ':id') }}'.replace(':id', paiementEdit.id)" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <input type="number" name="montant_paye" x-model="paiementEdit.montant_paye" required step="0.01" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <input type="date" name="date_paiement" x-model="paiementEdit.date_paiement" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-300">
                <select name="mode_paiement" x-model="paiementEdit.mode_paiement" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="especes">Espèces</option>
                    <option value="cheque">Chèque</option>
                    <option value="virement">Virement</option>
                    <option value="carte">Carte bancaire</option>
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-amber-600 text-white rounded-xl font-bold">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

