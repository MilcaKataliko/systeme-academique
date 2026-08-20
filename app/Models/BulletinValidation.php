<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulletinValidation extends Model
{
    public const EN_ATTENTE = 'en_attente';
    public const VALIDE = 'valide';

    protected $fillable = ['inscription_id', 'plan_id', 'statut', 'valide_par', 'valide_le'];
    protected $casts = ['valide_le' => 'datetime'];

    public function inscription(): BelongsTo { return $this->belongsTo(Inscription::class); }
    public function plan(): BelongsTo { return $this->belongsTo(Plan::class); }
    public function valideur(): BelongsTo { return $this->belongsTo(User::class, 'valide_par'); }
}
