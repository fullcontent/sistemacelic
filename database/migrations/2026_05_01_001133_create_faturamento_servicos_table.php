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
		Schema::create('faturamento_servicos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('servico_id')->unsigned()->index('faturamento_servicos_servico_id_foreign');
			$table->integer('faturamento_id')->unsigned()->index('faturamento_servicos_faturamento_id_foreign');
			$table->timestamps();
			$table->double('valorFaturado', 8, 2);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('faturamento_servicos');
	}
}
