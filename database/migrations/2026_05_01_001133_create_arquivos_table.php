<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArquivosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('arquivos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->integer('empresa_id')->nullable();
			$table->integer('unidade_id')->nullable();
			$table->integer('servico_id')->nullable();
			$table->string('arquivo')->nullable();
			$table->string('nome');
			$table->integer('pendencia_id')->nullable();
			$table->integer('user_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('arquivos');
	}
}
