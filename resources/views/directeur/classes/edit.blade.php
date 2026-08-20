<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une classe — Directeur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans">

    <!-- Navigation -->
    <nav class="bg-slate-950 border-b border-slate-800 px-8 py-5 flex justify-between items-center shadow-lg">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-600 p-3 rounded-xl text-white font-black tracking-wider text-xl shadow-md">EPST</div>
            <a href="{{ route('directeur.dashboard') }}" class="font-bold text-xl tracking-tight hover:text-blue-400 transition">Systeme Academique</a>
        </div>
        <div class="flex items-center space-x-5">
            <span class="text-sm text-slate-400 bg-slate-800 px-4 py-2 rounded-full border border-slate-700">
                <i class="fa-solid fa-user-tie text-blue-400 mr-2"></i>{{ Auth::user()->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-500/10 hover:bg-red-500 hover:text-white text-red-400 border border-red-500/20 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 cursor-pointer">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Deconnexion
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto p-8 md:p-10 space-y-8">

        <!-- En-tete -->
        <div class="bg-gradient-to-r from-amber-800 to-slate-950 border border-amber-500/30 p-8 rounded-3xl shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight text-white">Modifier la classe</h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        <span class="text-white font-semibold">{{ $classe->nom_classe }}</span> &mdash; Niveau {{ $classe->niveau }}
                        @if($classe->option)
                            &middot; <span class="text-amber-400">{{ $classe->option->nomoption }}</span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('directeur.classes.index') }}" class="text-sm text-slate-400 hover:text-white transition inline-flex items-center bg-slate-800/50 px-4 py-2.5 rounded-xl">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Retour
                </a>
            </div>

        <!-- Messages flash -->
        @if(session('success'))
            <div class="p-5 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-base flex items-center space-x-3 shadow-lg">
                <i class="fa-solid fa-circle-check text-xl"></i><span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-5 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-base flex items-center space-x-3 shadow-lg">
                <i class="fa-solid fa-circle-exclamation text-xl"></i><span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-5 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-base shadow-lg">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire de modification -->
        <div class="bg-slate-950 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
            <div class="px-8 py-6 border-b border-slate-800">
                <h2 class="text-2xl font-bold text-white">
                    <i class="fa-solid fa-pen text-amber-400 mr-3"></i>Modifier les informations
                </h2>
            </div>
            <div class="mx-8 mt-6 p-4 bg-blue-900/30 border border-blue-500/20 rounded-xl text-sm text-slate-300 space-y-2">
                <p class="text-blue-400 font-bold uppercase tracking-wider text-xs">Rappel</p>
                <p><span class="text-white font-semibold">Nom</span> &rarr; ex: "1ere Commerciale A", "7eme"</p>
                <p><span class="text-white font-semibold">Niveau</span> &rarr; 7eme, 8eme, 1ere, 2eme, 3eme, 4eme</p>
                <p><span class="text-white font-semibold">Option</span> &rarr; Optionnel pour 7eme/8eme, obligatoire pour 1ere a 4eme</p>
            </div>

            <form action="{{ route('directeur.classes.update', $classe->id) }}" method="POST" class="p-8 space-y-6">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Nom de la classe <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="nom_classe" value="{{ old('nom_classe', $classe->nom_classe) }}" required maxlength="50"
                           class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-5 py-3.5 text-base outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/40 w-full transition">
                    @error('nom_classe')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-300 uppercase tracking-wider mb-2">
                            Niveau <span class="text-red-400">*</span>
                        </label>
                        <select name="niveau" required class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-5 py-3.5 text-base outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/40 w-full transition"
                                onchange="toggleOptionEdit(this.value)">
                            <option value="">Choisissez...</option>
                            @foreach([7, 8, 1, 2, 3, 4] as $niv)
                                @php $ord = ($niv == 1) ? 'ere' : 'eme'; @endphp
                                <option value="{{ $niv }}" {{ old('niveau', $classe->niveau) == $niv ? 'selected' : '' }}>{{ $niv }}{{ $ord }}</option>
                            @endforeach
                        </select>
                        @error('niveau')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-300 uppercase tracking-wider mb-2">Option</label>
                        <select name="option_id" id="option_select_edit"
                                class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-5 py-3.5 text-base outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/40 w-full transition">
                            <option value="">Pas d'option (7eme/8eme)</option>
                            @foreach($options as $opt)
                                <option value="{{ $opt->idOption }}" {{ old('option_id', $classe->option_id) == $opt->idOption ? 'selected' : '' }}>{{ $opt->nomoption }}</option>
                            @endforeach
                        </select>
                        <p class="text-slate-500 text-xs mt-1">Optionnel pour 7eme/8eme, obligatoire sinon</p>
                        @error('option_id')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-300 uppercase tracking-wider mb-2">Section</label>
                        <input type="text" name="section" value="{{ old('section', $classe->section) }}" placeholder="Scientifique, Litteraire, Technique..."
                               class="bg-slate-900/60 border border-slate-700 text-slate-100 rounded-xl px-5 py-3.5 text-base outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/40 w-full transition">
                        @error('section')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
</div>

                <div class="flex space-x-4 pt-2">
                    <button type="submit"
                            class="flex-1 bg-amber-600 hover:bg-amber-500 text-white py-3.5 rounded-xl text-base font-bold transition-all duration-150 cursor-pointer shadow-lg hover:shadow-amber-500/20">
                        <i class="fa-solid fa-save mr-2"></i> Enregistrer
                    </button>
                    <a href="{{ route('directeur.classes.index') }}"
                       class="bg-slate-800 hover:bg-slate-700 text-slate-300 py-3.5 px-8 rounded-xl text-base font-bold transition inline-flex items-center">
                        Annuler
                    </a>
                </div>
            </form>
        </div>

    </main>

    <script>
    function toggleOptionEdit(niveau) {
        var select = document.getElementById('option_select_edit');
        if (niveau == '7' || niveau == '8') {
            select.removeAttribute('required');
        } else {
            select.setAttribute('required', 'required');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        var niveauSelect = document.querySelector('select[name="niveau"]');
        if (niveauSelect) { toggleOptionEdit(niveauSelect.value); }
    });
    </script>

</body>
</html>
