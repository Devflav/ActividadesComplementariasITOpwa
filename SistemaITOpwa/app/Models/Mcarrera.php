<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mcarrera extends Model
{
    protected $table = 'carrera';
    protected $primarykey = 'id_carrera';
    public $timestamps = false;
  
    protected $fillable = [
      'id_carrea', 'id_depto', 'nombre', 'estado'];
}
