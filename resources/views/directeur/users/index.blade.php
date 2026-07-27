<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Personnel & Élèves</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen p-6">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- En-tête -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h1 class="text-2xl font-black text-white">Gestion des Utilisateurs</h1>
                <p class="text-xs text-slate-400">Ajout et suivi des comptes de votre établissement</p>
            </div>
            <a href="{{ route('directeur.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-xl transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Retour au Tableau de Bord
            </a>
        </div>

        <!-- Alerte de succès -->
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm flex items-center">
                <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Formulaire d'ajout -->
            <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl h-fit">
                <h2 class="text-lg font-bold text-white mb-4">Créer un compte</h2>

                <form action="{{ route('directeur.users.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nom Complet</label>
                        <input type="text" name="name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Adresse Email</label>
                        <input type="email" name="email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Mot de passe initial</label>
                        <input type="password" name="password" required minlength="6" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Attribuer un Rôle</label>
                        <select name="role" required class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl px-3 py-2 text-sm outline-none focus:border-blue-500">
                            <option value="proviseur">Proviseur / Directeur des etudes</option>
                            <option value="enseignant">Enseignant / Professeur</option>
                            <option value="comptable">Comptable / Caisse</option>
                            <option value="eleve">Élève</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-xl text-sm transition cursor-pointer">
                        <i class="fa-solid fa-user-plus mr-2"></i> Enregistrer l'utilisateur
                    </button>
                </form>
            </div>

            <!-- Liste des comptes -->
            <div class="lg:col-span-2 bg-slate-900 border border-slate-800 p-6 rounded-2xl">
                <h2 class="text-lg font-bold text-white mb-4">Comptes actifs de l'école</h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-950 text-slate-400 uppercase text-xs">
                            <tr>
                                <th class="p-3">Nom</th>
                                <th class="p-3">Email</th>
                                <th class="p-3">Rôle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-800/50">
                                    <td class="p-3 font-semibold text-white">{{ $user->name }}</td>
                                    <td class="p-3 text-slate-400">{{ $user->email }}</td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                                            {{ $user->role === 'directeur' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : '' }}
                                            {{ $user->role === 'proviseur' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : '' }}
                                            {{ $user->role === 'enseignant' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                            {{ $user->role === 'comptable' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                            {{ $user->role === 'eleve' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-slate-500">Aucun utilisateur enregistré pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>

        </div>

    </div>

</body>
</html>