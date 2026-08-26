<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle année scolaire</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased">
    @include('layouts.sidebar')
    <div class="flex-1 md:ml-64 lg:ml-72 min-w-0">
        @include('layouts.header')
        <main class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto">
            <div class="bg-gradient-to-r from-emerald-950 via-slate-950 to-slate-900 border border-emerald-500/20 p-6 sm:p-8 rounded-3xl shadow-xl mb-6">
                <p class="text-emerald-400 text-xs font-bold uppercase tracking-wider mb-2"><i class="fa-solid fa-calendar-days mr-2"></i>Calendrier académique</p>
                <h1 class="text-2xl sm:text-3xl font-black text-white">Nouvelle année scolaire</h1>
                <p class="text-slate-400 text-sm mt-2">Ajoutez une période académique au format 2026-2027.</p>
            </div>
            <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-lg">
                <form action="{{ route('annees.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="anneescolaire" class="block text-sm font-bold text-slate-200 mb-2">Année scolaire</label>
                        <input type="text" name="anneescolaire" id="anneescolaire" value="{{ old('anneescolaire') }}" pattern="[0-9]{4}-[0-9]{4}" maxlength="9" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-4 py-3 outline-none focus:border-emerald-500" required placeholder="2026-2027">
                        @error('anneescolaire')<p class="text-rose-400 text-xs mt-2">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex justify-between gap-3">
                        <a href="{{ route('annees.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 transition">Annuler</a>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition"><i class="fa-solid fa-check mr-2"></i>Enregistrer</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>