<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdemServicoPagamentosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ordem_servico_pagamentos', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('ordemServico_id')->unsigned();
			$table->decimal('valor', 10)->nullable();
			$table->date('dataPagamento')->nullable();
			$table->date('dataVencimento')->nullable();
			$table->string('formaPagamento')->nullable();
			$table->integer('parcela')->nullable();
			$table->string('comprovante')->nullable();
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
		Schema::drop('ordem_servico_pagamentos');
	}
}
