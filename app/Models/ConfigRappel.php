<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfigRappel extends Model
{
    protected $table = 'config_rappels';

    protected $fillable = [
        'ecole_id',
        'actif',
        'frequence',
        'jour_envoi',
        'jour_du_mois',
        'heure_envoi',
        'email_actif',
        'sms_actif',
        'message_personnalise',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'email_actif' => 'boolean',
        'sms_actif' => 'boolean',
        'jour_du_mois' => 'integer',
        'heure_envoi' => 'integer',
    ];

    public function ecole(): BelongsTo
    {
        return $this->belongsTo(Ecole::class);
    }
}

