@php
    $user = Auth::user();
    $role = $user->role ?? 'directeur';
    $schoolName = $user->ecole->nom ?? 'Système Académique';
@endphp

<!-- Topbar Header -->
<header class="bg-slate-950/80 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-30 px-4 sm:px-6 py-3 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <!-- Toggle Button pour mobile -->
        <button onclick="toggleSidebar()" class="md:hidden text-slate-300 hover:text-white p-2 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-800 transition">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        
        <div class="flex items-center space-x-2">
            <span class="hidden sm:inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <h2 class="text-sm font-semibold text-slate-200">
                @yield('page_title', 'Tableau de bord')
            </h2>
        </div>
    </div>

    <!-- Actions & Info utilisateur -->
    <div class="flex items-center space-x-3">
        <div class="hidden sm:flex items-center space-x-2 text-xs text-slate-400 bg-slate-900/90 border border-slate-800 px-3 py-1.5 rounded-full">
            <i class="fa-solid fa-school text-blue-400"></i>
            <span class="font-medium text-slate-300">{{ $schoolName }}</span>
        </div>

        <div class="flex items-center space-x-2 text-xs bg-slate-900/90 border border-slate-800 px-3 py-1.5 rounded-full">
            <i class="fa-solid fa-circle-user {{ 
                match($role) {
                    'directeur' => 'text-blue-400',
                    'comptable' => 'text-emerald-400',
                    'enseignant' => 'text-purple-400',
                    'eleve' => 'text-cyan-400',
                    default => 'text-slate-400'
                }
            }}"></i>
            <span class="font-semibold text-slate-200 capitalize">{{ $user->name }}</span>
            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700">
                {{ $role }}
            </span>
        </div>
    </div>
</header>
