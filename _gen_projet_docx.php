<?php
/**
 * Générateur de document Word (.docx) — Documentation du projet tutoré L3
 * Construit un fichier .docx valide à partir de la structure OOXML.
 */

// --- Vérification de l'extension Zip ---
if (!class_exists('ZipArchive')) {
    fwrite(STDERR, "ERREUR : L'extension PHP 'zip' n'est pas active. Activez zip dans php.ini.\n");
    exit(1);
}

$outDir = __DIR__;
$outFile = $outDir . '/Documentation_Projet_Tutore_L3.docx';

// --- Échappement XML ---
function esc($s) {
    return htmlspecialchars((string)$s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

// --- Gestionnaires de contenu Word ---
$body = '';

function para($text, $bold = false, $size = 22, $align = 'left', $color = '000000', $italic = false) {
    $alignMap = ['left' => 'left', 'center' => 'center', 'right' => 'right', 'both' => 'both'];
    $a = $alignMap[$align] ?? 'left';
    $b = $bold ? '<w:b/>' : '';
    $i = $italic ? '<w:i/>' : '';
    return '<w:p><w:pPr><w:jc w:val="' . $a . '"/><w:spacing w:after="80" w:line="276" w:lineRule="auto"/></w:pPr>' .
           '<w:r><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:sz w:val="' . $size . '"/><w:szCs w:val="' . $size . '"/><w:color w:val="' . $color . '"/>' . $b . $i . '</w:rPr>' .
           '<w:t xml:space="preserve">' . esc($text) . '</w:t></w:r></w:p>';
}

function heading1($text) {
    return '<w:p><w:pPr><w:spacing w:before="240" w:after="160"/><w:outlineLvl w:val="0"/></w:pPr>' .
           '<w:r><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:b/><w:sz w:val="32"/><w:szCs w:val="32"/><w:color w:val="1F3864"/></w:rPr>' .
           '<w:t xml:space="preserve">' . esc($text) . '</w:t></w:r></w:p>';
}

function heading2($text) {
    return '<w:p><w:pPr><w:spacing w:before="180" w:after="120"/><w:outlineLvl w:val="1"/></w:pPr>' .
           '<w:r><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:b/><w:sz w:val="28"/><w:szCs w:val="28"/><w:color w:val="2E5395"/></w:rPr>' .
           '<w:t xml:space="preserve">' . esc($text) . '</w:t></w:r></w:p>';
}

function heading3($text) {
    return '<w:p><w:pPr><w:spacing w:before="120" w:after="80"/><w:outlineLvl w:val="2"/></w:pPr>' .
           '<w:r><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:b/><w:sz w:val="24"/><w:szCs w:val="24"/><w:color w:val="404040"/></w:rPr>' .
           '<w:t xml:space="preserve">' . esc($text) . '</w:t></w:r></w:p>';
}

function bullet($text, $boldPrefix = '') {
    $prefix = $boldPrefix !== '' ? '<w:r><w:rPr><w:b/><w:sz w:val="22"/></w:rPr><w:t xml:space="preserve">' . esc($boldPrefix) . '</w:t></w:r>' : '';
    return '<w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr><w:spacing w:after="40" w:line="276" w:lineRule="auto"/></w:pPr>' .
           $prefix .
           '<w:r><w:rPr><w:sz w:val="22"/></w:rPr><w:t xml:space="preserve">' . esc($text) . '</w:t></w:r></w:p>';
}

function tableRow($cells, $header = false) {
    $shd = $header ? '<w:shd w:val="clear" w:color="auto" w:fill="1F3864"/>' : '';
    $bold = $header ? '<w:b/>' : '';
    $color = $header ? '<w:color w:val="FFFFFF"/>' : '';
    $r = '<w:tr>';
    foreach ($cells as $c) {
        $r .= '<w:tc><w:tcPr><w:tcW w:w="3000" w:type="dxa"/>' . $shd . '<w:vAlign w:val="center"/></w:tcPr>' .
              '<w:p><w:pPr><w:spacing w:after="20"/></w:pPr>' .
              '<w:r><w:rPr><w:sz w:val="20"/>' . $bold . $color . '</w:rPr><w:t xml:space="preserve">' . esc($c) . '</w:t></w:r></w:p></w:tc>';
    }
    $r .= '</w:tr>';
    return $r;
}

function table($rows) {
    $r = '<w:tbl><w:tblPr><w:tblW w:w="0" w:type="auto"/><w:tblBorders>' .
         '<w:top w:val="single" w:sz="4" w:color="999999"/><w:left w:val="single" w:sz="4" w:color="999999"/>' .
         '<w:bottom w:val="single" w:sz="4" w:color="999999"/><w:right w:val="single" w:sz="4" w:color="999999"/>' .
         '<w:insideH w:val="single" w:sz="4" w:color="999999"/><w:insideV w:val="single" w:sz="4" w:color="999999"/>' .
         '</w:tblBorders><w:tblCellMar><w:left w:w="80" w:type="dxa"/><w:right w:w="80" w:type="dxa"/></w:tblCellMar></w:tblPr>';
    foreach ($rows as $i => $row) {
        $r .= tableRow($row, $i === 0);
    }
    $r .= '</w:tbl>';
    return $r;
}

function spacer() {
    return '<w:p><w:pPr><w:spacing w:after="0"/></w:pPr></w:p>';
}

// ============================================================
// CONTENU DU DOCUMENT
// ============================================================

// --- Page de garde ---
$body .= spacer();
$body .= para('RÉPUBLIQUE DÉMOCRATIQUE DU CONGO', true, 28, 'center');
$body .= spacer();
$body .= spacer();
$body .= para('DOCUMENTATION DU PROJET TUTORÉ', true, 40, 'center', '1F3864');
$body .= para('LICENCE 3 — SCIENCES INFORMATIQUES', true, 32, 'center', '2E5395');
$body .= para('Année académique 2024–2025 — Classes montantes', false, 24, 'center');
$body .= spacer();
$body .= spacer();
$body .= para('TITRE DU PROJET', true, 26, 'center');
$body .= para('Système académique intelligent pour les écoles secondaires : inscription en ligne, gestion des notes, paiements, tableaux de bord pour la direction.', true, 26, 'center');
$body .= spacer();
$body .= spacer();
$body .= spacer();
$body .= para('Encadreur / Promoteur : Dr Rodrigue KALUMENDO', false, 24, 'center');
$body .= spacingBreak();
$body .= '<w:br w:type="page"/>';

function spacingBreak() { return '<w:p><w:r><w:br w:type="page"/></w:r></w:p>'; }

// --- TABLE DES MATIÈRES ---
$body .= heading1('TABLE DES MATIÈRES');
$body .= para('1. Titre du projet', false, 24);
$body .= para('2. Contexte et justification', false, 24);
$body .= para('3. Problématique', false, 24);
$body .= para('4. Objectifs du projet', false, 24);
$body .= para('5. Méthodologie de réalisation', false, 24);
$body .= para('6. Analyse des besoins', false, 24);
$body .= para('7. Conception du système — méthode UML (L3)', false, 24);
$body .= para('8. Planification temporelle', false, 24);
$body .= para('9. Ressources matérielles et logicielles', false, 24);
$body .= para('10. Résultats attendus', false, 24);
$body .= para('11. Limites et perspectives', false, 24);
$body .= para('12. Annexes', false, 24);
$body .= para('13. Références au code du projet', false, 24);
$body .= spacingBreak();

// --- 1. TITRE ---
$body .= heading1('1. TITRE DU PROJET');
$body .= para('Système académique intelligent pour les écoles secondaires : inscription en ligne, gestion des notes, paiements, tableaux de bord pour la direction.', true, 24);
$body .= heading3('Titre court (soutenance)');
$body .= para('Plateforme web de gestion académique intégrée pour établissements d\'enseignement secondaire.', false, 24, 'left', '000000', true);
$body .= heading3('Domaine d\'application');
$body .= para('Gestion scolaire / Administration académique / Suivi des performances et de la scolarité.');

// --- 2. CONTEXTE ---
$body .= heading1('2. CONTEXTE ET JUSTIFICATION');
$body .= heading2('2.1 Contexte technologique et social');
$body .= para('Dans la plupart des écoles secondaires en République Démocratique du Congo (notamment dans les villes de l\'Est comme Butembo, Beni et Goma), la gestion académique repose encore sur des méthodes manuelles : registres papier, cahiers de cotes, calculs à la main et fichiers éparpillés. Ces pratiques engendrent plusieurs problèmes :');
$body .= bullet('Perte et altération des archives (bulletins, registres matricules, historiques de paiement) ;');
$body .= bullet('Lenteur dans la production des bulletins et des relevés de notes ;');
$body .= bullet('Erreurs de calcul des moyennes et des pourcentages ;');
$body .= bullet('Difficulté de suivi des frais scolaires (minerval, frais de participation) et des soldes par élève ;');
$body .= bullet('Manque de visibilité pour la direction sur les performances globales de l\'établissement ;');
$body .= bullet('Communication difficile avec les parents (retards de paiement non signalés, résultats non accessibles).');
$body .= heading2('2.2 Intérêt du projet');
$body .= para('Ce projet répond à un besoin réel et urgent de digitalisation. Il permet de :');
$body .= bullet('Centraliser toutes les données académiques et financières d\'une école dans une base de données unique et sécurisée ;');
$body .= bullet('Automatiser le calcul des cotes, des moyennes, des pourcentages et des bulletins ;');
$body .= bullet('Sécuriser les informations par authentification et cloisonnement multi-écoles (chaque école ne voit que ses données) ;');
$body .= bullet('Simplifier le travail des enseignants (encodage des cotes et présences), du comptable (frais, paiements, rappels) et de la direction (tableaux de bord, supervision) ;');
$body .= bullet('Offrir aux élèves un espace personnel de consultation des notes, bulletins et de la situation financière.');
$body .= heading2('2.3 Lien avec les compétences informatiques mobilisées');
$body .= table([
    ['Domaine', 'Compétences mises en œuvre'],
    ['Analyse et conception', 'Recueil des besoins, diagrammes UML (cas d\'utilisation, classes, séquence, activités, états), architecture MVC'],
    ['Développement', 'Programmation en PHP (Laravel 12), SQL, Blade, HTML/CSS (Tailwind), JavaScript'],
    ['Base de données', 'Modélisation relationnelle, migrations, requêtes Eloquent, MySQL/SQLite'],
    ['Génie logiciel', 'Gestion de versions (Git/GitHub), tests, documentation, méthodologie en cascade/cycle en V'],
    ['Sécurité', 'Authentification, contrôle d\'accès par rôle, validation des données, protection CSRF'],
    ['Gestion de projet', 'Planification, répartition des tâches, suivi, communication'],
]);

// --- 3. PROBLÉMATIQUE ---
$body .= heading1('3. PROBLÉMATIQUE');
$body .= para('La problématique centrale qui guide ce projet est formulée comme suit :');
$body .= para('« Comment concevoir et développer une plateforme web sécurisée, fiable et adaptée aux réalités des écoles secondaires congolaises, permettant la gestion intégrée des inscriptions en ligne, des notes et cotes, des présences, des paiements des frais scolaires ainsi que la production automatique des bulletins et des tableaux de bord pour la direction ? »', false, 24, 'both', '000000', true);
$body .= para('Cette question principale se décline en sous-questions :');
$body .= bullet('Comment modéliser les entités académiques (élèves, classes, cours, enseignants, inscriptions, cotes) de manière cohérente et extensible ?');
$body .= bullet('Comment garantir la sécurité des données et le cloisonnement entre plusieurs écoles ?');
$body .= bullet('Comment automatiser le calcul des moyennes, des pourcentages et l\'édition des bulletins ?');
$body .= bullet('Comment assurer le suivi financier (frais, paiements, soldes) et les rappels automatiques aux parents ?');
$body .= bullet('Comment offrir des tableaux de bord pertinents aux différents acteurs (direction, enseignant, comptable, élève) ?');

// --- 4. OBJECTIFS ---
$body .= heading1('4. OBJECTIFS DU PROJET');
$body .= heading2('4.1 Objectif général');
$body .= para('Développer une solution informatique web complète — le Système académique intelligent — répondant au besoin de digitalisation de la gestion académique et financière des écoles secondaires.');
$body .= heading2('4.2 Objectifs spécifiques');
$body .= bullet('Identifier les besoins fonctionnels et techniques des utilisateurs (direction, enseignants, comptables, élèves, parents) ;', 'OS1 : ');
$body .= bullet('Concevoir l\'architecture du système selon les normes méthodologiques UML (diagrammes de cas d\'utilisation, de classes, de séquence, d\'activités et d\'états) ;', 'OS2 : ');
$body .= bullet('Développer et tester la solution (module d\'authentification, inscription des élèves, gestion des cotes et présences, gestion des frais et paiements, génération des bulletins, rappels automatiques) ;', 'OS3 : ');
$body .= bullet('Gérer les ressources humaines, matérielles et financières du projet (organisation de l\'équipe, planification, suivi) ;', 'OS4 : ');
$body .= bullet('Documenter et présenter le projet (documentation technique, manuel utilisateur, soutenance avec démonstration).', 'OS5 : ');

// --- 5. MÉTHODOLOGIE ---
$body .= heading1('5. MÉTHODOLOGIE DE RÉALISATION');
$body .= heading2('5.1 Modèle de développement choisi');
$body .= para('Le projet suit une approche en cascade combinée au cycle en V, structurée en phases séquentielles avec validation à chaque étape :');
$body .= bullet('1. Analyse des besoins → 4. Tests unitaires et fonctionnels');
$body .= bullet('2. Conception UML → 5. Validation et intégration');
$body .= bullet('3. Développement → 6. Déploiement et documentation');
$body .= heading2('5.2 Outils techniques utilisés');
$body .= table([
    ['Catégorie', 'Outils'],
    ['Langages', 'PHP 8.2, SQL, HTML5, CSS3, JavaScript'],
    ['Framework', 'Laravel 12 (architecture MVC, Eloquent ORM, Blade)'],
    ['Base de données', 'MySQL (production) / SQLite (développement)'],
    ['Interface', 'Tailwind CSS, Font Awesome'],
    ['Conception UML', 'Draw.io, Mermaid'],
    ['IDE', 'VS Code, PhpStorm'],
    ['Gestion de versions', 'Git, GitHub'],
    ['Tests', 'PHPUnit, tests manuels fonctionnels'],
    ['Autres', 'Composer, Artisan CLI, Mailtrap/SMTP pour les emails de rappel'],
]);
$body .= heading2('5.3 Étapes clés de réalisation');
$body .= bullet('Analyse des besoins : entretiens avec les responsables d\'école, cahier des charges, questionnaire ;');
$body .= bullet('Conception UML : diagramme de cas d\'utilisation, diagramme de classes, diagrammes de séquence, diagrammes d\'activités, diagramme d\'états ;');
$body .= bullet('Développement : configuration Laravel, création des migrations et modèles, implémentation des contrôleurs et vues par module ;');
$body .= bullet('Tests : tests unitaires des calculs (moyennes, pourcentages), tests fonctionnels des parcours utilisateurs, correction des anomalies ;');
$body .= bullet('Déploiement : déploiement sur un serveur web, configuration de l\'environnement de production ;');
$body .= bullet('Documentation : rapport technique, manuel utilisateur, présentation finale.');

// --- 6. ANALYSE DES BESOINS ---
$body .= heading1('6. ANALYSE DES BESOINS');
$body .= heading2('6.1 Utilisateurs cibles');
$body .= table([
    ['Acteur', 'Description', 'Besoins principaux'],
    ['Directeur', 'Responsable de l\'établissement', 'Tableau de bord, gestion des options/classes/cours, inscriptions, supervision des cotes, gestion des comptes, rapports'],
    ['Enseignant', 'Personnel enseignant', 'Encodage des cotes (12 évaluations), gestion des présences hebdomadaires, consultation de ses classes/cours'],
    ['Comptable', 'Gestionnaire financier', 'Gestion des frais, enregistrement des paiements, reçus, relevés par élève, rappels de paiement automatiques'],
    ['Élève', 'Apprenant', 'Consultation des notes, des bulletins, de la situation financière et du profil'],
    ['Parent (indirect)', 'Tuteur de l\'élève', 'Réception des rappels de paiement, suivi des résultats'],
]);
$body .= heading2('6.2 Fonctionnalités attendues');
$body .= heading3('Module Authentification et utilisateurs');
$body .= bullet('Inscription de l\'établissement (école + compte directeur) ;');
$body .= bullet('Connexion / déconnexion sécurisée (routage par rôle) ;');
$body .= bullet('Création et gestion des comptes (enseignant, comptable, élève) ;');
$body .= bullet('Réinitialisation du mot de passe, photo de profil.');
$body .= heading3('Module Direction');
$body .= bullet('Tableau de bord avec statistiques (options, classes, cours, enseignants, élèves, utilisateurs) ;');
$body .= bullet('Gestion des options, années scolaires, classes, cours ;');
$body .= bullet('Gestion du corps enseignant et attribution des cours aux classes (plans) ;');
$body .= bullet('Registre des élèves (inscription, matricule automatique, fiche individuelle) ;');
$body .= bullet('Supervision de l\'encodage des cotes par les enseignants (statistiques de réussite) ;');
$body .= bullet('Génération et impression des bulletins de notes.');
$body .= heading3('Module Enseignant');
$body .= bullet('Tableau de bord (cours assignés, nombre d\'élèves, périodes actives) ;');
$body .= bullet('Encodage des cotes par classe et par cours (12 champs d\'évaluation) ;');
$body .= bullet('Saisie des présences hebdomadaires (grille élève × jours, Lun–Ven) ;');
$body .= bullet('Calcul automatique du pourcentage de présence et bonus de présence ;');
$body .= bullet('Consultation de son profil.');
$body .= heading3('Module Comptable');
$body .= bullet('Tableau de bord financier (total encaissé, nombre de paiements, frais) ;');
$body .= bullet('Gestion des frais (intitulé, montant, classe, année scolaire) ;');
$body .= bullet('Enregistrement des paiements avec numéro de reçu unique ;');
$body .= bullet('Relevé de compte par élève (total dû, total payé, solde) ;');
$body .= bullet('Configuration et déclenchement des rappels de paiement automatiques (email/SMS), journal des rappels.');
$body .= heading3('Module Élève');
$body .= bullet('Tableau de bord (moyenne générale, cours suivis, bulletins disponibles, situation financière) ;');
$body .= bullet('Consultation des notes détaillées par cours ;');
$body .= bullet('Consultation des bulletins (périodes clôturées) ;');
$body .= bullet('Consultation de la situation financière et du profil.');
$body .= heading2('6.3 Contraintes');
$body .= heading3('Contraintes techniques');
$body .= bullet('Cloisonnement strict des données par école (ecole_id) ;');
$body .= bullet('Compatibilité navigateur (application web responsive) ;');
$body .= bullet('Disponibilité réduite de la connexion Internet dans certaines zones → interface légère ;');
$body .= bullet('Utilisation de SQLite en développement pour faciliter le déploiement local.');
$body .= heading3('Contraintes ergonomiques');
$body .= bullet('Interface simple, en français, adaptée aux utilisateurs non experts ;');
$body .= bullet('Formulaires clairs avec validation et messages d\'erreur compréhensibles ;');
$body .= bullet('Impression correcte des bulletins.');
$body .= heading3('Contraintes sécuritaires');
$body .= bullet('Authentification obligatoire, mots de passe hachés (bcrypt) ;');
$body .= bullet('Contrôle d\'accès par rôle (directeur, enseignant, comptable, élève) ;');
$body .= bullet('Protection CSRF, validation des entrées, requêtes préparées (Eloquent) ;');
$body .= bullet('Empêcher la suppression du compte du directeur connecté.');

// --- 7. CONCEPTION UML ---
$body .= heading1('7. CONCEPTION DU SYSTÈME — MÉTHODE UML (LICENCE 3)');
$body .= heading2('7.1 Diagramme de cas d\'utilisation');
$body .= para('Les acteurs du système sont : le Directeur, l\'Enseignant, le Comptable et l\'Élève. Chaque acteur s\'authentifie puis accède aux cas d\'utilisation autorisés :');
$body .= table([
    ['Acteur', 'Cas d\'utilisation principaux'],
    ['Directeur', 'S\'authentifier, gérer options/années/classes/cours, gérer le corps enseignant, attribuer les cours, inscrire un élève, superviser les cotes, consulter les bulletins, gérer les comptes'],
    ['Enseignant', 'S\'authentifier, gérer les cotes, saisir les présences, consulter les bulletins'],
    ['Comptable', 'S\'authentifier, gérer les frais, enregistrer les paiements, générer reçus et relevés, configurer les rappels'],
    ['Élève', 'S\'authentifier, consulter ses notes, ses bulletins, sa situation financière'],
]);
$body .= heading2('7.2 Diagramme de classes');
$body .= para('Le diagramme de classes modélise les 16 entités principales du système et leurs associations :');
$body .= table([
    ['Entité', 'Rôle'],
    ['Ecole', 'Établissement ; racine de cloisonnement (ecole_id)'],
    ['User', 'Compte de connexion (rôle : directeur, enseignant, comptable, élève)'],
    ['Enseignant', 'Fiche professionnelle d\'un enseignant'],
    ['Eleve', 'Fiche d\'un élève (nom, matricule, naissance)'],
    ['Option', 'Section/filière d\'études'],
    ['Classe', 'Classe rattachée à une option'],
    ['Cours', 'Matière enseignée'],
    ['Plan', 'Attribution d\'un cours à une classe et un enseignant (maxima)'],
    ['Periode', 'Période d\'évaluation (1re, 2e, examen…)'],
    ['Inscription', 'Lien élève-classe-année'],
    ['Cote', 'Évaluations (12 champs) et pourcentage de présence'],
    ['Presence', 'Présence hebdomadaire par élève et par jour'],
    ['Frais', 'Type de frais scolaire'],
    ['FraisClasse', 'Association frais-classe-montant'],
    ['Paiement', 'Paiement avec numéro de reçu'],
    ['RappelPaiement', 'Historique des rappels de paiement'],
]);
$body .= para('Associations principales : Ecole(1)—(n) User/Enseignant/Eleve/Option/Cours/Periode/Frais ; User(1)—(0..1) Enseignant/Eleve ; Option(1)—(n) Classe ; Classe(1)—(n) Plan/Inscription ; Cours(1)—(n) Plan ; Eleve(1)—(n) Inscription/Presence ; Inscription(1)—(n) Cote/Paiement/RappelPaiement ; Plan(1)—(n) Cote/Presence ; Periode(1)—(n) Cote ; Frais(1)—(n) FraisClasse/Paiement/RappelPaiement ; Classe(1)—(n) FraisClasse.');
$body .= heading2('7.3 Diagramme de séquence — Encodage d\'une cote par l\'enseignant');
$body .= table([
    ['Étape', 'Acteur/Composant', 'Action'],
    ['1', 'Enseignant', 'S\'authentifier (email + mot de passe)'],
    ['2', 'EnseignantController', 'Vérifier les identifiants (Auth::attempt)'],
    ['3', 'Base de données', 'Retourner l\'utilisateur valide (rôle : enseignant)'],
    ['4', 'Système', 'Redirection vers /enseignant/dashboard'],
    ['5', 'Enseignant', 'Choisir une classe et un cours (plan)'],
    ['6', 'EnseignantController', 'Récupérer les inscriptions actives de la classe'],
    ['7', 'Base de données', 'Retourner la liste des élèves'],
    ['8', 'Système', 'Afficher le formulaire d\'encodage des cotes'],
    ['9', 'Enseignant', 'Saisir les notes (champ : période_1, etc.)'],
    ['10', 'EnseignantController', 'Cote::updateOrCreate(inscription_id, plan_id)'],
    ['11', 'Base de données', 'Cote créée/mise à jour'],
    ['12', 'Système', 'Redirection avec message « Notes enregistrées ! »'],
]);
$body .= heading2('7.4 Diagramme de séquence — Enregistrement d\'un paiement (comptable)');
$body .= table([
    ['Étape', 'Acteur/Composant', 'Action'],
    ['1', 'Comptable', 'S\'authentifier puis ouvrir le menu « Enregistrer un paiement »'],
    ['2', 'ComptableController', 'Charger les élèves actifs et les frais'],
    ['3', 'Système', 'Afficher le formulaire de paiement'],
    ['4', 'Comptable', 'Sélectionner élève, frais, montant, mode de paiement'],
    ['5', 'ComptableController', 'Générer un numéro de reçu unique (REC-AAAA-ID-NNNNNN)'],
    ['6', 'ComptableController', 'Paiement::create(...)'],
    ['7', 'Base de données', 'Paiement enregistré'],
    ['8', 'Système', 'Redirection vers la liste des paiements avec message de succès'],
]);
$body .= heading2('7.5 Diagramme d\'activités — Inscription d\'un élève');
$body .= para('Le processus d\'inscription suit les étapes suivantes :');
$body .= table([
    ['Étape', 'Action'],
    ['1', 'Le directeur ouvre le formulaire d\'inscription'],
    ['2', 'Choisir la classe et l\'année scolaire'],
    ['3', 'Saisir les informations de l\'élève (nom, postnom, prénom, genre, naissance)'],
    ['4', 'Si un email est fourni : créer un compte utilisateur élève'],
    ['5', 'Générer le code matricule unique (MT-AAAA-ecoleId-NNNN)'],
    ['6', 'Enregistrer l\'élève dans la table eleves'],
    ['7', 'Créer l\'inscription dans la table inscriptions'],
    ['8', 'En cas de succès : redirection avec message de succès ; sinon : rollback et message d\'erreur'],
]);
$body .= heading2('7.6 Diagramme d\'activités — Génération du bulletin de notes');
$body .= table([
    ['Étape', 'Action'],
    ['1', 'Charger l\'inscription de l\'élève'],
    ['2', 'Charger les périodes de l\'école'],
    ['3', 'Charger les plans (cours) de la classe pour l\'année'],
    ['4', 'Récupérer toutes les cotes de l\'inscription et les grouper par cours/période'],
    ['5', 'Calculer la moyenne par cours puis la moyenne générale'],
    ['6', 'Déterminer la mention (>=16 Très Bien, >=14 Bien, >=12 Assez Bien, >=10 Passable)'],
    ['7', 'Afficher le bulletin à l\'écran et permettre l\'impression'],
]);
$body .= heading2('7.7 Diagramme d\'états — Cycle de vie d\'une inscription');
$body .= table([
    ['État', 'Transition'],
    ['Créée', 'Inscription d\'un élève (statut = actif)'],
    ['Active', 'Validation de l\'inscription'],
    ['En retard', 'Non-paiement des frais scolaires'],
    ['Clôturée', 'Fin de l\'année scolaire'],
    ['Exclue', 'Radiée / abandon'],
]);
$body .= para('Transitions : Créée → Active → En retard → Active (après règlement) ; Active → Clôturée (fin d\'année) ; En retard → Exclue ; Active → Exclue (radiation).');
$body .= heading2('7.8 Diagramme d\'états — État d\'une cote (évaluation)');
$body .= table([
    ['État', 'Transition'],
    ['Non encodée', 'Nouvelle inscription'],
    ['Partielle', 'L\'enseignant saisit une évaluation (ex. période_1)'],
    ['Complète', 'Les 12 évaluations sont renseignées'],
    ['Calculée', 'Calcul automatique du total et du pourcentage'],
    ['Validée', 'Clôture de la période par la direction'],
    ['Publiée', 'Publication du bulletin'],
]);

// --- 8. PLANIFICATION ---
$body .= heading1('8. PLANIFICATION TEMPORELLE');
$body .= table([
    ['Phase', 'Activité principale', 'Durée', 'Période'],
    ['Préparation', 'Analyse des besoins, cahier des charges, questionnaire', '1 semaine', 'Semaine 1'],
    ['Conception', 'Diagrammes UML (cas d\'utilisation, classes, séquence, activités, états)', '2 semaines', 'Semaines 2–3'],
    ['Développement', 'Codage et intégration des modules (auth, direction, enseignant, comptable, élève)', '3 semaines', 'Semaines 4–6'],
    ['Tests', 'Vérification, validation, correction des anomalies', '1 semaine', 'Semaine 7'],
    ['Documentation', 'Rapport technique, manuel utilisateur', '1 semaine', 'Semaine 8'],
    ['Soutenance', 'Présentation finale avec démonstration', '1 semaine', 'Semaine 9'],
]);
$body .= heading2('Décomposition détaillée du développement');
$body .= table([
    ['Semaine', 'Tâches'],
    ['Semaine 4', 'Configuration Laravel, base de données (migrations), authentification et gestion des utilisateurs, gestion des options/classes/cours'],
    ['Semaine 5', 'Module inscriptions (élèves, matricules), module enseignants (attributions, encodage des cotes, présences)'],
    ['Semaine 6', 'Module comptable (frais, paiements, reçus, relevés), rappels automatiques, module élève (notes, bulletins, finances), tableaux de bord, impressions'],
]);

// --- 9. RESSOURCES MATÉRIELLES ET LOGICIELLES ---
$body .= heading1('9. RESSOURCES MATÉRIELLES ET LOGICIELLES');
$body .= heading2('9.1 Ressources matérielles');
$body .= table([
    ['Ressource', 'Utilisation'],
    ['Ordinateur portable (développeur)', 'Développement, tests, rédaction de la documentation'],
    ['Connexion Internet', 'Recherche, documentation, synchronisation Git/GitHub, déploiement'],
    ['Serveur web / hébergement', 'Déploiement de l\'application en ligne'],
    ['Téléphone / tablette', 'Tests de compatibilité mobile de l\'interface'],
]);
$body .= heading2('9.2 Ressources logicielles');
$body .= table([
    ['Catégorie', 'Outils'],
    ['Système d\'exploitation', 'Windows 11, Linux (serveur)'],
    ['IDE', 'VS Code, PhpStorm'],
    ['Outils de conception', 'Draw.io, Figma, Mermaid'],
    ['Gestion de version', 'Git, GitHub'],
    ['Base de données', 'MySQL, SQLite'],
    ['Framework', 'Laravel 12, Tailwind CSS'],
    ['Serveur local', 'XAMPP / WAMP / PHP artisan serve'],
    ['Email / SMS', 'Mailtrap (développement), SMTP de production, passerelle SMS'],
    ['Tests', 'PHPUnit, Postman (tests API éventuels)'],
]);

// --- 10. RÉSULTATS ATTENDUS ---
$body .= heading1('10. RÉSULTATS ATTENDUS');
$body .= bullet('Une solution fonctionnelle : plateforme web complète (authentification, inscription en ligne, gestion des notes, présences, paiements, tableaux de bord, bulletins imprimables) ;');
$body .= bullet('Une documentation technique complète : architecture, modèle de données, diagrammes UML, manuel de déploiement ;');
$body .= bullet('Un manuel utilisateur : guide pour la direction, les enseignants, le comptable et les élèves ;');
$body .= bullet('Une présentation finale avec démonstration : soutenance du projet avec démonstration en direct ;');
$body .= bullet('Des recommandations pour l\'amélioration et le déploiement réel du système ;');
$body .= bullet('Un dépôt Git/GitHub contenant le code source versionné.');

// --- 11. LIMITES ET PERSPECTIVES ---
$body .= heading1('11. LIMITES ET PERSPECTIVES');
$body .= heading2('11.1 Limites du projet');
$body .= table([
    ['Limite', 'Description'],
    ['Accès restreint aux données réelles', 'Difficulté d\'obtenir des données complètes d\'une école réelle pour les tests (anonymisation, disponibilité)'],
    ['Contraintes de temps et de matériel', 'Durée limitée du projet tutoré (9 semaines), matériel de développement limité'],
    ['Niveau technique des utilisateurs finaux', 'Certains utilisateurs ont une faible culture numérique → besoin de formation et d\'interfaces simplifiées'],
    ['Connectivité Internet', 'Zones à faible couverture Internet → limitations du déploiement 100 % en ligne'],
    ['Sécurité', 'Nécessité d\'un durcissement supplémentaire pour une mise en production réelle (HTTPS, sauvegardes, pare-feu)'],
]);
$body .= heading2('11.2 Perspectives');
$body .= table([
    ['Perspective', 'Description'],
    ['Extension géographique', 'Déploiement dans d\'autres villes et provinces de la RDC (Butembo, Beni, Goma, Kinshasa, Lubumbashi…)'],
    ['Modules avancés', 'Intégration de l\'IA pour la prédiction des performances scolaires, détection précoce des élèves en difficulté'],
    ['Sécurité renforcée', 'Authentification à deux facteurs (2FA), journalisation avancée, chiffrement des données sensibles'],
    ['Application mobile', 'Développement d\'une application mobile (React Native) pour les parents et les élèves'],
    ['Notifications temps réel', 'Envoi de SMS/email automatiques pour les absences, les résultats et les échéances de paiement'],
    ['Déploiement réel', 'Hébergement professionnel, sauvegardes automatiques, formation des utilisateurs, support'],
    ['Module de communication', 'Espace parent avec messagerie, suivi en temps réel des notes et présences'],
]);

// --- 12. ANNEXES ---
$body .= heading1('12. ANNEXES');
$body .= heading2('A. Diagrammes UML (conception)');
$body .= bullet('Diagramme de cas d\'utilisation (section 7.1) ;');
$body .= bullet('Diagramme de classes (section 7.2) ;');
$body .= bullet('Diagrammes de séquence (sections 7.3 et 7.4) ;');
$body .= bullet('Diagrammes d\'activités (sections 7.5 et 7.6) ;');
$body .= bullet('Diagrammes d\'états (sections 7.7 et 7.8).');
$body .= heading2('B. Captures d\'écran de l\'application');
$body .= bullet('Écran de connexion ; tableau de bord du directeur ; registre des élèves ; formulaire d\'encodage des cotes ; feuille de présence hebdomadaire ; liste des paiements et reçu ; bulletin de notes imprimé ; espace élève (notes, bulletins, finances).');
$body .= heading2('C. Code source');
$body .= bullet('Dépôt GitHub (lien à fournir) ; structure Laravel : app/Models, app/Http/Controllers, database/migrations, resources/views, routes/web.php.');
$body .= heading2('D. Documents d\'analyse');
$body .= bullet('Cahier des charges (à fournir) ; questionnaire d\'entretien avec les responsables d\'établissement (à fournir).');
$body .= heading2('E. Journal de bord du projet');
$body .= bullet('Suivi hebdomadaire des activités, décisions et difficultés rencontrées (à compléter).');

// --- 13. RÉFÉRENCES AU CODE ---
$body .= heading1('13. RÉFÉRENCES AU CODE DU PROJET');
$body .= table([
    ['Composant', 'Fichier(s) principal(aux)'],
    ['Modèles', 'app/Models/ (User, Ecole, Eleve, Enseignant, Option, Classe, Cours, Plan, Periode, Inscription, Cote, Presence, Frais, FraisClasse, Paiement, RappelPaiement, ConfigRappel)'],
    ['Contrôleurs', 'app/Http/Controllers/ (CustomAuthController, InscriptionController, EnseignantController, ComptableController, EleveController, CorpsEnseignantController, RappelController, …)'],
    ['Migrations', 'database/migrations/'],
    ['Vues', 'resources/views/ (directeur, enseignant, comptable, eleve, auth, users, options, annees)'],
    ['Routes', 'routes/web.php, routes/console.php'],
    ['Commandes', 'app/Console/Commands/RappelsPaiement.php (rappels automatiques)'],
    ['Services', 'app/Services/SmsService.php, app/Mail/RappelPaiementMail.php'],
]);

$body .= spacer();
$body .= para('Document rédigé conformément au canevas minimum du projet tutoré en Sciences Informatiques (L1–L3) — Dr Rodrigue KALUMENDO, Classes montantes 2024–2025.', false, 20, 'center', '666666', true);

// ============================================================
// CONSTRUCTION DU FICHIER .DOCX
// ============================================================
$documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
    '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" ' .
    'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' .
    '<w:body>' . $body .
    '<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1134" w:right="1134" w:bottom="1134" w:left="1134" w:header="708" w:footer="708" w:gutter="0"/></w:sectPr>' .
    '</w:body></w:document>';

$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
    '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
    '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
    '<Default Extension="xml" ContentType="application/xml"/>' .
    '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>' .
    '</Types>';

$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
    '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
    '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>' .
    '</Relationships>';

$numbering = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
    '<w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">' .
    '<w:abstractNum w:abstractNumId="0">' .
    '<w:multiLevelType w:val="hybridMultilevel"/>' .
    '<w:lvl w:ilvl="0"><w:start w:val="1"/><w:numFmt w:val="bullet"/><w:lvlText w:val="•"/><w:lvlJc w:val="left"/><w:pPr><w:ind w:left="720" w:hanging="360"/></w:pPr></w:lvl>' .
    '<w:lvl w:ilvl="1"><w:start w:val="1"/><w:numFmt w:val="bullet"/><w:lvlText w:val="o"/><w:lvlJc w:val="left"/><w:pPr><w:ind w:left="1440" w:hanging="360"/></w:pPr></w:lvl>' .
    '</w:abstractNum>' .
    '<w:num w:numId="1"><w:abstractNumId w:val="0"/></w:num>' .
    '</w:numbering>';

$styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
    '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">' .
    '<w:docDefaults><w:rPrDefault><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:sz w:val="22"/></w:rPr></w:rPrDefault></w:docDefaults>' .
    '<w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/></w:style>' .
    '</w:styles>';

// Création du fichier ZIP
if (file_exists($outFile)) { @unlink($outFile); }
$zip = new ZipArchive();
if ($zip->open($outFile, ZipArchive::CREATE) !== true) {
    fwrite(STDERR, "ERREUR : Impossible de créer le fichier $outFile\n");
    exit(1);
}
$zip->addFromString('[Content_Types].xml', $contentTypes);
$zip->addFromString('_rels/.rels', $rels);
$zip->addFromString('word/document.xml', $documentXml);
$zip->addFromString('word/_rels/document.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" .
    '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
    '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/numbering" Target="numbering.xml"/>' .
    '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
    '</Relationships>');
$zip->addFromString('word/numbering.xml', $numbering);
$zip->addFromString('word/styles.xml', $styles);
$zip->close();

echo "OK - Document généré : $outFile\n";
echo "Taille : " . filesize($outFile) . " octets\n";
