<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mcriterios_evaluacion extends Model
{
    protected $table = 'criterios_evaluacion';
    protected $primarykey = 'id_crit_eval';
    public $timestamps = false;

    protected $fillable = [
        'id_crit_eval', 'nombre', 
        'descripcion', 'estado'
    ];
}
