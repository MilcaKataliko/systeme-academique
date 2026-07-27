<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cour extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'cours';

    protected $fillable = [
        'ecole_id',
        'nom_cours',
        'code_cours',
    ];

    public function plans()
    {
        return $this->hasMany(Plan::class, 'cours_id');
    }
}

