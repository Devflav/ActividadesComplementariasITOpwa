<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestringidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restringida', function (Blueprint $table) {
            $table->foreign('id_grupo')->references('id_grupo')->on('grupo');
            $table->foreign('id_depto')->references('id_depto')->on('departamento');
            $table->string('observaciones', 250);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restringida');
    }
}
