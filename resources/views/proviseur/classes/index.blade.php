<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Classes — Proviseur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6" x-data="{ modalAjout: false, modalEdit: false, classeEdit: {} }">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-xl font-black text-white">Gestion des Classes</h1>
                <p class="text-xs text-slate-400">Configurez les promotions et sections de votre établissement</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('proviseur.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-xl transition flex items-center">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour
                </a>
                <button @click="modalAjout = true" class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2 rounded-xl transition cursor-pointer">
                    <i class="fa-solid fa-plus mr-1"></i> Nouvelle Classe
                </button>
            </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-sm mb-4 space-y-1">
                <div class="font-bold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Oups, il y a un problème :</div>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Liste des Classes -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl">
            <h2 class="text-base font-bold text-white mb-4">Classes configurées</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                        <tr>
                            <th class="p-3">#</th>
                            <th class="p-3">Nom de la classe</th>
                            <th class="p-3">Niveau</th>
                            <th class="p-3">Section</th>
                            <th class="p-3">Option</th>
                            <th class="p-3">Effectif max</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($classes as $classe)
                            <tr class="hover:bg-slate-800/50">
                                <td class="p-3 font-mono text-slate-500">{{ $classe->id }}</td>
                                <td class="p-3 font-semibold text-white">{{ $classe->nom_classe }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-500/10 text-blue-400">{{ $classe->niveau }}</span>
                                </td>
                                <td class="p-3 text-xs text-slate-400">{{ $classe->section ?? 'N/A' }}</td>
                                <td class="p-3 text-xs text-slate-400">{{ $classe->option->nomoption ?? 'Générale' }}</td>
                                <td class="p-3 text-xs text-slate-400">{{ $classe->effectif_max }}</td>
                                <td class="p-3 text-right space-x-2">
                                    <button @click="classeEdit = {{ json_encode($classe) }}; modalEdit = true" class="text-xs bg-slate-800 hover:bg-slate-700 text-amber-400 px-2.5 py-1.5 rounded-lg transition"><i class="fa-solid fa-pen"></i></button>
                                    <form action="{{ route('proviseur.classes.destroy', $classe->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement cette classe ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs bg-slate-800 hover:bg-slate-700 text-rose-400 px-2.5 py-1.5 rounded-lg transition"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center text-slate-500">Aucune classe configurée pour l'instant.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

    </div>

    <!-- MODAL D'AJOUT -->
    <div x-show="modalAjout" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Nouvelle classe</h2>
            <form action="{{ route('proviseur.classes.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="nom_classe" placeholder="Nom de la classe (ex: 1ère Commerciale)" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-indigo-500">

                <select name="niveau" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-indigo-500">
                    <option value="">Sélectionnez le niveau</option>
                    <option value="7ème">7ème</option>
                    <option value="8ème">8ème</option>
                    <option value="1ère">1ère</option>
                    <option value="2ème">2ème</option>
                    <option value="3ème">3ème</option>
                    <option value="4ème">4ème</option>
                </select>

                <input type="text" name="section" placeholder="Section (ex: Scientifique, Littéraire, Technique)" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-indigo-500">

                <select name="option_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-indigo-500">
                    <option value="">Option (facultatif)</option>
                    @foreach($options as $option)
                        <option value="{{ $option->idOption }}">{{ $option->nomoption }} ({{ $option->sigle ?? 'N/A' }})</option>
                    @endforeach
                </select>

                <input type="number" name="effectif_max" placeholder="Effectif maximum (défaut: 50)" min="1" max="200" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-indigo-500">

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalAjout = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-indigo-600 text-white rounded-xl font-bold">Enregistrer</button>
                </div>
            </form>
        </div>

    <!-- MODAL DE MODIFICATION -->
    <div x-show="modalEdit" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl max-w-md w-full space-y-4">
            <h2 class="text-base font-bold text-white">Modifier la classe</h2>
            <form :action="'{{ route('proviseur.classes.update', ':id') }}'.replace(':id', classeEdit.id)" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <input type="text" name="nom_classe" x-model="classeEdit.nom_classe" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-indigo-500">

                <select name="niveau" x-model="classeEdit.niveau" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-indigo-500">
                    <option value="7ème">7ème</option>
                    <option value="8ème">8ème</option>
                    <option value="1ère">1ère</option>
                    <option value="2ème">2ème</option>
                    <option value="3ème">3ème</option>
                    <option value="4ème">4ème</option>
                </select>

                <input type="text" name="section" x-model="classeEdit.section" placeholder="Section" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-indigo-500">

                <select name="option_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-indigo-500">
                    <option value="">Option (facultatif)</option>
                    @foreach($options as $option)
                        <option value="{{ $option->idOption }}" :selected="classeEdit.option_id == {{ $option->idOption }}">{{ $option->nomoption }}</option>
                    @endforeach
                </select>

                <input type="number" name="effectif_max" x-model="classeEdit.effectif_max" min="1" max="200" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-indigo-500">

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2 text-xs bg-slate-800 text-slate-300 rounded-xl">Annuler</button>
                    <button type="submit" class="px-4 py-2 text-xs bg-amber-600 text-white rounded-xl font-bold">Mettre à jour</button>
                </div>
            </form>
        </div>

</body>
</html>
