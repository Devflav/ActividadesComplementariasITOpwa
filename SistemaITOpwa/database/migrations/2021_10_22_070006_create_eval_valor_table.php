<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvalValorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eval_valor', function (Blueprint $table) {
            $table->foreign('id_evaluacion')->references('id_evaluacion')->on('evaluacion');
            $table->foreign('id_crit_eval')->references('id_crit_eval')->on('criterios_evaluacion');
            $table->foreign('id_desempenio')->references('id_desempenio')->on('desempenio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eval_valor');
    }
}
