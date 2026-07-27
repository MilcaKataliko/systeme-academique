<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Années Scolaires — Proviseur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6">

    <div class="max-w-5xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-xl font-black text-white">Années Scolaires</h1>
                <p class="text-xs text-slate-400">Définissez les années scolaires de votre établissement</p>
            </div>
            <a href="{{ route('proviseur.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-xl transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
            </a>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm mb-4">
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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Formulaire d'ajout -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl h-fit">
                <h2 class="text-base font-bold text-white mb-4">Ajouter une Année</h2>

                <form action="{{ route('proviseur.annees.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Année scolaire</label>
                        <input type="text" name="anneescolaire" placeholder="ex: 2025-2026" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-indigo-500">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2.5 rounded-xl text-sm transition cursor-pointer">
                        <i class="fa-solid fa-plus mr-1"></i> Ajouter l'année
                    </button>
                </form>
            </div>

            <!-- Liste des années -->
            <div class="md:col-span-2 bg-slate-900 border border-slate-800 p-6 rounded-2xl">
                <h2 class="text-base font-bold text-white mb-4">Années enregistrées</h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Année scolaire</th>
                                <th class="p-3">Statut</th>
                                <th class="p-3">Date d'ajout</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($annees as $annee)
                                <tr class="hover:bg-slate-800/50">
                                    <td class="p-3 font-mono text-slate-500">{{ $annee->idAnnee }}</td>
                                    <td class="p-3 font-semibold text-white">{{ $annee->anneescolaire }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-500/10 text-emerald-400">Actif</span>
                                    </td>
                                    <td class="p-3 text-xs text-slate-400">{{ $annee->created_at ? $annee->created_at->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="p-3 text-right">
                                        <form action="{{ route('proviseur.annees.destroy', $annee->idAnnee) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement cette année scolaire ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs bg-slate-800 hover:bg-slate-700 text-rose-400 px-2.5 py-1.5 rounded-lg transition"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-slate-500">Aucune année scolaire enregistrée pour l'instant.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</body>
</html>

