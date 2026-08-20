<?php

namespace App\Services;

use App\Models\Cote;
use App\Models\Inscription;
use App\Models\Plan;
use App\Models\StudentGrade;

class StudentGradeService
{
    /** Synchronise la grille officielle du bulletin avec une cote encodée. */
    public static function synchronise(Cote $cote, Inscription $inscription, Plan $plan): StudentGrade
    {
        $maxTravail = (float) $plan->maxima_periode;
        $maxExamen = (float) $plan->maxima_examen;
        $travaux1 = self::note($cote->interrogation_s1);
        $travaux2 = self::note($cote->devoir_domicile_s1);
        $exam1 = self::note($cote->examen_s1);
        $travaux3 = self::note($cote->interrogation_s2);
        $travaux4 = self::note($cote->devoir_domicile_s2);
        $exam2 = self::note($cote->examen_s2);
        $total1 = ($travaux1 ?? 0) + ($travaux2 ?? 0) + ($exam1 ?? 0);
        $total2 = ($travaux3 ?? 0) + ($travaux4 ?? 0) + ($exam2 ?? 0);
        $max1 = ($maxTravail * 2) + $maxExamen;

        return StudentGrade::updateOrCreate(
            ['inscription_id' => $inscription->id, 'plan_id' => $plan->id],
            [
                'student_id' => $inscription->eleve_id,
                'school_year' => $inscription->annee_scolaire,
                'class_name' => $inscription->classe->nom_classe,
                'subject' => $plan->cours->nom_cours,
                'travaux_1' => $travaux1, 'travaux_2' => $travaux2, 'exam_1' => $exam1,
                'total_1' => $total1, 'max_1' => $max1,
                'travaux_3' => $travaux3, 'travaux_4' => $travaux4, 'exam_2' => $exam2,
                'total_2' => $total2, 'max_2' => $max1,
                'tg' => $total1 + $total2, 'max_tg' => $max1 * 2,
            ]
        );
    }

    private static function note(mixed $value): ?float
    {
        return $value === null ? null : (float) $value;
    }
}
