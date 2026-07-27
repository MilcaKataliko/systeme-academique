<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Périodes — Proviseur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6" x-data="{ modalAjout: false, modalEdit: false, periodeEdit: {} }">

    <div class="max-w-5xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-xl font-black text-white">Périodes Scolaires</h1>
                <p class="text-xs text-slate-400">Définissez les périodes d'évaluation (1ère période, Examen, etc.)</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('proviseur.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-xl transition flex items-center">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                </a>
                <button @click="modalAjout = true" class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-xl transition cursor-pointer">
                    <i class="fa-solid fa-plus mr-1"></i> Nouvelle Période
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
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Barre de Recherche -->
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl">
            <form method="GET" action="{{ route('proviseur.periodes.index') }}" class="flex gap-4">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-500 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher une période..." class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3 py-2 text-sm text-white outline-none focus:border-indigo-500">
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-xs px-4 py-2 rounded-xl font-bold transition">Rechercher</button>
                @if($search)
                    <a href="{{ route('proviseur.periodes.index') }}" class="bg-slate-800/50 hover:bg-slate-800 text-slate-400 text-xs px-3 py-2 rounded-xl transition flex items-center">Réinitialiser</a>
                @endif
            </form>
        </div>

        <!-- Liste -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                        <tr>
                            <th class="p-3">#</th>
                            <th class="p-3">Nom de la période</th>
                            <th class="p-3">Statut</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($periodes as $periode)
                            <tr class="hover:bg-slate-800/50">
                                <td class="p-3 font-mono text-slate-500">{{ $periode->id }}</td>
                                <td class="p-3 font-semibold text-white">{{ $periode->nom_periode }}</td>
                                <td class="p-3">
                                    @if($periode->est_cloturee)
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-rose-500/10 text-rose-400">Clôturée</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-500/10 text-emerald-400">Active</span>
                                    @endif
                                </td>
                                <td class="p-3 text-right space-x-2">
                                    <button @click="periodeEdit = {{ json_encode($periode) }}; modalEdit = true" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-2.5 py-1.5 rounded-lg transition"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('proviseur.periodes.destroy', $periode->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement cette période ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs bg-slate-800 hover:bg-slate-700 text-rose-400 px-2.5 py-1.5 rounded-lg transition"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-center text-slate-500">Aucune période trouvée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $periodes->links() }}</div>
        </div>

    </div>

    <!-- MODAL AJOUT -->
    <div x-show="modalAjout" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Nouvelle période</h2>
            <form action="{{ route('proviseur.periodes.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="nom_periode" placeholder="Ex: 1ère Période, Examen 1er Semestre" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" name="est_cloturee" value="1" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-indigo-600">
                    Clôturer cette période
                </label>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalAjout = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-indigo-600 text-white rounded-xl font-bold">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div x-show="modalEdit" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Modifier la période</h2>
            <form :action="'{{ route('proviseur.periodes.update', ':id') }}'.replace(':id', periodeEdit.id)" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="text" name="nom_periode" x-model="periodeEdit.nom_periode" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <label class="flex items-center gap-2 text-sm text-slate-300">
                    <input type="checkbox" name="est_cloturee" value="1" x-model="periodeEdit.est_cloturee" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-amber-600">
                    Clôturer cette période
                </label>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-amber-600 text-white rounded-xl font-bold">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

