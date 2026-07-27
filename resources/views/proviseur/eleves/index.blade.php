<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Élèves — Proviseur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6" x-data="{ modalAjout: false, modalEdit: false, eleveEdit: {} }">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-xl font-black text-white">Annuaire des Élèves</h1>
                <p class="text-xs text-slate-400">Enregistrez et gérez les fiches individuelles des élèves</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('proviseur.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-xl transition flex items-center">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                </a>
                <button @click="modalAjout = true" class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-xl transition cursor-pointer">
                    <i class="fa-solid fa-plus mr-1"></i> Nouvel Élève
                </button>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Barre de Recherche -->
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl">
            <form action="{{ route('proviseur.eleves.index') }}" method="GET" class="flex gap-4">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-500 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par nom, postnom ou prénom..." class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3 py-2 text-sm text-white outline-none focus:border-indigo-500">
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-xs px-4 py-2 rounded-xl font-bold transition">Rechercher</button>
                @if($search)
                    <a href="{{ route('proviseur.eleves.index') }}" class="bg-slate-800/50 hover:bg-slate-800 text-slate-400 text-xs px-3 py-2 rounded-xl transition flex items-center">Réinitialiser</a>
                @endif
            </form>
        </div>

        <!-- Liste des Élèves -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                        <tr>
                            <th class="p-3">#</th>
                            <th class="p-3">Nom complet</th>
                            <th class="p-3">Genre</th>
                            <th class="p-3">Date de naissance</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($eleves as $eleve)
                            <tr class="hover:bg-slate-800/50">
                                <td class="p-3 font-mono text-slate-500">{{ $eleve->id }}</td>
                                <td class="p-3 font-semibold text-white">{{ $eleve->nom_complet }}</td>
                                <td class="p-3"><span class="px-2 py-0.5 rounded text-xs font-bold {{ $eleve->genre == 'M' ? 'bg-blue-500/10 text-blue-400' : 'bg-pink-500/10 text-pink-400' }}">{{ $eleve->genre == 'M' ? 'Masculin' : 'Féminin' }}</span></td>
                                <td class="p-3 text-xs text-slate-400">{{ $eleve->date_naissance ?? 'N/A' }}</td>
                                <td class="p-3 text-right space-x-2">
                                    <button @click="eleveEdit = {{ json_encode($eleve) }}; modalEdit = true" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-2.5 py-1.5 rounded-lg transition"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('proviseur.eleves.destroy', $eleve->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement cet élève ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs bg-slate-800 hover:bg-slate-700 text-rose-400 px-2.5 py-1.5 rounded-lg transition"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-500">Aucun élève trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $eleves->links() }}</div>
        </div>

    </div>

    <!-- MODAL D'AJOUT -->
    <div x-show="modalAjout" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Nouveau dossier élève</h2>
            <form action="{{ route('proviseur.eleves.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="nom" placeholder="Nom" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <input type="text" name="postnom" placeholder="Postnom" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <input type="text" name="prenom" placeholder="Prénom" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <select name="genre" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </select>
                <input type="date" name="date_naissance" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-300">
                <input type="text" name="lieu_naissance" placeholder="Date de naissance" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalAjout = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-indigo-600 text-white rounded-xl font-bold">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DE MODIFICATION -->
    <div x-show="modalEdit" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Modifier l'élève</h2>
            <form :action="'{{ route('proviseur.eleves.update', ':id') }}'.replace(':id', eleveEdit.id)" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="text" name="nom" x-model="eleveEdit.nom" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <input type="text" name="postnom" x-model="eleveEdit.postnom" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <input type="text" name="prenom" x-model="eleveEdit.prenom" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <select name="genre" x-model="eleveEdit.genre" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </select>
                <input type="date" name="date_naissance" x-model="eleveEdit.date_naissance" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-slate-300">
                <input type="text" name="lieu_naissance" x-model="eleveEdit.lieu_naissance" placeholder="Lieu de naissance" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-amber-600 text-white rounded-xl font-bold">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>