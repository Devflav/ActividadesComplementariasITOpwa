<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mperiodo extends Model
{
    protected $table = 'periodo';
    protected $primarykey = 'id_periodo';
    public $timestamps = false;
  
    protected $fillable = [
      'id_periodo', 'nombre', 'inicio', 'fin',
      'ini_inscripcion', 'fin_inscripcion', 'ini_evaluacion',
      'fin_evaluacion', 'ini_gconstancias', 'fin_gconstancias',
      'logo_gob', 'logo_tecnm', 'logo_ito', 'logo_anio',
      'estado', 'condicion'
    ];
}
