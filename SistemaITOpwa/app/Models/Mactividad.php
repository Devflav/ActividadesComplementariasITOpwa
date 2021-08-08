<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mactividad extends Model
{
    protected $table = 'actividad';
    protected $primarykey = 'id_actividad';
    public $timestamps = false;
  
    protected $fillable = [
      'id_actividad', 'id_depto', 'id_tipo', 
      'id_periodo', 'clave', 'nombre', 
      'creditos', 'descripcion', 
      'restringida', 'estado'
    ];

}
