<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mdias_semana extends Model
{
    protected $table = 'dias_semana';
    protected $primarykey = 'id_dia';
    public $timestamps = false;
  
    protected $fillable = [
      'id_dia','nombre'];
}
