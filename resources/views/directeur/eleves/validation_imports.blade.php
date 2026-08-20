<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation des imports</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-900 text-slate-100">
<main class="max-w-6xl mx-auto p-6 space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div><h1 class="text-2xl font-black">Validation manuelle des imports</h1><p class="text-sm text-slate-400 mt-1">Corrigez les notes manquantes ou hors barème avant leur intégration au bulletin.</p></div>
        <a href="{{ route('directeur.eleves.index') }}" class="text-sm text-slate-400 hover:text-white"><i class="fa-solid fa-arrow-left mr-2"></i>Élèves</a>
    </div>
    @if(session('success'))<div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-300">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 text-red-300">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 text-red-300">{{ $errors->first() }}</div>@endif
    <div class="rounded-2xl border border-slate-800 bg-slate-950 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-800 font-bold"><i class="fa-solid fa-triangle-exclamation text-amber-400 mr-2"></i>{{ $anomalies->count() }} anomalie(s) en attente</div>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-slate-900 text-slate-400 text-xs uppercase"><tr><th class="p-3 text-left">Ligne</th><th class="p-3 text-left">Élève</th><th class="p-3 text-left">Cours / évaluation</th><th class="p-3 text-left">Anomalie</th><th class="p-3 text-left">Correction</th></tr></thead><tbody>
        @forelse($anomalies as $anomalie)
            <tr class="border-t border-slate-800"><td class="p-3 font-mono text-slate-400">{{ $anomalie->ligne_source ?? '—' }}</td><td class="p-3">{{ $anomalie->matricule }}</td><td class="p-3"><span class="font-semibold">{{ $anomalie->code_cours }}</span><br><span class="text-xs text-slate-400">{{ $anomalie->champ }}</span></td><td class="p-3 text-amber-300">{{ $anomalie->motif }}</td><td class="p-3"><form method="POST" action="{{ route('directeur.bulletin.validation.corriger', $anomalie) }}" class="flex gap-2">@csrf @method('PUT')<input type="number" step="0.01" min="0" name="note" value="{{ $anomalie->note }}" placeholder="Note" class="w-24 rounded-lg border border-slate-700 bg-slate-900 p-2"><button class="rounded-lg bg-emerald-600 px-3 py-2 font-bold hover:bg-emerald-500">Valider</button></form></td></tr>
        @empty<tr><td colspan="5" class="p-12 text-center text-slate-500"><i class="fa-solid fa-circle-check text-3xl text-emerald-400 block mb-3"></i>Aucune anomalie à corriger.</td></tr>@endforelse
        </tbody></table></div>
    </div>
</main></body></html>
