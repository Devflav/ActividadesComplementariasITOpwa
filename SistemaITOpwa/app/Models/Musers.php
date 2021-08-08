<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musers extends Model
{
    protected $table = 'users';
    protected $primarykey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id', 'id_persona', 'id_puesto',
        'nombre', 'usuario', 'password', 
        'fecha_registro', 'edo_sesion', 
        'estado'];
}
