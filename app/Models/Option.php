<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Option extends Model
{
    use HasFactory;

    protected $primaryKey = 'idOption'; // Ta clé primaire personnalisée
    protected $fillable = ['nomoption', 'sigle', 'ecole_id'];

    /**
     * Une option peut avoir plusieurs classes.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class, 'option_id', 'idOption');
    }
}
