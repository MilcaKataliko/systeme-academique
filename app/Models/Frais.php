<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frais extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'ecole_id',
        'intitule_frais',
        'montant_standard',
        'devise',
    ];

    public function fraisClasses()
    {
        return $this->hasMany(FraisClasse::class, 'frais_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'frais_id');
    }
}

