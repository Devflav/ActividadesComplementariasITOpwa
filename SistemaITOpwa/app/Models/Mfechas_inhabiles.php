<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mfechas_inhabiles extends Model
{
    protected $table = 'fechas_inhabiles';
    protected $primarykey = 'id_fecha';
    public $timestamps = false;

    protected $fillable = [
        'id_fecha', 'fecha', 
        'motivo', 'estado'
    ];
}
