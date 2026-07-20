<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annee extends Model
{
    use HasFactory;

    protected $primaryKey = 'idAnnee'; // Ta clé primaire personnalisée
    protected $fillable = ['anneescolaire'];
}
