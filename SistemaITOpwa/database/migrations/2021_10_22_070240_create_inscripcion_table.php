<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripcion', function (Blueprint $table) {
            $table->id('id_inscripcion')->autoIncrement();
            $table->foreign('id_estudiante')->references('id_estudiante')->on('estudiante');
            $table->foreign('id_grupo')->references('id_grupo')->on('grupo');
            $table->date('fecha');
            $table->tinyInteger('aprobada');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscripcion');
    }
}
