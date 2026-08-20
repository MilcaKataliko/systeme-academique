<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    protected $fillable = [
        'ecole_id',
        'inscription_id',
        'frais_id',
        'comptable_id',
        'montant_paye',
        'date_paiement',
        'numero_recu',
        'mode_paiement',
    ];

    protected $casts = [
        'date_paiement' => 'date',
    ];

    /**
     * Un paiement appartient à une inscription (élève).
     */
    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    /**
     * Un paiement concerne un type de frais.
     */
    public function frais(): BelongsTo
    {
        return $this->belongsTo(Frais::class);
    }

    /**
     * Un paiement a été enregistré par un comptable.
     */
    public function comptable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'comptable_id');
    }

    /**
     * Un paiement appartient à une école.
     */
    public function ecole(): BelongsTo
    {
        return $this->belongsTo(Ecole::class);
    }
}

