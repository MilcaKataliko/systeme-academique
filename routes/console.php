<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Planification des rappels de paiement automatiques
// Par défaut : exécution toutes les heures pour vérifier si des rappels doivent être envoyés
Schedule::command('rappels:envoyer')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
