<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration des rappels — Comptable</title>
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

    <main class="max-w-5xl mx-auto p-6 md:p-8 space-y-8">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-white">
                    <i class="fa-solid fa-bell text-amber-400 mr-3"></i>Rappels automatiques
                </h1>
                <p class="text-slate-400 text-sm mt-1">Configurez les rappels de paiement pour les élèves.</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('comptable.rappels.logs') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 px-4 py-2 rounded-xl text-sm font-semibold transition inline-flex items-center">
                    <i class="fa-solid fa-clock-rotate-left mr-2"></i>Historique
                </a>
                <a href="{{ route('comptable.dashboard') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Tableau de bord
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-slate-950 border border-slate-800 p-4 rounded-2xl shadow-lg text-center">
                <p class="text-2xl font-black text-white">{{ $stats->total_rappels }}</p>
                <p class="text-xs text-slate-400 mt-1">Total rappels</p>
            </div>
            <div class="bg-slate-950 border border-slate-800 p-4 rounded-2xl shadow-lg text-center">
                <p class="text-2xl font-black text-emerald-400">{{ $stats->envoyes }}</p>
                <p class="text-xs text-slate-400 mt-1">Envoyés</p>
            </div>
            <div class="bg-slate-950 border border-slate-800 p-4 rounded-2xl shadow-lg text-center">
                <p class="text-2xl font-black text-red-400">{{ $stats->echoues }}</p>
                <p class="text-xs text-slate-400 mt-1">Échoués</p>
            </div>
            <div class="bg-slate-950 border border-slate-800 p-4 rounded-2xl shadow-lg text-center">
                <p class="text-2xl font-black text-blue-400">{{ $stats->emails_envoyes }}</p>
                <p class="text-xs text-slate-400 mt-1">Emails</p>
            </div>
            <div class="bg-slate-950 border border-slate-800 p-4 rounded-2xl shadow-lg text-center">
                <p class="text-2xl font-black text-purple-400">{{ $stats->sms_envoyes }}</p>
                <p class="text-xs text-slate-400 mt-1">SMS</p>
            </div>
        </div>

        <!-- Formulaire de configuration -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h2 class="font-bold text-lg text-white mb-6">
                <i class="fa-solid fa-gear text-amber-400 mr-3"></i>Configuration des rappels
            </h2>

            <form action="{{ route('comptable.rappels.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Activation -->
                <div class="flex items-center justify-between p-4 bg-slate-900/50 rounded-xl border border-slate-700/50">
                    <div>
                        <p class="font-bold text-white">Activer les rappels automatiques</p>
                        <p class="text-xs text-slate-400">Les rappels seront envoyés selon la fréquence configurée</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="actif" value="0">
                        <input type="checkbox" name="actif" value="1" {{ $config->actif ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fréquence -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Fréquence d'envoi</label>
                        <select name="frequence" id="frequence" required
                                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 transition">
                            <option value="hebdomadaire" {{ $config->frequence == 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="mensuel" {{ $config->frequence == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                            <option value="trimestriel" {{ $config->frequence == 'trimestriel' ? 'selected' : '' }}>Trimestriel</option>
                            <option value="semestriel" {{ $config->frequence == 'semestriel' ? 'selected' : '' }}>Semestriel</option>
                        </select>
                    </div>

                    <!-- Jour d'envoi (hebdomadaire) -->
                    <div id="jour_envoi_group">
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Jour d'envoi (hebdomadaire)</label>
                        <select name="jour_envoi"
                                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 transition">
                            <option value="monday" {{ $config->jour_envoi == 'monday' ? 'selected' : '' }}>Lundi</option>
                            <option value="tuesday" {{ $config->jour_envoi == 'tuesday' ? 'selected' : '' }}>Mardi</option>
                            <option value="wednesday" {{ $config->jour_envoi == 'wednesday' ? 'selected' : '' }}>Mercredi</option>
                            <option value="thursday" {{ $config->jour_envoi == 'thursday' ? 'selected' : '' }}>Jeudi</option>
                            <option value="friday" {{ $config->jour_envoi == 'friday' ? 'selected' : '' }}>Vendredi</option>
                            <option value="saturday" {{ $config->jour_envoi == 'saturday' ? 'selected' : '' }}>Samedi</option>
                            <option value="sunday" {{ $config->jour_envoi == 'sunday' ? 'selected' : '' }}>Dimanche</option>
                        </select>
                    </div>

                    <!-- Jour du mois (mensuel) -->
                    <div id="jour_du_mois_group" class="{{ $config->frequence == 'mensuel' || $config->frequence == 'trimestriel' || $config->frequence == 'semestriel' ? '' : 'hidden' }}">
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Jour du mois</label>
                        <input type="number" name="jour_du_mois" value="{{ $config->jour_du_mois ?? 1 }}" min="1" max="31"
                               class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 transition">
                    </div>

                    <!-- Heure d'envoi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Heure d'envoi</label>
                        <select name="heure_envoi"
                                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 transition">
                            @for($h = 0; $h <= 23; $h++)
                                <option value="{{ $h }}" {{ $config->heure_envoi == $h ? 'selected' : '' }}>
                                    {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Canaux -->
                <div class="border-t border-slate-800 pt-6">
                    <p class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4">Canaux d'envoi</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center justify-between p-4 bg-slate-900/50 rounded-xl border border-slate-700/50 cursor-pointer hover:border-emerald-500/30 transition">
                            <div>
                                <p class="font-bold text-white"><i class="fa-solid fa-envelope text-emerald-400 mr-2"></i>Email</p>
                                <p class="text-xs text-slate-400">Envoyer les rappels par email</p>
                            </div>
                            <input type="hidden" name="email_actif" value="0">
                            <input type="checkbox" name="email_actif" value="1" {{ $config->email_actif ? 'checked' : '' }}
                                   class="w-4 h-4 text-emerald-500 bg-slate-700 border-slate-600 rounded focus:ring-emerald-500">
                        </label>
                        <label class="flex items-center justify-between p-4 bg-slate-900/50 rounded-xl border border-slate-700/50 cursor-pointer hover:border-purple-500/30 transition">
                            <div>
                                <p class="font-bold text-white"><i class="fa-solid fa-mobile-screen text-purple-400 mr-2"></i>SMS</p>
                                <p class="text-xs text-slate-400">Envoyer les rappels par SMS</p>
                            </div>
                            <input type="hidden" name="sms_actif" value="0">
                            <input type="checkbox" name="sms_actif" value="1" {{ $config->sms_actif ? 'checked' : '' }}
                                   class="w-4 h-4 text-purple-500 bg-slate-700 border-slate-600 rounded focus:ring-purple-500">
                        </label>
                    </div>
                </div>

                <!-- Message personnalisé SMS -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Message SMS personnalisé <span class="text-slate-500 font-normal lowercase">(optionnel)</span>
                    </label>
                    <textarea name="message_personnalise" rows="3" maxlength="500"
                              placeholder="Ex: Cher parent, {eleve} a un solde de {solde} USD. Merci de payer. {ecole}"
                              class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-500 transition">{{ $config->message_personnalise }}</textarea>
                    <p class="text-xs text-slate-500 mt-1">
                        Variables disponibles : <code class="text-amber-400 bg-slate-800 px-1 rounded">{eleve}</code>,
                        <code class="text-amber-400 bg-slate-800 px-1 rounded">{classe}</code>,
                        <code class="text-amber-400 bg-slate-800 px-1 rounded">{solde}</code>,
                        <code class="text-amber-400 bg-slate-800 px-1 rounded">{ecole}</code>,
                        <code class="text-amber-400 bg-slate-800 px-1 rounded">{annee}</code>
                    </p>
                </div>

                <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                    <i class="fa-solid fa-save mr-2"></i> Enregistrer la configuration
                </button>
            </form>
        </div>

        <!-- Actions rapides -->
        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h2 class="font-bold text-lg text-white mb-4">
                <i class="fa-solid fa-bolt text-emerald-400 mr-3"></i>Actions rapides
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <form action="{{ route('comptable.rappels.declencher') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-3 rounded-xl text-sm font-bold transition cursor-pointer inline-flex items-center justify-center">
                        <i class="fa-solid fa-play mr-2"></i> Déclencher manuellement les rappels
                    </button>
                </form>
                <a href="{{ route('comptable.rappels.logs') }}" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 py-3 rounded-xl text-sm font-bold transition inline-flex items-center justify-center">
                    <i class="fa-solid fa-clock-rotate-left mr-2"></i> Voir l'historique des envois
                </a>
            </div>
        </div>

    </main>

    <script>
        // Afficher/masquer les champs selon la fréquence
        document.getElementById('frequence').addEventListener('change', function() {
            const freq = this.value;
            const jourHebdo = document.getElementById('jour_envoi_group');
            const jourMois = document.getElementById('jour_du_mois_group');

            if (freq === 'hebdomadaire') {
                jourHebdo.style.display = 'block';
                jourMois.style.display = 'none';
            } else if (freq === 'mensuel' || freq === 'trimestriel' || freq === 'semestriel') {
                jourHebdo.style.display = 'none';
                jourMois.style.display = 'block';
            } else {
                jourHebdo.style.display = 'none';
                jourMois.style.display = 'none';
            }
        });
    </script>

</body>
</html>

