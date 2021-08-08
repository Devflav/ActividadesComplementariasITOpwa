<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mtipo extends Model
{
    protected $table = 'tipo';
    protected $primarykey = 'id_tipo';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo','nombre', 'estado'];
}
