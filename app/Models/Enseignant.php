<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enseignant extends Model
{
    protected $fillable = [
        'ecole_id', 
        'user_id', 
        'matricule', 
        'nom', 
        'postnom', 
        'prenom', 
        'telephone', 
        'grade'
    ];

    /**
     * Un enseignant appartient à une école.
     */
    public function ecole(): BelongsTo
    {
        return $this->belongsTo(Ecole::class, 'ecole_id');
    }

    /**
     * Un enseignant peut avoir un compte utilisateur de connexion.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}