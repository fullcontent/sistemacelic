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
		Schema::create('servico_lpus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('nomeCelic');
			$table->text('nome')->nullable();
			$table->string('categoria', 50)->nullable();
			$table->string('tipoServico', 50)->nullable();
			$table->string('processo', 50)->nullable();
			$table->text('escopo')->nullable();
			$table->double('valor', 8, 2)->nullable()->default(0.00);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('servico_lpus');
	}
}
