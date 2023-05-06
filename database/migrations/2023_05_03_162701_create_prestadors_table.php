<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrestadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prestadors', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('nome')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('contato')->nullable();
            $table->string('qualificacao')->nullable();
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();

            $table->string('cidadeAtuacao')->nullable();
            $table->string('ufAtuacao')->nullable();

            $table->string('chavePix')->nullable();
            $table->string('tipoChave')->nullable();
            $table->string('banco')->nullable();
            $table->string('agencia')->nullable();
            $table->string('conta')->nullable();
            $table->string('formaPagamento')->nullable();

            $table->string('tomadorNome')->nullable();
            $table->string('tomadorCnpj')->nullable();


            $table->string('cnpjVinculado')->nullable();
            $table->string('razaoSocial')->nullable();
            $table->text('obs')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prestadors');
    }
}
