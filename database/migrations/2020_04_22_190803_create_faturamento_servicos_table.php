<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaturamentoServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faturamento_servicos', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

               

                $table->unsignedInteger('servico_id');
                $table->foreign('servico_id')->references('id')->on('servicos');

                $table->unsignedInteger('faturamento_id');
                $table->foreign('faturamento_id')->references('id')->on('faturamentos');

                $table->float('valorFaturado');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faturamento_servicos');
    }
}
