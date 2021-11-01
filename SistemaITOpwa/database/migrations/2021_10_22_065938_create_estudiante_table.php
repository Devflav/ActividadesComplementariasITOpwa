<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstudianteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estudiante', function (Blueprint $table) {
            $table->id('id_estudiante')->autoIncrement();
            $table->foreign('id_persona')->references('id_persona')->on('persona');
            $table->foreign('id_carrera')->references('id_carrera')->on('carrera');
            $table->string('num_control', 9);
            $table->string('email', 26);
            $table->tinyInteger('semestre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estudiante');
    }
}
