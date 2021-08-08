<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mestudiante extends Model
{
    protected $table = 'estudiante';
    protected $primarykey = 'id_estudiante';
    public $timestamps = false;

    protected $fillable = [
        'id_estudiante', 'id_persona', 
        'id_carrera', 'num_control', 
        'email', 'semestre'
    ];
}
