<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Frais extends Model
{
    protected $fillable = [
        'ecole_id',
        'intitule_frais',
        'montant',
        'devise',
        'classe_id',
        'annee_scolaire',
    ];

    /**
     * Un frais appartient à une école.
     */
    public function ecole(): BelongsTo
    {
        return $this->belongsTo(Ecole::class);
    }

    /**
     * Un frais est associé à une classe spécifique.
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Un frais peut avoir plusieurs paiements.
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }
}

