<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulletinImportAnomaly extends Model
{
    protected $fillable = ['ecole_id', 'matricule', 'code_cours', 'champ', 'note', 'motif', 'ligne_source'];

    protected $casts = ['note' => 'decimal:2'];
}
