<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidades', function (Blueprint $table) {
            $table->increments('id');
           

            $table->unsignedInteger('empresa_id')->unsigned();
            $table->foreign('empresa_id')->references('id')->on('empresas');

            $table->string('nomeFantasia');
            $table->string('razaoSocial');
            $table->string('cnpj');
            $table->string('inscricaoEst');
            $table->string('inscricaoMun');
            $table->string('inscricaoImo');

            $table->string('status');
           
            $table->string('matriculaRI')->nullable();
            $table->string('tipoImovel');
            $table->string('codigo');
            $table->string('area')->nullable();
            
            $table->string('cidade');
            $table->string('uf',2);
            $table->string('endereco');
            $table->string('numero');
            $table->string('complemento');
            $table->string('cep');
            $table->string('bairro');
            
            $table->string('telefone')->nullable();
            $table->string('responsavel')->nullable();
            $table->string('email');

            
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
        Schema::dropIfExists('unidades');
    }
}
