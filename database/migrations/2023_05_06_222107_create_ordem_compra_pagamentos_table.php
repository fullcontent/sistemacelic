<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdemCompraPagamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordem_compra_pagamentos', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->integer('ordemCompra_id')->nullable();
            $table->decimal('valor', 10, 2)->nullable();
            $table->date('dataPagamento')->nullable();
            $table->date('dataVencimento')->nullable();
            $table->string('formaPagamento')->nullable();
            $table->integer('parcela')->nullable();
            $table->string('anexoComprovante')->nullable();
            $table->text('obs')->nullable();
            $table->string('situacao')->nullable();
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
        Schema::dropIfExists('ordem_compra_pagamentos');
    }
}
