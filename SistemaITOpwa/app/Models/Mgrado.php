<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mgrado extends Model
{
    protected $table = 'grado';
    protected $primarykey = 'id_grado';
    public $timestamps = false;

    protected $fillable = [
        'id_grado','nombre', 'significado', 'estado'];
}
