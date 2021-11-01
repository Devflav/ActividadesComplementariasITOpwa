<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRespaldoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('respaldo', function (Blueprint $table) {
            $table->id('id_respaldo')->autoIncrement();
            $table->foreign('id_empleado')->references('id_empleado')->on('empleado');
            $table->string('tipo', 30);
            $table->string('ubicacion', 300);
            $table->string('descripcion', 150);
            $table->date('fecha');
            $table->time('hora');
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
        Schema::dropIfExists('respaldo');
    }
}
