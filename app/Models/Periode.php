<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'ecole_id',
        'nom_periode',
        'est_cloturee',
    ];

    protected $casts = [
        'est_cloturee' => 'boolean',
    ];

    public function cotes()
    {
        return $this->hasMany(Cote::class, 'periode_id');
    }
}

