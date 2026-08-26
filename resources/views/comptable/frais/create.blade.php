<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau frais — Comptable</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
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

    <main class="max-w-xl mx-auto p-6 md:p-8">

        <div class="bg-slate-950 border border-slate-800 rounded-2xl p-8 shadow-xl">
            <h1 class="text-2xl font-black tracking-tight text-white mb-6">
                <i class="fa-solid fa-coins text-emerald-400 mr-3"></i>Nouveau type de frais
            </h1>

            @if($errors->any())
                <div class="mb-5 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('comptable.frais.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Intitulé du frais</label>
                    <input type="text" name="intitule_frais" value="{{ old('intitule_frais') }}" required
                           placeholder="Ex: Minerval, Frais de participation..."
                           class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/40 transition">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Montant standard</label>
                        <input type="number" name="montant_standard" value="{{ old('montant_standard') }}" step="0.01" min="0" required
                               class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/40 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Devise</label>
                        <select name="devise" required
                                class="w-full bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/40 transition">
                            <option value="USD">USD</option>
                            <option value="CDF">CDF (FC)</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-2.5 rounded-xl text-sm font-bold transition cursor-pointer">
                    <i class="fa-solid fa-check mr-2"></i> Créer le type de frais
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('comptable.frais.index') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Retour à la liste
                </a>
            </div>
        </div>

    </main>

</body>
</html>

