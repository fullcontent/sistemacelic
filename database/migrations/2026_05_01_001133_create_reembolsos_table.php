<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReembolsosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reembolsos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->integer('empresa_id');
			$table->string('nome')->nullable();
			$table->double('valorTotal', 8, 2)->nullable();
			$table->string('obs')->nullable();
			$table->date('dataPagamento')->nullable();
			$table->integer('dadosCastro_id')->nullable()->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reembolsos');
	}
}
