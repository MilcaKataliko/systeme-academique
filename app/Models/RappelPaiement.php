<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RappelPaiement extends Model
{
    protected $table = 'rappels_paiement';

    protected $fillable = [
        'ecole_id',
        'inscription_id',
        'frais_id',
        'montant_du',
        'montant_paye',
        'solde',
        'type_rappel',
        'statut',
        'email_destinataire',
        'sms_destinataire',
        'email_envoye',
        'sms_envoye',
        'message_erreur',
        'date_envoi',
    ];

    protected $casts = [
        'montant_du' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'solde' => 'decimal:2',
        'email_envoye' => 'boolean',
        'sms_envoye' => 'boolean',
        'date_envoi' => 'datetime',
    ];

    public function ecole(): BelongsTo
    {
        return $this->belongsTo(Ecole::class);
    }

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    public function frais(): BelongsTo
    {
        return $this->belongsTo(Frais::class);
    }
}

