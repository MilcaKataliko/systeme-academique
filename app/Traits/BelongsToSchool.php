<?php

namespace App\Traits;


use App\Models\Ecole;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToSchool
{
    /**
     * Boot du Trait : Injection automatique lors de la création
     */
    protected static function bootBelongsToSchool()
    {
        // 1. Lors de la création d'un modèle (User, Eleve, Option, etc.)
        static::creating(function ($model) {
            if (auth()->check() && !$model->ecole_id) {
                $model->ecole_id = auth()->user()->ecole_id ?? session('ecole_id');
            }
        });

        // 2. Scope global : Filtre automatiquement les requêtes SQL par école !
        static::addGlobalScope('school', function (Builder $builder) {
            if (auth()->check() && auth()->user()->ecole_id) {
                $builder->where('ecole_id', auth()->user()->ecole_id);
            }
        });
    }

    public function ecole()
    {
        return $this->belongsTo(Ecole::class, 'ecole_id');
    }
}