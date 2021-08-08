<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mpuesto extends Model
{
    protected $table = 'puesto';
    protected $primarykey = 'id_puesto';
    public $timestamps = false;

    protected $fillable = [
        'id_puesto', 'nombre', 
        'descripcion', 'estado'
    ];
}
