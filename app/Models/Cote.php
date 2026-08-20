<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cote extends Model
{
    protected $fillable = [
        'inscription_id',
        'plan_id',
        'periode_id',
        'encode_par',
        'interrogation_s1',
        'devoir_domicile_s1',
        'periode_1',
        'periode_2',
'periode_3',
        'examen_s1',
        'interrogation_s2',
        'devoir_domicile_s2',
        'periode_4',
        'periode_5',
        'periode_6',
        'examen_s2',
        'pourcentage_presence',
    ];

    protected $casts = [
        'interrogation_s1' => 'decimal:2',
        'devoir_domicile_s1' => 'decimal:2',
        'periode_1' => 'decimal:2',
        'periode_2' => 'decimal:2',
        'periode_3' => 'decimal:2',
        'examen_s1' => 'decimal:2',
        'interrogation_s2' => 'decimal:2',
        'devoir_domicile_s2' => 'decimal:2',
        'periode_4' => 'decimal:2',
        'periode_5' => 'decimal:2',
        'periode_6' => 'decimal:2',
        'examen_s2' => 'decimal:2',
        'pourcentage_presence' => 'decimal:2',
    ];

    /**
     * Total des points obtenus par l'élève pour ce cours.
     */
    public function getTotalPointsAttribute(): float
    {
        return collect([
            $this->interrogation_s1,
            $this->devoir_domicile_s1,
            $this->periode_1,
            $this->periode_2,
            $this->periode_3,
            $this->examen_s1,
            $this->interrogation_s2,
            $this->devoir_domicile_s2,
            $this->periode_4,
            $this->periode_5,
            $this->periode_6,
            $this->examen_s2,
        ])->sum() ?? 0;
    }

    /**
     * Point maximum total du cours (basé sur le plan).
     */
    public function getMaxTotalAttribute(): float
    {
        if (!$this->plan) return 0;
        $maxPeriode = $this->plan->maxima_periode ?? 10;
        $maxExamen = $this->plan->maxima_examen ?? 20;
        // 6 périodes + 2 examens + 2 interros + 2 devoirs
        return ($maxPeriode * 6) + ($maxExamen * 2) + ($maxPeriode * 4);
    }

    /**
     * Pourcentage obtenu.
     */
    public function getPourcentageAttribute(): ?float
    {
        $max = $this->max_total;
        if ($max <= 0) return null;
        return round(($this->total_points / $max) * 100, 2);
    }

    /**
     * Moyenne des points (total / nombre d'évaluations renseignées).
     */
    public function getMoyenneAttribute(): ?float
    {
        $count = 0;
        $champs = [
            $this->interrogation_s1, $this->devoir_domicile_s1,
            $this->periode_1, $this->periode_2, $this->periode_3, $this->examen_s1,
            $this->interrogation_s2, $this->devoir_domicile_s2,
            $this->periode_4, $this->periode_5, $this->periode_6, $this->examen_s2,
        ];
        foreach ($champs as $val) {
            if ($val !== null) $count++;
        }
if ($count === 0) return null;
        return round($this->total_points / $count, 2);
    }

/**
     * Bonus de présence basé sur le pourcentage de présence.
     *  - Présence à 100%  => bonus de 5%
     *  - Sinon            => bonus de 0%
     */
    public function getBonusPresenceAttribute(): float
    {
        // Si aucune présence saisie, pas de bonus (ou 0)
        if ($this->pourcentage_presence === null) {
            return 0;
        }

        return (float) $this->pourcentage_presence >= 100 ? 5 : 0;
    }

    /**
     * Pourcentage final = pourcentage obtenu + bonus de présence.
     */
    public function getPourcentageFinalAttribute(): ?float
    {
        $base = $this->pourcentage;
        if ($base === null) {
            return null;
        }

        return round($base + $this->bonus_presence, 2);
    }

    /**
     * Statut de réussite (Réussi si pourcentage final >= 55).
     */
    public function getStatutAttribute(): ?string
    {
        $final = $this->pourcentage_final;
        if ($final === null) {
            return null;
        }

        return $final >= 55 ? 'Réussi' : 'Échoué';
    }

    public function inscription(): BelongsTo
    {
        return $this->belongsTo(Inscription::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function encodeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encode_par');
    }
}
