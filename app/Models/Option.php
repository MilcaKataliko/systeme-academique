<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $primaryKey = 'idOption'; // Ta clé primaire personnalisée
    protected $fillable = ['nomoption', 'sigle'];
}
