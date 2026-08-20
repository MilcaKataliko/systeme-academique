<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::all();

echo str_repeat('-', 80) . PHP_EOL;
echo sprintf("%-3s | %-20s | %-30s | %-12s", "ID", "NOM", "EMAIL", "ROLE") . PHP_EOL;
echo str_repeat('-', 80) . PHP_EOL;

foreach ($users as $user) {
    printf(
        "%-3d | %-20s | %-30s | %-12s\n",
        $user->id,
        $user->name,
        $user->email,
        $user->role
    );
}

echo str_repeat('-', 80) . PHP_EOL;
echo "Total: " . $users->count() . " utilisateur(s)" . PHP_EOL;
echo PHP_EOL;
echo "Mot de passe par défaut pour tous : password123" . PHP_EOL;
