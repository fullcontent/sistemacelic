<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicoFinalizadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servico_finalizados', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('servico_id');
            $table->foreign('servico_id')->references('id')->on('servicos');

            $table->date('finalizado');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servico_finalizados');
    }
}
