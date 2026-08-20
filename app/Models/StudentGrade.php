<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGrade extends Model
{
    protected $fillable = [
        'student_id', 'inscription_id', 'plan_id', 'school_year', 'class_name', 'subject',
        'travaux_1', 'travaux_2', 'exam_1', 'total_1', 'max_1',
        'travaux_3', 'travaux_4', 'exam_2', 'total_2', 'max_2', 'tg', 'max_tg',
    ];

    protected $casts = [
        'travaux_1' => 'decimal:2', 'travaux_2' => 'decimal:2', 'exam_1' => 'decimal:2',
        'total_1' => 'decimal:2', 'max_1' => 'decimal:2', 'travaux_3' => 'decimal:2',
        'travaux_4' => 'decimal:2', 'exam_2' => 'decimal:2', 'total_2' => 'decimal:2',
        'max_2' => 'decimal:2', 'tg' => 'decimal:2', 'max_tg' => 'decimal:2',
    ];

    public function student(): BelongsTo { return $this->belongsTo(Eleve::class, 'student_id'); }
    public function inscription(): BelongsTo { return $this->belongsTo(Inscription::class); }
    public function plan(): BelongsTo { return $this->belongsTo(Plan::class); }
}
