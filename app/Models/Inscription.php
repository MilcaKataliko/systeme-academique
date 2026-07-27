<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'ecole_id',
        'eleve_id',
        'classe_id',
        'annee_scolaire',
        'statut',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class, 'eleve_id');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'inscription_id');
    }

    public function cotes()
    {
        return $this->hasMany(Cote::class, 'inscription_id');
    }
}

