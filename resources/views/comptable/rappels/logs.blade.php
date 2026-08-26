<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des rappels — Comptable</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans">

    <nav class="bg-slate-950 border-b border-slate-800 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="bg-blue-600 p-2 rounded-lg text-white font-black tracking-wider">EPST</div>
            <a href="{{ route('comptable.dashboard') }}" class="font-bold text-lg tracking-tight hover:text-emerald-400 transition">Comptabilité</a>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-slate-400 bg-slate-800 px-3 py-1.5 rounded-full border border-slate-700">
                <i class="fa-solid fa-calculator text-green-400 mr-2"></i>{{ Auth::user()->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-500/10 hover:bg-red-500 hover:text-white text-red-400 border border-red-500/20 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Déconnexion
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6 md:p-8 space-y-8">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-white">
                    <i class="fa-solid fa-clock-rotate-left text-amber-400 mr-3"></i>Historique des rappels
                </h1>
                <p class="text-slate-400 text-sm mt-1">Suivi des rappels de paiement envoyés aux élèves.</p>
            </div>
            <a href="{{ route('comptable.rappels.index') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                <i class="fa-solid fa-arrow-left mr-2"></i> Configuration
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation"></i><span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-slate-950 border border-slate-800 p-4 rounded-2xl shadow-lg text-center">
                <p class="text-2xl font-black text-white">{{ $stats['total'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Total</p>
            </div>
            <div class="bg-slate-950 border border-slate-800 p-4 rounded-2xl shadow-lg text-center">
                <p class="text-2xl font-black text-emerald-400">{{ $stats['email'] }}</p>
                <p class="text-xs text-slate-400 mt-1">Emails</p>
            </div>
            <div class="bg-slate-950 border border-slate-800 p-4 rounded-2xl shadow-lg text-center">
                <p class="text-2xl font-black text-purple-400">{{ $stats['sms'] }}</p>
                <p class="text-xs text-slate-400 mt-1">SMS</p>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1">Statut</label>
                    <select name="statut" class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-3 py-2 text-sm outline-none focus:border-amber-500 transition">
                        <option value="">Tous</option>
                        <option value="envoye" {{ request('statut') == 'envoye' ? 'selected' : '' }}>Envoyé</option>
                        <option value="echoue" {{ request('statut') == 'echoue' ? 'selected' : '' }}>Échoué</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1">Type</label>
                    <select name="type_rappel" class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-3 py-2 text-sm outline-none focus:border-amber-500 transition">
                        <option value="">Tous</option>
                        <option value="hebdomadaire" {{ request('type_rappel') == 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="mensuel" {{ request('type_rappel') == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                        <option value="trimestriel" {{ request('type_rappel') == 'trimestriel' ? 'selected' : '' }}>Trimestriel</option>
                        <option value="semestriel" {{ request('type_rappel') == 'semestriel' ? 'selected' : '' }}>Semestriel</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1">Du</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                           class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-3 py-2 text-sm outline-none focus:border-amber-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1">Au</label>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                           class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-3 py-2 text-sm outline-none focus:border-amber-500 transition">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white px-4 py-2 rounded-xl text-sm font-semibold transition cursor-pointer">
                        <i class="fa-solid fa-filter mr-1"></i> Filtrer
                    </button>
                    <a href="{{ route('comptable.rappels.logs') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 px-4 py-2 rounded-xl text-sm font-semibold transition inline-flex items-center">
                        <i class="fa-solid fa-undo mr-1"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Logs -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 bg-slate-900/50">
                            <th class="text-left py-3 px-4 font-semibold text-slate-400 uppercase text-xs">Date</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-400 uppercase text-xs">Élève</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-400 uppercase text-xs">Classe</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-400 uppercase text-xs">Solde</th>
                            <th class="text-center py-3 px-4 font-semibold text-slate-400 uppercase text-xs">Type</th>
                            <th class="text-center py-3 px-4 font-semibold text-slate-400 uppercase text-xs">Canaux</th>
                            <th class="text-center py-3 px-4 font-semibold text-slate-400 uppercase text-xs">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="border-b border-slate-800/50 hover:bg-slate-900/30 transition">
                                <td class="py-3 px-4 text-slate-300 text-xs">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-white font-medium">
                                    {{ $log->inscription->eleve->nom ?? '—' }} {{ $log->inscription->eleve->postnom ?? '' }}
                                </td>
                                <td class="py-3 px-4 text-slate-400">
                                    {{ $log->inscription->classe->nom_classe ?? '—' }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold {{ $log->solde > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                                    {{ number_format($log->solde, 2) }} $
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="text-xs bg-slate-800 text-slate-300 px-2 py-1 rounded-full">{{ $log->type_rappel }}</span>
                                </td>
                                <td class="py-3 px-4 text-center text-xs">
                                    <div class="flex items-center justify-center space-x-1">
                                        @if($log->email_envoye)
                                            <span class="bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-full">
                                                <i class="fa-solid fa-envelope"></i>
                                            </span>
                                        @endif
                                        @if($log->sms_envoye)
                                            <span class="bg-purple-500/20 text-purple-400 px-2 py-0.5 rounded-full">
                                                <i class="fa-solid fa-mobile-screen"></i>
                                            </span>
                                        @endif
                                        @if(!$log->email_envoye && !$log->sms_envoye)
                                            <span class="text-slate-600">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($log->statut === 'envoye')
                                        <span class="bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded-full text-xs font-semibold"><i class="fa-solid fa-check mr-1"></i>Envoyé</span>
                                    @elseif($log->statut === 'echoue')
                                        <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded-full text-xs font-semibold" title="{{ $log->message_erreur }}"><i class="fa-solid fa-xmark mr-1"></i>Échoué</span>
                                    @else
                                        <span class="bg-amber-500/20 text-amber-400 px-2 py-1 rounded-full text-xs font-semibold"><i class="fa-solid fa-clock mr-1"></i>En attente</span>
                                    @endif
                                </td>
                            </tr>
                            @if($log->message_erreur)
                            <tr class="bg-red-900/10">
                                <td colspan="7" class="py-1 px-8 text-xs text-red-400">
                                    <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $log->message_erreur }}
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-500">
                                    <i class="fa-solid fa-bell-slash text-3xl mb-3 block"></i>
                                    <p>Aucun rappel envoyé pour le moment.</p>
                                    <p class="text-xs mt-1">Configurez les rappels et déclenchez un envoi manuel.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-slate-800">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </main>

</body>
</html>

