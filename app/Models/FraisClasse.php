<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraisClasse extends Model
{
    use HasFactory;

    protected $table = 'frais_classe';

    protected $fillable = [
        'classe_id',
        'frais_id',
        'montant_specifique',
        'annee_scolaire',
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function frais()
    {
        return $this->belongsTo(Frais::class, 'frais_id');
    }
}

