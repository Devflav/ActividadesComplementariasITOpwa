<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id')->autoIncrement();
            $table->foreign('id_persona')->references('id_persona')->on('persona');
            $table->foreign('id_puesto')->references('id_puesto')->on('puesto');
            $table->string('nombre', 80);
            $table->string('usuario', 26);
            $table->string('password', 250);
            $table->date('fecha_registro');
            $table->tinyInteger('edo_sesion');
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
        Schema::dropIfExists('users');
    }
}
