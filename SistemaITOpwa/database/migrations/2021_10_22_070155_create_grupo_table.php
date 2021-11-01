<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupo', function (Blueprint $table) {
            $table->id('id_grupo')->autoIncrement();
            $table->foreign('id_periodo')->references('id_periodo')->on('periodo');
            $table->foreign('id_actividad')->references('id_actividad')->on('actividad');
            $table->foreign('id_persona')->references('id_persona')->on('persona');
            $table->foreign('id_lugar')->references('id_lugar')->on('lugar');
            $table->string('clave', 7);
            $table->smallInteger('cupo', 4);
            $table->smallInteger('cupo_libre', 4);
            $table->tinyInteger('asistencias', 2);
            $table->tinyInteger('orden');
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
        Schema::dropIfExists('grupo');
    }
}
