<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mevaluacion extends Model
{
    protected $table = 'evaluacion';
    protected $primarykey = 'id_evaluacion';
    public $timestamps = false;
  
    protected $fillable = [
      'id_evaluacion', 'id_inscripcion', 
      'id_desempenio', 'asistencias', 
      'calificacion', 'observaciones',
      'constancia'
    ];
}
