<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ecole extends Model
{
    // Autoriser l'insertion de ces données
    protected $fillable = ['nom_ecole', 'code_national_epst', 'province_educationnelle', 'adresse'];
}