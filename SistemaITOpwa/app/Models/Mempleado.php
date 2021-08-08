<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mempleado extends Model
{
    protected $table = 'empleado';
    protected $primarykey = 'id_empleado';
    public $timestamps = false;

    protected $fillable = [
        'id_empleado','id_persona', 
        'id_depto', 'id_grado', 'id_puesto'];
}
