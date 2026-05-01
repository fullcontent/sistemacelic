<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdemServicosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ordem_servicos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable();
			$table->integer('prestador_id')->nullable();
			$table->integer('servico_id')->nullable();
			$table->decimal('valorServico', 10)->nullable();
			$table->string('situacao')->nullable();
			$table->string('formaPagamento')->nullable();
			$table->text('escopo')->nullable();
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
		Schema::drop('ordem_servicos');
	}
}
