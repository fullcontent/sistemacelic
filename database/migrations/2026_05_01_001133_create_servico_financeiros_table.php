<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicoFinanceirosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('servico_financeiros', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('servico_id');
			$table->double('valorTotal', 8, 2);
			$table->double('valorFaturado', 8, 2)->nullable();
			$table->double('valorFaturar', 8, 2)->nullable();
			$table->double('valorAberto', 8, 2)->nullable();
			$table->string('status')->default('aberto');
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
		Schema::drop('servico_financeiros');
	}
}
