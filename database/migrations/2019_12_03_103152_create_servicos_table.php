<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('tipo');
            $table->string('nome');
            $table->date('emissao');
            $table->date('validade');
            $table->string('protocolo');
            $table->string('situacao');
            $table->text('observacoes');
            $table->date('meta');
            $table->integer('historico');
            $table->string('pendencia');
            $table->string('acao');

            $table->unsignedInteger('empresa_id')->unsigned()->nullable();
            $table->foreign('empresa_id')->references('id')->on('empresas');

            $table->unsignedInteger('unidade_id')->unsigned()->nullable();
            $table->foreign('unidade_id')->references('id')->on('unidades');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicos');
    }
}
