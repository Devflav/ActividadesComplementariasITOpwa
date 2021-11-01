<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluacion', function (Blueprint $table) {
            $table->id('id_evaluacion')->autoIncrement();
            $table->foreign('id_inscripcion')->references('id_inscripcion')->on('inscripcion');
            $table->foreign('id_desempenio')->references('id_desempenio')->on('desempenio');
            $table->tinyInteger('asistencias', 2);
            $table->decimal('calificacion', $precision = 10, $scale = 2);
            $table->string('observaciones', 250);
            $table->string('constancia', 200);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluacion');
    }
}
