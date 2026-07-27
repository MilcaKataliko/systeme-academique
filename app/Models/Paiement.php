<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory, BelongsToSchool;

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

    public function inscription()
    {
        return $this->belongsTo(Inscription::class, 'inscription_id');
    }

    public function frais()
    {
        return $this->belongsTo(Frais::class, 'frais_id');
    }

    public function comptable()
    {
        return $this->belongsTo(User::class, 'comptable_id');
    }
}

