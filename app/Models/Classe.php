<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'classes';

    protected $fillable = [
        'ecole_id',
        'option_id',
        'nom_classe',
        'niveau',
        'section',
        'effectif_max',
    ];

    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id', 'idOption');
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'classe_id');
    }
}
