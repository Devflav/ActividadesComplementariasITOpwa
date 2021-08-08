<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mgrupo extends Model
{
    protected $table = 'grupo';
    protected $primarykey = 'id_grupo';
    public $timestamps = false;

    protected $fillable = [
        'id_grupo', 'id_periodo', 'id_actividad', 'id_persona',
        'id_lugar', 'clave', 'cupo', 'cupo_libre', 'asistencias', 
        'orden', 'estado'
    ];
}
