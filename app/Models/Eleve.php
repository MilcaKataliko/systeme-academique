<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eleve extends Model
{
    use HasFactory, BelongsToSchool;

    protected $primaryKey = 'id';

    protected $fillable = [
        'ecole_id',
        'nom',
        'postnom',
        'prenom',
        'genre',
        'date_naissance',
        'lieu_naissance',
    ];

    // Helper pour afficher le nom complet
    public function getNomCompletAttribute()
    {
        return strtoupper($this->nom) . ' ' . ucfirst($this->postnom) . ' ' . ucfirst($this->prenom);
    }
}