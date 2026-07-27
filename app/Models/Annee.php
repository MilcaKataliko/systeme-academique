<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annee extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'annees';
    protected $primaryKey = 'idAnnee';

    protected $fillable = ['ecole_id', 'anneescolaire'];

    public $timestamps = true;
}

