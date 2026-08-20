<?php
// Vérification de l'intégrité du fichier .docx généré
$file = __DIR__ . '/Documentation_Projet_Tutore_L3.docx';

if (!file_exists($file)) {
    echo "ERREUR : fichier introuvable\n";
    exit(1);
}

echo "Fichier : " . $file . "\n";
echo "Taille  : " . filesize($file) . " octets\n";
echo "Extension: " . pathinfo($file, PATHINFO_EXTENSION) . "\n";

$z = new ZipArchive();
if ($z->open($file) !== true) {
    echo "ERREUR : le fichier n'est pas un ZIP/DOCX valide\n";
    exit(1);
}

echo "---- Contenu du package DOCX ----\n";
for ($i = 0; $i < $z->numFiles; $i++) {
    echo "  + " . $z->getNameIndex($i) . "\n";
}

$doc = $z->getFromName('word/document.xml');
echo "---- Résumé du document ----\n";
echo "word/document.xml : " . strlen($doc) . " caractères\n";

// Extraire le texte brut approximatif pour vérifier le contenu
$text = strip_tags(str_replace(['<w:p>', '<w:br/>', '</w:p>'], "\n", preg_replace('/<w:pPr>.*?<\/w:pPr>/s', '', $doc)));
$text = preg_replace('/<[^>]+>/', '', $text);
$text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
$lines = array_filter(array_map('trim', explode("\n", $text)));

echo "Nb de lignes de texte : " . count($lines) . "\n";

// Vérifier la présence des sections clés
$sections = ['TITRE DU PROJET', 'CONTEXTE', 'PROBLÉMATIQUE', 'OBJECTIFS', 'MÉTHODOLOGIE',
             'ANALYSE DES BESOINS', 'CONCEPTION', 'PLANIFICATION', 'RESSOURCES', 'RÉSULTATS',
             'LIMITES', 'ANNEXES'];
$full = implode("\n", $lines);
foreach ($sections as $s) {
    echo (stripos($full, $s) !== false ? "[OK] " : "[MANQUANT] ") . $s . "\n";
}

// Vérifier l'absence des sections exclues
echo "---- Sections exclues (RH et Budget) ----\n";
echo (stripos($full, 'RESSOURCES HUMAINES') === false ? "[OK] absente" : "[PRESENTE!]") . " : RESSOURCES HUMAINES\n";
echo (stripos($full, 'BUDGET') === false ? "[OK] absente" : "[PRESENTE!]") . " : BUDGET\n";

$z->close();
echo "---- VERIFICATION TERMINÉE ----\n";

