<?php
$content = file_get_contents('resources/views/directeur/classes/index.blade.php');

$content = str_replace(
    'Total classes</div><div class="bar">',
    'Total classes</div></div><div class="bar">'
);

$content = str_replace(
    'Eleves inscrits</div><div class="bar">',
    'Eleves inscrits</div></div><div class="bar">'
);

$content = str_replace(
    'Options disponibles</div><div class="bar">',
    'Options disponibles</div></div><div class="bar">'
);

$content = str_replace(
    '#14b8a6"></div>' . "\n" . '<div class="card"><div class="cbody">',
    '#14b8a6"></div></div>' . "\n" . '<div class="card"><div class="cbody">'
);

$content = str_replace(
    '#3b82f6"></div>' . "\n" . '<div class="card"><div class="cbody">',
    '#3b82f6"></div></div>' . "\n" . '<div class="card"><div class="cbody">'
);

$content = str_replace(
    '#a855f7"></div>' . "\n" . '</div>',
    '#a855f7"></div></div>' . "\n" . '</div>'
);

file_put_contents('resources/views/directeur/classes/index.blade.php', $content);
echo "Fixed HTML structure!\n";
