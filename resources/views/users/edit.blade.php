<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Utilisateur — Système Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-slate-950 via-blue-950 to-slate-950 px-4 py-12">
    
    <div class="w-full max-w-2xl bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-2xl shadow-2xl">
        
        <div class="text-center mb-8">
            <div class="inline-flex bg-blue-600 p-3 rounded-xl text-white font-black tracking-wider text-xl mb-4 shadow-lg shadow-blue-600/20">
                EPST
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">Modifier le compte</h2>
            <p class="text-xs text-slate-400 mt-2">{{ $user->name }}</p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs text-center">
                {{ $errors->first() }}
            </div>
        @endif

<form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Photo de profil -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Photo de profil</label>
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 rounded-full overflow-hidden border border-slate-700 bg-slate-900/60 flex items-center justify-center shrink-0">
                        @if($user->photo)
                            <img src="{{ asset('storage/photos/' . $user->photo) }}" alt="Photo de {{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-slate-500 text-2xl font-black">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="photo" accept="image/*"
                            class="block w-full text-sm text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer
                            bg-slate-900/60 border border-slate-700 rounded-xl py-2 px-3 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150" />
                        <p class="text-[10px] text-slate-500 mt-1.5">Formats acceptés : JPG, PNG, GIF, WEBP. Taille max : 2 Mo.</p>
                    </div>
                </div>
            </div>

            <!-- Nom complet -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Nom complet</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150" 
                        placeholder="Nom et prénom" />
                </div>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Adresse Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150" 
                        placeholder="email@exemple.com" />
                </div>
            </div>

            <!-- Rôle -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Rôle</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-user-tag"></i>
                    </span>
                    <select name="role" required
                        class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150 appearance-none">
                        <option value="enseignant" {{ (old('role', $user->role) == 'enseignant') ? 'selected' : '' }}>Enseignant</option>
                        <option value="comptable" {{ (old('role', $user->role) == 'comptable') ? 'selected' : '' }}>Comptable</option>
                        <option value="eleve" {{ (old('role', $user->role) == 'eleve') ? 'selected' : '' }}>Élève</option>
                    </select>
                </div>
            </div>

            <!-- Mot de passe (optionnel) -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                    Nouveau mot de passe <span class="text-slate-500 font-normal">(laisser vide pour ne pas changer)</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password"
                        class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 pl-10 pr-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150" 
                        placeholder="Laisser vide pour conserver l'actuel" />
                </div>
            </div>

            @if($user->role === 'enseignant' || old('role', $user->role) === 'enseignant')
            <!-- Section Informations professionnelles Enseignant -->
            <div class="border-t border-slate-700/50 pt-5 mt-5">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="bg-purple-500/10 p-2 rounded-lg border border-purple-500/20">
                        <i class="fa-solid fa-chalkboard-user text-purple-400"></i>
                    </div>
                    <h3 class="font-bold text-white text-sm">Informations professionnelles</h3>
                    @if($enseignant)
                        <span class="text-[10px] bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded-full border border-emerald-500/20">
                            Matricule: {{ $enseignant->matricule }}
                        </span>
                    @else
                        <span class="text-[10px] bg-amber-500/10 text-amber-400 px-2 py-0.5 rounded-full border border-amber-500/20">
                            Profil incomplet
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Nom</label>
                        <input type="text" name="enseignant_nom" 
                               value="{{ old('enseignant_nom', $enseignant->nom ?? old('name', $user->name)) }}"
                               class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition duration-150" 
                               placeholder="Nom de famille" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Postnom</label>
                        <input type="text" name="enseignant_postnom" 
                               value="{{ old('enseignant_postnom', $enseignant->postnom ?? '') }}"
                               class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition duration-150" 
                               placeholder="Postnom" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Prénom</label>
                        <input type="text" name="enseignant_prenom" 
                               value="{{ old('enseignant_prenom', $enseignant->prenom ?? '') }}"
                               class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition duration-150" 
                               placeholder="Prénom" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Téléphone</label>
                        <input type="text" name="enseignant_telephone" 
                               value="{{ old('enseignant_telephone', $enseignant->telephone ?? '') }}"
                               class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition duration-150" 
                               placeholder="+243 XXX XXX XXX" />
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Grade</label>
                        <select name="enseignant_grade"
                                class="block w-full rounded-xl bg-slate-900/60 border border-slate-700 text-slate-100 py-2.5 px-4 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition duration-150 appearance-none">
                            <option value="Titulaire" {{ (old('enseignant_grade', $enseignant->grade ?? '') == 'Titulaire') ? 'selected' : '' }}>Titulaire</option>
                            <option value="Principal" {{ (old('enseignant_grade', $enseignant->grade ?? '') == 'Principal') ? 'selected' : '' }}>Principal</option>
                            <option value="Directeur des études" {{ (old('enseignant_grade', $enseignant->grade ?? '') == 'Directeur des études') ? 'selected' : '' }}>Directeur des études</option>
                            <option value="A1" {{ (old('enseignant_grade', $enseignant->grade ?? '') == 'A1') ? 'selected' : '' }}>A1</option>
                            <option value="A2" {{ (old('enseignant_grade', $enseignant->grade ?? '') == 'A2') ? 'selected' : '' }}>A2</option>
                            <option value="G0" {{ (old('enseignant_grade', $enseignant->grade ?? '') == 'G0') ? 'selected' : '' }}>G0</option>
                            <option value="G1" {{ (old('enseignant_grade', $enseignant->grade ?? '') == 'G1') ? 'selected' : '' }}>G1</option>
                            <option value="G2" {{ (old('enseignant_grade', $enseignant->grade ?? '') == 'G2') ? 'selected' : '' }}>G2</option>
                            <option value="G3" {{ (old('enseignant_grade', $enseignant->grade ?? '') == 'G3') ? 'selected' : '' }}>G3</option>
                        </select>
                    </div>
                </div>

                @if(!$enseignant)
                    <div class="mt-3 p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-xs text-amber-400 flex items-center space-x-2">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Ce compte enseignant n'a pas encore de fiche professionnelle complète. Remplissez les champs ci-dessus et sauvegardez pour créer automatiquement sa fiche.</span>
                    </div>
                @endif
            </div>
            @endif

            <!-- Bouton -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 active:scale-[0.98] transition-all duration-150 cursor-pointer flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Enregistrer les modifications</span>
                </button>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('users.index') }}" class="text-xs text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Retour à la liste
                </a>
            </div>
        </form>
    </div>
</body>
</html>

