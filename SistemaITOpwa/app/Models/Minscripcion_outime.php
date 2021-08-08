<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Minscripcion_outime extends Model
{
    protected $table = 'inscripcion_outime';
    protected $primarykey;
    public $timestamps = false;

    protected $fillable = [
        'id_inscripcion','oficio'];
}
