<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicoLpusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servico_lpus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('empresa_id');

            $table->string('tipo');
            $table->string('documento');
            $table->string('tipoProcesso');
            $table->float('valor');
            



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
        Schema::dropIfExists('servico_lpus');
    }
}
