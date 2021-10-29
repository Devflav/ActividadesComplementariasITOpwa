<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpleadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleado', function (Blueprint $table) {
            $table->id('id_empleado')->autoIncrement();
            $table->foreign('id_persona')->references('id_persona')->on('persona');
            $table->foreign('id_depto')->references('id_depto')->on('departamento');
            $table->foreign('id_grado')->references('id_grado')->on('grado');
            $table->foreign('id_puesto')->references('id_puesto')->on('puesto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empleado');
    }
}
