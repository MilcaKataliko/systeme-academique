@php
    $user = Auth::user();
    $role = $user->role ?? 'directeur';
    $currentRoute = Route::currentRouteName();
    $schoolName = $user->ecole->nom_ecole ?? 'Système Académique';
@endphp

<!-- Sidebar Latérale Gauche -->
<aside id="main-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 md:w-72 bg-slate-950/95 backdrop-blur-xl border-r border-slate-800/80 flex flex-col justify-between transition-all duration-300 -translate-x-full md:translate-x-0 shadow-2xl">
    
    <!-- Header Sidebar -->
    <div class="flex flex-col">
        <!-- Logo & Établissement -->
        <div class="p-5 border-b border-slate-800/70 flex items-center justify-between">
            <a href="{{ 
                match($role) {
                    'directeur' => route('directeur.dashboard'),
                    'comptable' => route('comptable.dashboard'),
                    'enseignant' => route('enseignant.dashboard'),
                    'eleve' => route('eleve.dashboard'),
                    default => '/'
                }
            }}" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-blue-500/25 group-hover:scale-105 transition-transform">
                    {{ strtoupper(substr($schoolName, 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <h1 class="font-bold text-slate-100 text-sm tracking-tight truncate leading-tight group-hover:text-blue-400 transition-colors">
                        {{ $schoolName }}
                    </h1>
                    <span class="text-[11px] font-semibold tracking-wider text-blue-400 uppercase">
                        Système Académique
                    </span>
                </div>
            </a>
            <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-800/50">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Navigation Links selon le rôle -->
        <nav class="p-4 space-y-1.5 overflow-y-auto max-h-[calc(100vh-220px)] custom-scrollbar">
            
            @if($role === 'directeur')
                <!-- DIRECTEUR NAVIGATION -->
                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Vue d'ensemble</div>
                
                <a href="{{ route('directeur.dashboard') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'directeur.dashboard' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-base {{ $currentRoute === 'directeur.dashboard' ? 'text-white' : 'text-blue-400' }}"></i>
                    <span>Tableau de bord</span>
                </a>

                <div class="pt-3 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Gestion Scolaire</div>

                <a href="{{ route('directeur.eleves.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ str_starts_with($currentRoute, 'directeur.eleves') && !str_contains($currentRoute, 'cotes') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-user-graduate w-5 text-center text-base text-amber-400"></i>
                    <span>Élèves & Inscriptions</span>
                </a>

                <a href="{{ route('directeur.classes.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ str_starts_with($currentRoute, 'directeur.classes') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-school w-5 text-center text-base text-teal-400"></i>
                    <span>Classes</span>
                </a>

                <a href="{{ route('options.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ str_starts_with($currentRoute, 'options') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-layer-group w-5 text-center text-base text-cyan-400"></i>
                    <span>Options & Filières</span>
                </a>

                <a href="{{ route('directeur.cours.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ str_starts_with($currentRoute, 'directeur.cours') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-book-bookmark w-5 text-center text-base text-indigo-400"></i>
                    <span>Cours & Matières</span>
                </a>

                <div class="pt-3 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Pédagogie & Cotes</div>

                <a href="{{ route('directeur.enseignants') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ str_starts_with($currentRoute, 'directeur.enseignants') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-chalkboard-user w-5 text-center text-base text-purple-400"></i>
                    <span>Corps Enseignant</span>
                </a>

                <a href="{{ route('directeur.enseignants.supervision') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'directeur.enseignants.supervision' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-pen-ruler w-5 text-center text-base text-pink-400"></i>
                    <span>Supervision des Cotes</span>
                </a>

                <div class="pt-3 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Administration</div>

                <a href="{{ route('annees.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ str_starts_with($currentRoute, 'annees') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-calendar-check w-5 text-center text-base text-emerald-400"></i>
                    <span>Années Scolaires</span>
                </a>

                <a href="{{ route('users.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ str_starts_with($currentRoute, 'users') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-users-gear w-5 text-center text-base text-rose-400"></i>
                    <span>Utilisateurs & Accès</span>
                </a>

            @elseif($role === 'comptable')
                <!-- COMPTABLE NAVIGATION -->
                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Finance & Trésorerie</div>

                <a href="{{ route('comptable.dashboard') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'comptable.dashboard' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line w-5 text-center text-base text-emerald-400"></i>
                    <span>Tableau de bord</span>
                </a>

                <a href="{{ route('comptable.paiements.create') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'comptable.paiements.create' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-cash-register w-5 text-center text-base text-teal-400"></i>
                    <span>Encaisser un paiement</span>
                </a>

                <a href="{{ route('comptable.paiements.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'comptable.paiements.index' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-receipt w-5 text-center text-base text-amber-400"></i>
                    <span>Journal des paiements</span>
                </a>

                <a href="{{ route('comptable.frais.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ str_starts_with($currentRoute, 'comptable.frais') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-base text-blue-400"></i>
                    <span>Grille des Frais</span>
                </a>

                <a href="{{ route('comptable.rappels.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ str_starts_with($currentRoute, 'comptable.rappels') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-bell w-5 text-center text-base text-rose-400"></i>
                    <span>Rappels automatiques</span>
                </a>

            @elseif($role === 'enseignant')
                <!-- ENSEIGNANT NAVIGATION -->
                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Pédagogie</div>

                <a href="{{ route('enseignant.dashboard') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'enseignant.dashboard' ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-chalkboard-user w-5 text-center text-base text-purple-400"></i>
                    <span>Tableau de bord</span>
                </a>

                <a href="{{ route('enseignant.profil') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'enseignant.profil' ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-id-card w-5 text-center text-base text-indigo-400"></i>
                    <span>Mon Profil Enseignant</span>
                </a>

            @elseif($role === 'eleve')
                <!-- ELEVE NAVIGATION -->
                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">Mon Parcours</div>

                <a href="{{ route('eleve.dashboard') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'eleve.dashboard' ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line w-5 text-center text-base text-cyan-400"></i>
                    <span>Tableau de bord</span>
                </a>

                <a href="{{ route('eleve.notes') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'eleve.notes' ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-list-check w-5 text-center text-base text-blue-400"></i>
                    <span>Mes Notes & Cotes</span>
                </a>

                <a href="{{ route('eleve.bulletins') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'eleve.bulletins' ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-award w-5 text-center text-base text-amber-400"></i>
                    <span>Mes Bulletins</span>
                </a>

                <a href="{{ route('eleve.finances') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'eleve.finances' ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-wallet w-5 text-center text-base text-emerald-400"></i>
                    <span>Mes Frais & Paiements</span>
                </a>

                <a href="{{ route('eleve.profil') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 {{ $currentRoute === 'eleve.profil' ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-600/30' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <i class="fa-solid fa-user-circle w-5 text-center text-base text-teal-400"></i>
                    <span>Mon Profil</span>
                </a>
            @endif

        </nav>
    </div>

    <!-- Footer Sidebar : Profil utilisateur & Déconnexion -->
    <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
        <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-slate-900/80 border border-slate-800">
            <div class="flex items-center space-x-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-300 font-bold text-xs uppercase border border-slate-700">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ $user->name ?? 'Utilisateur' }}</p>
                    <p class="text-[10px] font-medium text-slate-400 capitalize flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full {{ 
                            match($role) {
                                'directeur' => 'bg-blue-400',
                                'comptable' => 'bg-emerald-400',
                                'enseignant' => 'bg-purple-400',
                                'eleve' => 'bg-cyan-400',
                                default => 'bg-slate-400'
                            }
                        }}"></span>
                        {{ $role }}
                    </p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" title="Déconnexion" class="p-2 text-red-400 hover:text-white hover:bg-red-500/20 rounded-lg transition-colors cursor-pointer">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </button>
            </form>
        </div>
    </div>

</aside>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm hidden md:hidden"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('main-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>
