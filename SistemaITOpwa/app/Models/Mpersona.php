<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mpersona extends Model
{
    protected $table = 'persona';
    protected $primarykey = 'id_persona';
    public $timestamps = false;

    protected $fillable = [
        'id_persona', 'nombre', 'apePat', 
        'apeMat', 'curp', 'tipo', 'estado'
    ];
}
