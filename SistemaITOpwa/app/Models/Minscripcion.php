<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Minscripcion extends Model
{
    protected $table = 'inscripcion';
    protected $primarykey = 'id_inscripcion';
    public $timestamps = false;
  
    protected $fillable = [
      'id_inscripcion','id_estudiante', 
      'id_grupo', 'fecha', 'aprobada'
    ];
}
