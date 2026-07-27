<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'classe_id',
        'cours_id',
        'enseignant_id',
        'maxima_periode',
        'maxima_examen',
        'annee_scolaire',
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function cour()
    {
        return $this->belongsTo(Cour::class, 'cours_id');
    }

    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function cotes()
    {
        return $this->hasMany(Cote::class, 'plan_id');
    }
}

