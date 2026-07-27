<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cote extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'plan_id',
        'periode_id',
        'points_obtenus',
        'encode_par',
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class, 'inscription_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }

    public function encodeur()
    {
        return $this->belongsTo(User::class, 'encode_par');
    }
}

