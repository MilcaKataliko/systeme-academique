<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    protected $fillable = [
        'eleve_id',
        'plan_id',
        'semaine_debut',
        'jour_index',
        'date',
        'statut',
        'encode_par',
    ];

    protected $casts = [
        'semaine_debut' => 'date:Y-m-d',
        'jour_index'    => 'integer',
        'date'          => 'date:Y-m-d',
    ];

    /**
     * Statuts possibles (simplifié).
     */
    public const STATUTS = [
        'present' => 'Présent',
        'absent'  => 'Absent',
        'retard'  => 'Retard',
    ];

    /**
     * Un statut présent = participation (présent ou retard).
     */
    public function getEstPresentAttribute(): bool
    {
        return $this->statut === 'present';
    }

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function encodeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encode_par');
    }
}
