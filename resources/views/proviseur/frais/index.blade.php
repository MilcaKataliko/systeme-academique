<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Frais — Proviseur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6" x-data="{ modalAjout: false, modalEdit: false, fraisEdit: {}, modalAssoc: false }">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-xl font-black text-white">Gestion des Frais Scolaires</h1>
                <p class="text-xs text-slate-400">Définissez les frais et associez-les aux classes</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('proviseur.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-xl transition flex items-center">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                </a>
                <button @click="modalAjout = true" class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-xl transition cursor-pointer">
                    <i class="fa-solid fa-plus mr-1"></i> Nouveau Frais
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
            <form method="GET" action="{{ route('proviseur.frais.index') }}" class="flex gap-4">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-500 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher un frais..." class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3 py-2 text-sm text-white">
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-xs px-4 py-2 rounded-xl font-bold">Rechercher</button>
                @if($search)<a href="{{ route('proviseur.frais.index') }}" class="bg-slate-800/50 hover:bg-slate-800 text-slate-400 text-xs px-3 py-2 rounded-xl flex items-center">Réinitialiser</a>@endif
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Liste Frais -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
                <h2 class="text-base font-bold text-white mb-4">Frais généraux</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                            <tr><th class="p-3">Intitulé</th><th class="p-3">Montant</th><th class="p-3">Devise</th><th class="p-3 text-right">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($frais as $f)
                                <tr class="hover:bg-slate-800/50">
                                    <td class="p-3 font-semibold text-white">{{ $f->intitule_frais }}</td>
                                    <td class="p-3 text-slate-400">{{ number_format($f->montant_standard, 2) }}</td>
                                    <td class="p-3 text-xs font-bold text-indigo-400">{{ $f->devise }}</td>
                                    <td class="p-3 text-right space-x-2">
                                        <button @click="fraisEdit = {{ json_encode($f) }}; modalEdit = true" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-2 py-1.5 rounded-lg"><i class="fa-solid fa-pen"></i></button>
                                        <form action="{{ route('proviseur.frais.destroy', $f->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs bg-slate-800 hover:bg-slate-700 text-rose-400 px-2 py-1.5 rounded-lg"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="p-4 text-center text-slate-500">Aucun frais défini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $frais->links() }}</div>

                <div class="mt-6 pt-4 border-t border-slate-800">
                    <button @click="modalAssoc = true" class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-xl transition cursor-pointer">
                        <i class="fa-solid fa-link mr-1"></i> Associer un frais à une classe
                    </button>
                </div>
            </div>

            <!-- Frais par Classe -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
                <h2 class="text-base font-bold text-white mb-4">Frais par classe</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                            <tr><th class="p-3">Frais</th><th class="p-3">Classe</th><th class="p-3">Montant</th><th class="p-3">Année</th><th class="p-3 text-right">Action</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($fraisClasses as $fc)
                                <tr class="hover:bg-slate-800/50">
                                    <td class="p-3 text-white font-semibold">{{ $fc->frais->intitule_frais ?? 'N/A' }}</td>
                                    <td class="p-3 text-slate-400">{{ $fc->classe->nom_classe ?? 'N/A' }}</td>
                                    <td class="p-3 text-slate-400">{{ number_format($fc->montant_specifique, 2) }}</td>
                                    <td class="p-3 text-xs text-slate-400">{{ $fc->annee_scolaire }}</td>
                                    <td class="p-3 text-right">
                                        <form action="{{ route('proviseur.frais.frais-classe.destroy', $fc->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette association ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs bg-slate-800 hover:bg-slate-700 text-rose-400 px-2 py-1.5 rounded-lg"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-4 text-center text-slate-500">Aucune association frais-classe.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL AJOUT FRAIS -->
    <div x-show="modalAjout" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Nouveau frais</h2>
            <form action="{{ route('proviseur.frais.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="intitule_frais" placeholder="Intitulé (ex: Minerval)" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <input type="number" name="montant_standard" placeholder="Montant standard" required step="0.01" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <select name="devise" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="USD">USD</option>
                    <option value="CDF">CDF</option>
                    <option value="EUR">EUR</option>
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalAjout = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-indigo-600 text-white rounded-xl font-bold">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT FRAIS -->
    <div x-show="modalEdit" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Modifier le frais</h2>
            <form :action="'{{ route('proviseur.frais.update', ':id') }}'.replace(':id', fraisEdit.id)" method="POST" class="space-y-3">
                @csrf @method('PUT')
                <input type="text" name="intitule_frais" x-model="fraisEdit.intitule_frais" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <input type="number" name="montant_standard" x-model="fraisEdit.montant_standard" required step="0.01" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <select name="devise" x-model="fraisEdit.devise" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="USD">USD</option><option value="CDF">CDF</option><option value="EUR">EUR</option>
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-amber-600 text-white rounded-xl font-bold">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL ASSOCIATION FRAIS-CLASSE -->
    <div x-show="modalAssoc" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Associer un frais à une classe</h2>
            <form action="{{ route('proviseur.frais.frais-classe.store') }}" method="POST" class="space-y-3">
                @csrf
                <select name="frais_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="">Sélectionnez un frais</option>
                    @foreach($frais as $f)
                        <option value="{{ $f->id }}">{{ $f->intitule_frais }} ({{ number_format($f->montant_standard, 2) }} {{ $f->devise }})</option>
                    @endforeach
                </select>
                <select name="classe_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="">Sélectionnez une classe</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}">{{ $classe->nom_classe }}</option>
                    @endforeach
                </select>
                <input type="number" name="montant_specifique" placeholder="Montant spécifique" required step="0.01" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <select name="annee_scolaire" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="">Année scolaire</option>
                    @foreach($annees as $annee)
                        <option value="{{ $annee->anneescolaire }}">{{ $annee->anneescolaire }}</option>
                    @endforeach
                </select>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalAssoc = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-indigo-600 text-white rounded-xl font-bold">Associer</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

