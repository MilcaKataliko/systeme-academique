<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Cours — Proviseur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6" x-data="{ modalAjout: false, modalEdit: false, courEdit: {} }">

    <div class="max-w-5xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-xl font-black text-white">Gestion des Cours</h1>
                <p class="text-xs text-slate-400">Définissez les matières enseignées dans l'établissement</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('proviseur.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-xl transition flex items-center">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                </a>
                <button @click="modalAjout = true" class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-xl transition cursor-pointer">
                    <i class="fa-solid fa-plus mr-1"></i> Nouveau Cours
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
            <form method="GET" action="{{ route('proviseur.cours.index') }}" class="flex gap-4">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-500 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher un cours..." class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3 py-2 text-sm text-white outline-none focus:border-indigo-500">
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-xs px-4 py-2 rounded-xl font-bold transition">Rechercher</button>
                @if($search)
                    <a href="{{ route('proviseur.cours.index') }}" class="bg-slate-800/50 hover:bg-slate-800 text-slate-400 text-xs px-3 py-2 rounded-xl transition flex items-center">Réinitialiser</a>
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
                            <th class="p-3">Code</th>
                            <th class="p-3">Nom du cours</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($cours as $cour)
                            <tr class="hover:bg-slate-800/50">
                                <td class="p-3 font-mono text-slate-500">{{ $cour->id }}</td>
                                <td class="p-3 font-mono text-indigo-400 font-bold">{{ $cour->code_cours ?? 'N/A' }}</td>
                                <td class="p-3 font-semibold text-white">{{ $cour->nom_cours }}</td>
                                <td class="p-3 text-right space-x-2">
                                    <button @click="courEdit = {{ json_encode($cour) }}; modalEdit = true" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-2.5 py-1.5 rounded-lg transition"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('proviseur.cours.destroy', $cour->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement ce cours ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs bg-slate-800 hover:bg-slate-700 text-rose-400 px-2.5 py-1.5 rounded-lg transition"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-center text-slate-500">Aucun cours trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $cours->links() }}</div>
        </div>

    </div>

    <!-- MODAL AJOUT -->
    <div x-show="modalAjout" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Nouveau cours</h2>
            <form action="{{ route('proviseur.cours.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="nom_cours" placeholder="Nom du cours (ex: Mathématiques)" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <input type="text" name="code_cours" placeholder="Code (ex: MATH)" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
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
            <h2 class="text-base font-bold text-white">Modifier le cours</h2>
            <form :action="'{{ route('proviseur.cours.update', ':id') }}'.replace(':id', courEdit.id)" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="text" name="nom_cours" x-model="courEdit.nom_cours" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <input type="text" name="code_cours" x-model="courEdit.code_cours" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white">
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-amber-600 text-white rounded-xl font-bold">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

