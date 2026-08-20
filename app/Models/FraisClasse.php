<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FraisClasse extends Model
{
    protected $table = 'frais_classe';

    protected $fillable = [
        'classe_id',
        'frais_id',
        'montant_specifique',
        'annee_scolaire',
    ];

    /**
     * Un frais_classe appartient à une classe.
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Un frais_classe appartient à un type de frais.
     */
    public function frais(): BelongsTo
    {
        return $this->belongsTo(Frais::class);
    }
}

