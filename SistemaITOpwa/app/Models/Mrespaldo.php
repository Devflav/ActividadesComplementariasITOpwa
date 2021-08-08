<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mrespaldo extends Model
{
    protected $table = 'respaldo';
    protected $primarykey = 'id_respaldo';
    public $timestamps = false;

    protected $fillable = [
        'id_respaldo', 'id_empleado',
        'tipo', 'ubicacion', 'descripcion',
        'fecha', 'hora', 'estado'];
}
