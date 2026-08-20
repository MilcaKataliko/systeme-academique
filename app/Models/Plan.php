<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'classe_id', 'cours_id', 'enseignant_id',
        'maxima_periode', 'maxima_examen', 'coefficient', 'annee_scolaire'
    ];

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class);
    }

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enseignant_id');
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
}
