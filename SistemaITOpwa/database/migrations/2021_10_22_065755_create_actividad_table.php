<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad', function (Blueprint $table) {
            $table->id('id_actividad')->autoIncrement();
            $table->foreign('id_depto')->references('id_depto')->on('departamento');
            $table->foreign('id_tipo')->references('id_tipo')->on('tipo');
            $table->foreign('id_periodo')->references('id_periodo')->on('periodo');
            $table->string('clave', 5);
            $table->string('nombre', 100);
            $table->tinyInteger('creditos');
            $table->string('descripcion', 250);
            $table->tinyInteger('restringida');
            $table->tinyInteger('estado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actividad');
    }
}
