<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mhorarios_impresos extends Model
{
    protected $table = 'horarios_impresos';
    protected $primarykey;
    public $timestamps = false;

    protected $fillable = [
        'id_grupo','id_estudiante'];
}
