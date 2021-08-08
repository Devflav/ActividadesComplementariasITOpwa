<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mdepartamento extends Model
{
    protected $table = 'departamento';
    protected $primarykey = 'id_depto';
    public $timestamps = false;

    protected $fillable = [
        'id_depto', 'id_persona', 
        'nombre',  'hoja_mem', 'estado'];
}
