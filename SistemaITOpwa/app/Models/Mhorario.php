<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mhorario extends Model
{
    protected $table = 'horario';
    public $timestamps = false;
  
    protected $fillable = [
      'id_grupo','id_dia', 
      'hora_inicio', 'hora_fin'
    ];
}
