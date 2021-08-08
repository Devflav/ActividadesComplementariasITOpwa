<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meval_valor extends Model
{
    protected $table = 'eval_valor';
    protected $primarykey;
    public $timestamps = false;

    protected $fillable = [
        'id_evaluacion', 'id_crit_eval', 
        'id_desempenio'
    ];
}
