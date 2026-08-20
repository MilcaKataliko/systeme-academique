<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    protected $fillable = ['ecole_id', 'nom_periode', 'est_cloturee'];

    protected $casts = [
        'est_cloturee' => 'boolean',
    ];

    public function ecole(): BelongsTo
    {
        return $this->belongsTo(Ecole::class);
    }

    public function cotes(): HasMany
    {
        return $this->hasMany(Cote::class);
    }
}
