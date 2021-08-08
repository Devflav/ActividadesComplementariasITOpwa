<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mlugar extends Model
{
    protected $table = 'lugar';
    protected $primarykey = 'id_lugar';
    public $timestamps = false;

    protected $fillable = [
        'id_lugar','nombre', 'estado'];
}
