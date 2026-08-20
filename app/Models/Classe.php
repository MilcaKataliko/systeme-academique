<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    protected $fillable = ['option_id', 'nom_classe', 'niveau', 'section'];

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'option_id', 'idOption');
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class);
    }
}
