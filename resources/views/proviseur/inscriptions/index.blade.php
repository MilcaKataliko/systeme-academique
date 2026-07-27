<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Inscriptions — Proviseur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6" x-data="{ modalAjout: false, modalEdit: false, inscEdit: {} }">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-xl font-black text-white">Inscriptions</h1>
                <p class="text-xs text-slate-400">Affectez les élèves dans leurs classes pour chaque année scolaire</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('proviseur.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-xl transition flex items-center">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                </a>
                <button @click="modalAjout = true" class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-xl transition cursor-pointer">
                    <i class="fa-solid fa-plus mr-1"></i> Nouvelle Inscription
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
            <form method="GET" action="{{ route('proviseur.inscriptions.index') }}" class="flex gap-4">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-500 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher par nom d'élève ou année scolaire..." class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3 py-2 text-sm text-white outline-none focus:border-indigo-500">
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-xs px-4 py-2 rounded-xl font-bold transition">Rechercher</button>
                @if($search)
                    <a href="{{ route('proviseur.inscriptions.index') }}" class="bg-slate-800/50 hover:bg-slate-800 text-slate-400 text-xs px-3 py-2 rounded-xl transition flex items-center">Réinitialiser</a>
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
                            <th class="p-3">Élève</th>
                            <th class="p-3">Classe</th>
                            <th class="p-3">Année scolaire</th>
                            <th class="p-3">Statut</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($inscriptions as $inscription)
                            <tr class="hover:bg-slate-800/50">
                                <td class="p-3 font-mono text-slate-500">{{ $inscription->id }}</td>
                                <td class="p-3 font-semibold text-white">{{ $inscription->eleve->nom_complet }}</td>
                                <td class="p-3 text-slate-400">{{ $inscription->classe->nom_classe }} ({{ $inscription->classe->option->nomoption ?? 'Générale' }})</td>
                                <td class="p-3 text-slate-400">{{ $inscription->annee_scolaire }}</td>
                                <td class="p-3">
                                    @if($inscription->statut == 'actif')
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-500/10 text-emerald-400">Actif</span>
                                    @elseif($inscription->statut == 'inactif')
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-rose-500/10 text-rose-400">Inactif</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-500/10 text-blue-400">Terminé</span>
                                    @endif
                                </td>
                                <td class="p-3 text-right space-x-2">
                                    <button @click="inscEdit = {{ json_encode($inscription) }}; modalEdit = true" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-2.5 py-1.5 rounded-lg transition"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('proviseur.inscriptions.destroy', $inscription->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement cette inscription ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs bg-slate-800 hover:bg-slate-700 text-rose-400 px-2.5 py-1.5 rounded-lg transition"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-slate-500">Aucune inscription trouvée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $inscriptions->links() }}</div>
        </div>

    </div>

    <!-- MODAL AJOUT -->
    <div x-show="modalAjout" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Nouvelle inscription</h2>
            <form action="{{ route('proviseur.inscriptions.store') }}" method="POST" class="space-y-3">
                @csrf
                <select name="eleve_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="">Sélectionnez un élève</option>
                    @foreach($eleves as $eleve)
                        <option value="{{ $eleve->id }}">{{ $eleve->nom_complet }}</option>
                    @endforeach
                </select>
                <select name="classe_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="">Sélectionnez une classe</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}">{{ $classe->nom_classe }} ({{ $classe->option->nomoption ?? 'Générale' }})</option>
                    @endforeach
                </select>
                <select name="annee_scolaire" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="">Année scolaire</option>
                    @foreach($annees as $annee)
                        <option value="{{ $annee->anneescolaire }}">{{ $annee->anneescolaire }}</option>
                    @endforeach
                </select>
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
            <h2 class="text-base font-bold text-white">Modifier l'inscription</h2>
            <form :action="'{{ route('proviseur.inscriptions.update', ':id') }}'.replace(':id', inscEdit.id)" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <select name="classe_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="">Sélectionnez une classe</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" :selected="inscEdit.classe_id == {{ $classe->id }}">{{ $classe->nom_classe }}</option>
                    @endforeach
                </select>
                <select name="annee_scolaire" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    @foreach($annees as $annee)
                        <option value="{{ $annee->anneescolaire }}" :selected="inscEdit.annee_scolaire == '{{ $annee->anneescolaire }}'">{{ $annee->anneescolaire }}</option>
                    @endforeach
                </select>
                <select name="statut" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                    <option value="actif" :selected="inscEdit.statut == 'actif'">Actif</option>
                    <option value="inactif" :selected="inscEdit.statut == 'inactif'">Inactif</option>
                    <option value="termine" :selected="inscEdit.statut == 'termine'">Terminé</option>
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

