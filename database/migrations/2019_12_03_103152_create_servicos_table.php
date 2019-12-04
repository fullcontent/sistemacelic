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

            $table->string('tipo');//Primario, Secundario;            
            $table->string('nome');
            $table->string('os');

            $table->string('protocolo_anexo')->nullable();
            $table->date('protocolo_emissao')->nullable();
            $table->date('protocolo_validade')->nullable();
            
        
            $table->string('situacao');//Finalizado, Andamento, Vencimento

            $table->text('observacoes');
            
        
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
