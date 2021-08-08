<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mnivel_desempenio extends Model
{
    protected $table = 'nivel_desempenio';
    protected $primarykey = 'id_desempenio';
    public $timestamps = false;

    protected $fillable = [
        'id_desempenio','nombre', 'valor'];
}
