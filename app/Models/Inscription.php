<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscription extends Model
{
    protected $fillable = [
        'ecole_id', 'eleve_id', 'classe_id',
        'annee_scolaire', 'statut'
    ];

    public function ecole(): BelongsTo
    {
        return $this->belongsTo(Ecole::class);
    }

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function cotes(): HasMany
    {
        return $this->hasMany(Cote::class);
    }

    public function studentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function validationsBulletin(): HasMany
    {
        return $this->hasMany(BulletinValidation::class);
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }
}
