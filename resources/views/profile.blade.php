<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - Système Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans flex flex-col md:flex-row antialiased selection:bg-blue-600 selection:text-white">
    @include('layouts.sidebar')

    <div class="flex-1 md:ml-64 lg:ml-72 min-w-0">
        @include('layouts.header')
        <main class="p-4 sm:p-6 lg:p-8 max-w-6xl mx-auto space-y-6">
            <section class="relative overflow-hidden rounded-3xl border border-blue-500/20 bg-gradient-to-r from-blue-950 via-slate-950 to-slate-900 p-6 sm:p-8 shadow-xl">
                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-5">
                    <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl border border-blue-400/30 bg-blue-600 flex items-center justify-center text-2xl font-black text-white shadow-lg">
                        @if($user->photo)
                            <img src="{{ asset('storage/photos/' . $user->photo) }}" alt="Photo de {{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-wider text-blue-400"><i class="fa-solid fa-id-badge mr-2"></i>Espace personnel</p>
                        <h1 class="text-2xl sm:text-3xl font-black text-white">{{ $user->name }}</h1>
                        <p class="mt-1 text-sm text-slate-400">{{ $user->email }}</p>
                        <span class="mt-3 inline-flex items-center gap-2 rounded-full border border-blue-400/20 bg-blue-500/10 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-300">
                            <i class="fa-solid fa-user-shield"></i>{{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>
            </section>

            @if(session('success'))
                <div class="flex items-center gap-2 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-sm text-emerald-400"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="rounded-2xl border border-rose-500/20 bg-rose-500/10 p-4 text-sm text-rose-300"><p class="font-bold">Vérifiez les informations saisies.</p><ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <section class="rounded-2xl border border-slate-800/90 bg-slate-950/80 p-6 shadow-lg lg:col-span-2">
                    <div class="mb-6 flex items-center gap-3 border-b border-slate-800 pb-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-blue-500/20 bg-blue-500/10 text-blue-400"><i class="fa-solid fa-user-pen"></i></div>
                        <div><h2 class="font-bold text-white">Informations personnelles</h2><p class="text-xs text-slate-500">Ces informations servent à identifier votre compte.</p></div>
                    </div>
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="name" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-400">Nom complet</label>
                                <div class="relative"><i class="fa-solid fa-user absolute left-3.5 top-3.5 text-slate-500"></i><input id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border border-slate-700 bg-slate-900/70 py-3 pl-10 pr-4 text-sm text-white outline-none transition focus:border-blue-500" /></div>
                            </div>
                            <div>
                                <label for="email" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-400">Adresse email</label>
                                <div class="relative"><i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-slate-500"></i><input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-xl border border-slate-700 bg-slate-900/70 py-3 pl-10 pr-4 text-sm text-white outline-none transition focus:border-blue-500" /></div>
                            </div>
                        </div>
                        <div>
                            <label for="photo" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-400">Photo de profil</label>
                            <input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full rounded-xl border border-slate-700 bg-slate-900/70 px-3 py-2.5 text-sm text-slate-300 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-3 file:py-2 file:font-semibold file:text-white hover:file:bg-blue-500" />
                            <p class="mt-1.5 text-[11px] text-slate-500">JPG, PNG, GIF ou WEBP, 2 Mo maximum.</p>
                        </div>
                        <div class="flex justify-end border-t border-slate-800 pt-5"><button type="submit" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-500"><i class="fa-solid fa-floppy-disk mr-2"></i>Enregistrer le profil</button></div>
                    </form>
                </section>

                <section class="rounded-2xl border border-slate-800/90 bg-slate-950/80 p-6 shadow-lg">
                    <div class="mb-6 flex items-center gap-3 border-b border-slate-800 pb-4"><div class="flex h-10 w-10 items-center justify-center rounded-xl border border-amber-500/20 bg-amber-500/10 text-amber-400"><i class="fa-solid fa-lock"></i></div><div><h2 class="font-bold text-white">Sécurité</h2><p class="text-xs text-slate-500">Modifiez votre mot de passe.</p></div></div>
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}"><input type="hidden" name="email" value="{{ $user->email }}">
                        <div><label for="current_password" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-400">Mot de passe actuel</label><input id="current_password" type="password" name="current_password" class="w-full rounded-xl border border-slate-700 bg-slate-900/70 px-4 py-3 text-sm text-white outline-none focus:border-amber-500" autocomplete="current-password"></div>
                        <div><label for="password" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-400">Nouveau mot de passe</label><input id="password" type="password" name="password" minlength="8" class="w-full rounded-xl border border-slate-700 bg-slate-900/70 px-4 py-3 text-sm text-white outline-none focus:border-amber-500" autocomplete="new-password"><p class="mt-1.5 text-[11px] text-slate-500">8 caractères minimum.</p></div>
                        <div><label for="password_confirmation" class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-400">Confirmation</label><input id="password_confirmation" type="password" name="password_confirmation" minlength="8" class="w-full rounded-xl border border-slate-700 bg-slate-900/70 px-4 py-3 text-sm text-white outline-none focus:border-amber-500" autocomplete="new-password"></div>
                        <button type="submit" class="w-full rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm font-bold text-amber-300 transition hover:bg-amber-500 hover:text-white"><i class="fa-solid fa-key mr-2"></i>Changer le mot de passe</button>
                    </form>
                </section>
            </div>

            <section class="rounded-2xl border border-slate-800/90 bg-slate-950/80 p-6 shadow-lg">
                <h2 class="mb-4 flex items-center gap-2 font-bold text-white"><i class="fa-solid fa-circle-info text-cyan-400"></i>Informations du compte</h2>
                <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-800 bg-slate-900 p-4"><p class="text-xs font-bold uppercase text-slate-500">Rôle</p><p class="mt-1 font-semibold capitalize text-white">{{ $user->role }}</p></div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900 p-4"><p class="text-xs font-bold uppercase text-slate-500">Établissement</p><p class="mt-1 font-semibold text-white">{{ $user->ecole?->nom_ecole ?? 'Non renseigné' }}</p></div>
                    <div class="rounded-xl border border-slate-800 bg-slate-900 p-4"><p class="text-xs font-bold uppercase text-slate-500">Compte créé le</p><p class="mt-1 font-semibold text-white">{{ $user->created_at?->format('d/m/Y') ?? 'Non renseigné' }}</p></div>
                    @if($enseignant)
                        <div class="rounded-xl border border-slate-800 bg-slate-900 p-4"><p class="text-xs font-bold uppercase text-slate-500">Matricule enseignant</p><p class="mt-1 font-mono font-semibold text-purple-300">{{ $enseignant->matricule }}</p></div>
                        <div class="rounded-xl border border-slate-800 bg-slate-900 p-4"><p class="text-xs font-bold uppercase text-slate-500">Grade</p><p class="mt-1 font-semibold text-white">{{ $enseignant->grade ?: 'Non renseigné' }}</p></div>
                        <div class="rounded-xl border border-slate-800 bg-slate-900 p-4"><p class="text-xs font-bold uppercase text-slate-500">Téléphone</p><p class="mt-1 font-semibold text-white">{{ $enseignant->telephone ?: 'Non renseigné' }}</p></div>
                    @endif
                    @if($eleve)
                        <div class="rounded-xl border border-slate-800 bg-slate-900 p-4"><p class="text-xs font-bold uppercase text-slate-500">Matricule élève</p><p class="mt-1 font-mono font-semibold text-cyan-300">{{ $eleve->code_matricule }}</p></div>
                        <div class="rounded-xl border border-slate-800 bg-slate-900 p-4"><p class="text-xs font-bold uppercase text-slate-500">Classe actuelle</p><p class="mt-1 font-semibold text-white">{{ $eleve->inscriptions->first()?->classe?->nom_classe ?? 'Non assignée' }}</p></div>
                            <div class="rounded-xl border border-slate-800 bg-slate-900 p-4"><p class="text-xs font-bold uppercase text-slate-500">Identifiant du compte</p><p class="mt-1 font-mono font-semibold text-white">#{{ $user->id }}</p></div>
                    @endif
                </div>
            </section>
        </main>
    </div>
</body>
</html>
