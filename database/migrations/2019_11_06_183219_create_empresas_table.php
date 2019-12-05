<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->increments('id');

            $table->string('nomeFantasia');
            $table->string('razaoSocial');
            $table->string('cnpj');
            $table->string('inscricaoEst');
            $table->string('inscricaoMun');
            $table->string('inscricaoImo');
           
            $table->string('matriculaRI');
            $table->string('tipoImovel');
            $table->string('codigo');
            $table->string('area');
            
            $table->string('cidade');
            $table->string('uf',2);
            $table->string('endereco');
            $table->string('cep');
            $table->string('bairro');
            $table->string('telefone');
            $table->string('responsavel');
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
        Schema::dropIfExists('empresas');
    }
}
