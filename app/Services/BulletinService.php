<?php

namespace App\Services;

use App\Models\Cote;
use App\Models\Plan;

class BulletinService
{
    public const CHAMPS = [
        'interrogation_s1', 'devoir_domicile_s1', 'periode_1', 'periode_2', 'periode_3', 'examen_s1',
        'interrogation_s2', 'devoir_domicile_s2', 'periode_4', 'periode_5', 'periode_6', 'examen_s2',
    ];

    public static function maximumPourChamp(Plan $plan, string $champ): float
    {
        return in_array($champ, ['examen_s1', 'examen_s2'], true)
            ? (float) $plan->maxima_examen
            : (float) $plan->maxima_periode;
    }

    /** Moyenne de matière normalisée sur 20, quelle que soit la grille de notation. */
    public static function moyenneMatiere(Cote $cote, Plan $plan): ?float
    {
        $total = 0;
        $maximum = 0;
        foreach (self::CHAMPS as $champ) {
            if ($cote->{$champ} !== null) {
                $total += (float) $cote->{$champ};
                $maximum += self::maximumPourChamp($plan, $champ);
            }
        }

        // Compatibilité avec les anciennes notes enregistrées dans points_obtenus.
        if ($maximum === 0 && $cote->points_obtenus !== null) {
            $total = (float) $cote->points_obtenus;
            $maximum = (float) ($plan->maxima_periode ?: 20);
        }

        return $maximum > 0 ? round(($total / $maximum) * 20, 2) : null;
    }

    /** @return array{moyenne: ?float, total_coefficients: int} */
    public static function moyenneGenerale(iterable $cotes): array
    {
        $totalPondere = 0;
        $totalCoefficients = 0;
        foreach ($cotes as $cote) {
            $plan = $cote->plan;
            if (! $plan) continue;
            $moyenne = self::moyenneMatiere($cote, $plan);
            if ($moyenne === null) continue;
            $coefficient = max(1, (int) $plan->coefficient);
            $totalPondere += $moyenne * $coefficient;
            $totalCoefficients += $coefficient;
        }
        return ['moyenne' => $totalCoefficients ? round($totalPondere / $totalCoefficients, 2) : null, 'total_coefficients' => $totalCoefficients];
    }
}
