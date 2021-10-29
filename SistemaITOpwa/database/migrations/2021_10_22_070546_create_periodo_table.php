<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periodo', function (Blueprint $table) {
            $table->id('id_periodo')->autoIncrement();
            $table->string('nombre', 30);
            $table->date('inicio');
            $table->date('fin');
            $table->date('ini_inscripcion');
            $table->date('fin_inscripcion');
            $table->date('ini_evaluacion');
            $table->date('fin_evluacion');
            $table->date('ini_gconstancias');
            $table->date('fin_gcontancias');
            $table->string('cabecera', 50);
            $table->string('pie', 50);
            $table->string('estado', 10);
            $table->tinyInteger('condicion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('periodo');
    }
}
