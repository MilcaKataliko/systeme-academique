<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin scolaire — {{ $inscription->eleve->code_matricule }}</title>
    <style>
        :root { --blue: #003f7d; --red: #ce1126; --gold: #f7d117; --ink: #17212b; --line: #7b8794; }
        * { box-sizing: border-box; } body { margin: 0; background: #e8edf2; color: var(--ink); font-family: Arial, Helvetica, sans-serif; }
        .toolbar { max-width: 1120px; margin: 18px auto; display: flex; justify-content: space-between; gap: 12px; }
        .button { border: 0; border-radius: 6px; padding: 11px 16px; color: #fff; background: var(--blue); font-weight: 700; cursor: pointer; text-decoration: none; font-size: 14px; }.button.secondary { background: #4b5563; }
        .sheet { width: 297mm; min-height: 210mm; margin: 0 auto 25px; padding: 10mm 12mm; background: #fff; box-shadow: 0 3px 20px #64748b55; position: relative; }
        .top-rule { height: 7px; background: linear-gradient(90deg, #007fff 0 33.33%, var(--red) 33.33% 66.66%, var(--gold) 66.66%); margin: -10mm -12mm 7mm; }
        .republic-header { display: grid; grid-template-columns: 1fr 110px 1fr; gap: 12px; align-items: center; text-align: center; }.republic-header p { margin: 0; font-size: 10px; line-height: 1.35; font-weight: 700; text-transform: uppercase; }.republic-header .school { text-transform: none; font-size: 11px; }
        .seal { height: 82px; width: 82px; margin: auto; border: 2px solid var(--blue); outline: 2px solid var(--gold); outline-offset: 3px; border-radius: 50%; display: grid; place-items: center; color: var(--blue); font-size: 9px; font-weight: 800; line-height: 1.15; }
        .document-title { text-align: center; margin: 13px 0 10px; border-top: 2px solid var(--blue); border-bottom: 2px solid var(--blue); padding: 7px; }.document-title h1 { margin: 0; font-size: 20px; letter-spacing: 1.2px; color: var(--blue); }.document-title p { margin: 3px 0 0; font-size: 11px; font-weight: 700; }
        .notice { border: 1px solid #d89700; background: #fff8dc; color: #694d00; text-align: center; padding: 5px; font-size: 10px; font-weight: 700; margin-bottom: 8px; }
        .student-box { border: 1px solid var(--line); display: grid; grid-template-columns: 1.25fr 1fr 1fr; margin-bottom: 9px; }.student-box div { padding: 6px 8px; border-right: 1px solid #c7ced6; min-height: 40px; }.student-box div:nth-child(3n) { border-right: 0; }.student-box div:nth-child(n+4) { border-top: 1px solid #c7ced6; }.label { display: block; color: #4b5563; font-size: 8px; font-weight: 800; text-transform: uppercase; margin-bottom: 3px; }.value { font-size: 11px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; font-size: 9px; } th { background: var(--blue); color: #fff; border: 1px solid var(--blue); padding: 5px 4px; text-align: center; text-transform: uppercase; font-size: 8px; } td { border: 1px solid #9ba6b2; padding: 5px 4px; vertical-align: middle; } td.course { font-weight: 700; width: 20%; } td.coef, td.average { text-align: center; width: 7%; font-weight: 700; } td.notes { width: 56%; }.note { display: inline-block; margin: 1px 2px 1px 0; padding: 2px 3px; border: 1px solid #c7ced6; border-radius: 2px; white-space: nowrap; }.empty { color: #6b7280; font-style: italic; }
        .summary { display: grid; grid-template-columns: 1.2fr .8fr; gap: 10px; margin-top: 10px; }.summary-box { border: 1px solid var(--line); padding: 8px; min-height: 68px; }.summary-box h2 { font-size: 10px; color: var(--blue); text-transform: uppercase; margin: 0 0 5px; }.summary-box p { margin: 4px 0; font-size: 10px; }.general-average { text-align: center; background: #edf5fc; }.general-average strong { display: block; font-size: 23px; color: var(--blue); margin: 6px 0; }
        .signatures { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 15px; text-align: center; font-size: 10px; }.signature-line { border-top: 1px solid #1f2937; margin: 42px 10px 0; padding-top: 4px; }.footer { position: absolute; bottom: 7mm; left: 12mm; right: 12mm; border-top: 1px solid #9ba6b2; padding-top: 4px; font-size: 8px; color: #4b5563; display: flex; justify-content: space-between; }
        @page { size: A4 landscape; margin: 0; } @media print { body { background: #fff; }.no-print { display: none !important; }.sheet { margin: 0; width: 297mm; min-height: 210mm; box-shadow: none; page-break-after: avoid; } } @media screen and (max-width: 1100px) { .sheet { width: 100%; min-height: auto; } }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <a class="button secondary" href="{{ isset($inscription) ? route('directeur.eleves.show', $inscription->eleve_id) : (isset($inscriptions) && $inscriptions->count() ? route('directeur.eleves.show', $inscriptions->first()->eleve_id) : '#') }}">← Retour à l’élève</a>
        <button class="button" onclick="printCurrent()">Imprimer le bulletin</button>
        <button class="button" onclick="window.print()">Imprimer tous les bulletins</button>
        <a class="button" href="{{ request()->fullUrlWithQuery(['format' => 'pdf']) }}">Télécharger PDF</a>
    </div>

    <script>
        function printCurrent(){
            const sheets = document.querySelectorAll('.sheet');
            if(!sheets || sheets.length <= 1){ window.print(); return; }
            sheets.forEach((s,i)=> { if(i!==0) s.style.display='none'; });
            window.print();
            sheets.forEach(s=> s.style.display='block');
        }
    </script>
    @if(isset($inscriptions) && $inscriptions->count())
        @foreach($inscriptions as $inscription)
            @php
                $plansLocal = $plansParInscription[$inscription->id] ?? $plans;
                $resultatsParPlanLocal = $resultatsParPlanParInscription[$inscription->id] ?? $resultatsParPlan;
                $detailsNotesParPlanLocal = $detailsNotesParPlanParInscription[$inscription->id] ?? $detailsNotesParPlan;
                $resumeBulletinLocal = $resumeBulletinParInscription[$inscription->id] ?? $resumeBulletin;
                $nbMatieresValideesLocal = $nbMatieresValideesParInscription[$inscription->id] ?? $nbMatieresValidees;
                $bulletinDefinitifLocal = $bulletinDefinitifParInscription[$inscription->id] ?? $bulletinDefinitif;
            @endphp

            <main class="sheet" style="page-break-after: always;">
                <div class="top-rule"></div>
                <header class="republic-header">
                    <div><p>République Démocratique du Congo</p><p>Ministère de l’Éducation Nationale</p><p>et Nouvelle Citoyenneté</p><p style="margin-top:5px">Province éducationnelle : {{ $inscription->ecole->province_educationnelle }}</p></div>
                    <div class="seal">RDC<br>ÉDUCATION<br>NATIONALE</div>
                    <div><p class="school">{{ $inscription->ecole->nom_ecole }}</p><p style="margin-top:5px">Code : {{ $inscription->ecole->code_national_epst }}</p><p class="school" style="margin-top:5px">{{ $inscription->ecole->adresse }}</p></div>
                </header>
                <div class="document-title"><h1>BULLETIN SCOLAIRE</h1><p>Année scolaire {{ $inscription->annee_scolaire }} — Résultats académiques</p></div>
                @if(! $bulletinDefinitifLocal)<div class="notice">BULLETIN PROVISOIRE — {{ $nbMatieresValideesLocal }}/{{ $plansLocal->count() }} matière(s) validée(s). Les notes restent visibles et sont mises à jour automatiquement.</div>@endif
                <section class="student-box">
                    <div><span class="label">Nom complet de l’élève</span><span class="value">{{ $inscription->eleve->nom }} {{ $inscription->eleve->postnom }} {{ $inscription->eleve->prenom }}</span></div><div><span class="label">Matricule</span><span class="value">{{ $inscription->eleve->code_matricule }}</span></div><div><span class="label">Sexe</span><span class="value">{{ $inscription->eleve->genre === 'M' ? 'Masculin' : 'Féminin' }}</span></div>
                    <div><span class="label">Classe / Option</span><span class="value">{{ $inscription->classe->nom_classe }}{{ $inscription->classe->option ? ' — ' . $inscription->classe->option->nomoption : '' }}</span></div><div><span class="label">Année scolaire</span><span class="value">{{ $inscription->annee_scolaire }}</span></div><div><span class="label">Statut du bulletin</span><span class="value">{{ $bulletinDefinitifLocal ? 'Définitif' : 'Provisoire' }}</span></div>
                </section>
                <table><thead><tr><th>Matières</th><th>Coef.</th><th>Détails des cotes obtenues</th><th>Moyenne /20</th></tr></thead><tbody>
                    @forelse($plansLocal as $plan)
                        @php($moyenne = $resultatsParPlanLocal->get($plan->id))
                        <tr><td class="course">{{ $plan->cours->nom_cours }}</td><td class="coef">{{ $plan->coefficient }}</td><td class="notes">@forelse($detailsNotesParPlanLocal->get($plan->id, collect()) as $detail)<span class="note">{{ $detail['libelle'] }} : <strong>{{ $detail['note'] }}/{{ $detail['maximum'] }}</strong></span>@empty <span class="empty">Aucune cote encodée</span> @endforelse</td><td class="average">{{ $moyenne !== null ? number_format($moyenne, 2) : '—' }}</td></tr>
                    @empty <tr><td colspan="4" class="empty" style="text-align:center">Aucune matière attribuée à cette classe.</td></tr>
                    @endforelse
                </tbody></table>
                <section class="summary">
                    <div class="summary-box"><h2>Appréciation du conseil des enseignants</h2>@if($resumeBulletinLocal['moyenne'] !== null) @php($mention = $resumeBulletinLocal['moyenne'] >= 16 ? 'Très bien' : ($resumeBulletinLocal['moyenne'] >= 14 ? 'Bien' : ($resumeBulletinLocal['moyenne'] >= 12 ? 'Assez bien' : ($resumeBulletinLocal['moyenne'] >= 10 ? 'Satisfaction' : 'A améliorer'))))<p><strong>Mention :</strong> {{ $mention }}</p>@endif</div>
                    <div class="summary-box general-average"><h2>Moyenne générale pondérée</h2><strong>{{ $resumeBulletinLocal['moyenne'] !== null ? number_format($resumeBulletinLocal['moyenne'], 2) . ' / 20' : '—' }}</strong><p>Somme des coefficients : {{ $resumeBulletinLocal['total_coefficients'] }}</p></div>
                </section>
                <section class="signatures"><div><strong>Le Titulaire</strong><div class="signature-line">Nom et signature</div></div><div><strong>Les Parents / Tuteur</strong><div class="signature-line">Signature</div></div><div><strong>Le Chef d’établissement</strong><div class="signature-line">Nom, signature et cachet</div></div></section>
                <footer class="footer"><span>Document généré automatiquement le {{ now()->format('d/m/Y') }}</span><span>{{ $inscription->ecole->nom_ecole }} — {{ $inscription->eleve->code_matricule }}</span></footer>
            </main>
        @endforeach
    @else
        @php
            $plansLocal = $plans;
            $resultatsParPlanLocal = $resultatsParPlan;
            $detailsNotesParPlanLocal = $detailsNotesParPlan;
            $resumeBulletinLocal = $resumeBulletin;
            $nbMatieresValideesLocal = $nbMatieresValidees;
            $bulletinDefinitifLocal = $bulletinDefinitif;
        @endphp

        <main class="sheet">
            <div class="top-rule"></div>
            <header class="republic-header">
                <div><p>République Démocratique du Congo</p><p>Ministère de l’Éducation Nationale</p><p>et Nouvelle Citoyenneté</p><p style="margin-top:5px">Province éducationnelle : {{ $inscription->ecole->province_educationnelle }}</p></div>
                <div class="seal">RDC<br>ÉDUCATION<br>NATIONALE</div>
                <div><p class="school">{{ $inscription->ecole->nom_ecole }}</p><p style="margin-top:5px">Code : {{ $inscription->ecole->code_national_epst }}</p><p class="school" style="margin-top:5px">{{ $inscription->ecole->adresse }}</p></div>
            </header>
            <div class="document-title"><h1>BULLETIN SCOLAIRE</h1><p>Année scolaire {{ $inscription->annee_scolaire }} — Résultats académiques</p></div>
            @if(! $bulletinDefinitifLocal)<div class="notice">BULLETIN PROVISOIRE — {{ $nbMatieresValideesLocal }}/{{ $plansLocal->count() }} matière(s) validée(s). Les notes restent visibles et sont mises à jour automatiquement.</div>@endif
            <section class="student-box">
                <div><span class="label">Nom complet de l’élève</span><span class="value">{{ $inscription->eleve->nom }} {{ $inscription->eleve->postnom }} {{ $inscription->eleve->prenom }}</span></div><div><span class="label">Matricule</span><span class="value">{{ $inscription->eleve->code_matricule }}</span></div><div><span class="label">Sexe</span><span class="value">{{ $inscription->eleve->genre === 'M' ? 'Masculin' : 'Féminin' }}</span></div>
                <div><span class="label">Classe / Option</span><span class="value">{{ $inscription->classe->nom_classe }}{{ $inscription->classe->option ? ' — ' . $inscription->classe->option->nomoption : '' }}</span></div><div><span class="label">Année scolaire</span><span class="value">{{ $inscription->annee_scolaire }}</span></div><div><span class="label">Statut du bulletin</span><span class="value">{{ $bulletinDefinitifLocal ? 'Définitif' : 'Provisoire' }}</span></div>
            </section>
            <table><thead><tr><th>Matières</th><th>Coef.</th><th>Détails des cotes obtenues</th><th>Moyenne /20</th></tr></thead><tbody>
                @forelse($plansLocal as $plan)
                    @php($moyenne = $resultatsParPlanLocal->get($plan->id))
                    <tr><td class="course">{{ $plan->cours->nom_cours }}</td><td class="coef">{{ $plan->coefficient }}</td><td class="notes">@forelse($detailsNotesParPlanLocal->get($plan->id, collect()) as $detail)<span class="note">{{ $detail['libelle'] }} : <strong>{{ $detail['note'] }}/{{ $detail['maximum'] }}</strong></span>@empty <span class="empty">Aucune cote encodée</span> @endforelse</td><td class="average">{{ $moyenne !== null ? number_format($moyenne, 2) : '—' }}</td></tr>
                @empty <tr><td colspan="4" class="empty" style="text-align:center">Aucune matière attribuée à cette classe.</td></tr>
                @endforelse
            </tbody></table>
            <section class="summary">
                <div class="summary-box"><h2>Appréciation du conseil des enseignants</h2>@if($resumeBulletinLocal['moyenne'] !== null) @php($mention = $resumeBulletinLocal['moyenne'] >= 16 ? 'Très bien' : ($resumeBulletinLocal['moyenne'] >= 14 ? 'Bien' : ($resumeBulletinLocal['moyenne'] >= 12 ? 'Assez bien' : ($resumeBulletinLocal['moyenne'] >= 10 ? 'Satisfaction' : 'A améliorer'))))<p><strong>Mention :</strong> {{ $mention }}</p>@else <p class="empty">Résultats insuffisants pour une appréciation.</p>@endif</div>
                <div class="summary-box general-average"><h2>Moyenne générale pondérée</h2><strong>{{ $resumeBulletinLocal['moyenne'] !== null ? number_format($resumeBulletinLocal['moyenne'], 2) . ' / 20' : '—' }}</strong><p>Somme des coefficients : {{ $resumeBulletinLocal['total_coefficients'] }}</p></div>
            </section>
            <section class="signatures"><div><strong>Le Titulaire</strong><div class="signature-line">Nom et signature</div></div><div><strong>Les Parents / Tuteur</strong><div class="signature-line">Signature</div></div><div><strong>Le Chef d’établissement</strong><div class="signature-line">Nom, signature et cachet</div></div></section>
            <footer class="footer"><span>Document généré automatiquement le {{ now()->format('d/m/Y') }}</span><span>{{ $inscription->ecole->nom_ecole }} — {{ $inscription->eleve->code_matricule }}</span></footer>
        </main>
    @endif

</body>
</html>
