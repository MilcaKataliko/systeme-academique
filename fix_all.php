<?php
// Read the original file
$content = file_get_contents('resources/views/directeur/classes/index.blade.php');

// Step 1: Find the g3 section and rebuild it completely with proper HTML
$pattern = '/<div class="g3">(.*?)<\/div>\s*<div class="g2">/s';
preg_match($pattern, $content, $matches);

// Build the correct g3 section with 3 properly closed cards
$correct_g3 = '<div class="g3">
<div class="card"><div class="cbody"><div style="display:flex;align-items:center;gap:20px"><div class="sicon" style="background:rgba(20,184,166,.1);border:1px solid rgba(20,184,166,.2);color:#14b8a6"><i class="fa-solid fa-school"></i></div><div><div class="num">' . '{{ $stats->total }}' . '</div><div class="lbl">Total classes</div></div><div class="bar"><div class="bfill" style="width:{{ min($stats->total * 10, 100) }}%;background:#14b8a6"></div></div>
<div class="card"><div class="cbody"><div style="display:flex;align-items:center;gap:20px"><div class="sicon" style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);color:#60a5fa"><i class="fa-solid fa-users"></i></div><div><div class="num">' . '{{ $stats->total_eleves }}' . '</div><div class="lbl">Eleves inscrits</div></div><div class="bar"><div class="bfill" style="width:{{ min($stats->total_eleves * 5, 100) }}%;background:#3b82f6"></div></div>
<div class="card"><div class="cbody"><div style="display:flex;align-items:center;gap:20px"><div class="sicon" style="background:rgba(168,85,247,.1);border:1px solid rgba(168,85,247,.2);color:#a78bfa"><i class="fa-solid fa-graduation-cap"></i></div><div><div class="num">' . '{{ $stats->options }}' . '</div><div class="lbl">Options disponibles</div></div><div class="bar"><div class="bfill" style="width:{{ min($stats->options * 20, 100) }}%;background:#a855f7"></div></div>
</div>

<div class="g2">';

$content = preg_replace($pattern, $correct_g3, $content);

file_put_contents('resources/views/directeur/classes/index.blade.php', $content);
echo "Fixed!";
