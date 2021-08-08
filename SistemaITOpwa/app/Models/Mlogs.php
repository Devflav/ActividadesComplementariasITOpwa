<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mlogs extends Model
{
    protected $table = 'logs';
    protected $primarykey = 'id_log';
    public $timestamps = false;
  
    protected $fillable = [
      'id_log','id_persona', 
      'id_de_evento', 'evento', 
      'direccion', 'fecha'
      ];
}
