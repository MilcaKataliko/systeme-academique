<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs — Directeur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-rose-600 selection:text-white">

    <!-- 1. BARRE DE NAVIGATION LATÉRALE GAUCHE (SIDEBAR) -->
    @include('layouts.sidebar')

    <!-- 2. CONTENU PRINCIPAL (ESPACE DE TRAVAIL) -->
    <div class="flex-1 md:ml-64 lg:ml-72 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header Supérieur -->
        @include('layouts.header')

        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- En-tête -->
            <div class="bg-gradient-to-r from-rose-950 via-slate-950 to-slate-900 border border-rose-500/20 p-6 sm:p-8 rounded-3xl shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-semibold uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-users-gear"></i> Administration
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Gestion des Utilisateurs</h1>
                    <p class="text-slate-400 mt-1 text-sm">Gérez les comptes, les rôles d'accès et la réinitialisation des identifiants.</p>
                </div>
                <a href="{{ route('register.show') }}" class="bg-rose-600 hover:bg-rose-500 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition inline-flex items-center space-x-2 shadow-lg shadow-rose-600/30">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Nouvel utilisateur</span>
                </a>
            </div>

            <!-- Messages flash -->
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i><span>{!! session('success') !!}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Tableau des Utilisateurs -->
            <div class="bg-slate-950/80 border border-slate-800/90 rounded-2xl overflow-hidden shadow-lg">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <h2 class="font-bold text-base text-white flex items-center gap-2">
                        <i class="fa-solid fa-list text-rose-400"></i> Comptes utilisateurs
                    </h2>
                    <span class="text-xs text-slate-400 font-semibold">{{ $users->count() }} compte(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-900/90 text-slate-400 uppercase text-[11px] font-bold">
                            <tr>
                                <th class="p-4">Utilisateur</th>
                                <th class="p-4">Email</th>
                                <th class="p-4">Rôle</th>
                                <th class="p-4">Date de création</th>
                                <th class="p-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-900/50 transition">
                                    <td class="p-4 font-semibold text-white flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center font-bold text-xs uppercase text-slate-300 border border-slate-700">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <span>{{ $user->name }}</span>
                                    </td>
                                    <td class="p-4 font-mono text-xs text-slate-400">{{ $user->email }}</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize border {{ 
                                            match($user->role) {
                                                'directeur' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                                'comptable' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                                'enseignant' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                                'eleve' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
                                                default => 'bg-slate-500/10 text-slate-400 border-slate-500/20'
                                            }
                                        }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-xs text-slate-400">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '—' }}</td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('users.edit', $user->id) }}" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-semibold transition" title="Modifier">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            @if($user->id !== Auth::id())
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement l\'utilisateur {{ $user->name }} ?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-2 bg-rose-500/10 hover:bg-rose-500 hover:text-white text-rose-400 rounded-lg text-xs font-semibold transition" title="Supprimer">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-slate-500 text-xs">
                                        <i class="fa-solid fa-users text-3xl mb-2 block"></i>
                                        Aucun utilisateur enregistré.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>
