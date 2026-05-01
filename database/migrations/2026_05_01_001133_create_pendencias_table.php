<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendenciasTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pendencias', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->integer('created_by');
			$table->integer('servico_id');
			$table->string('pendencia');
			$table->date('vencimento')->nullable();
			$table->string('responsavel_tipo');
			$table->integer('responsavel_id')->nullable();
			$table->string('status');
			$table->text('observacoes')->nullable();
			$table->timestamps();
			$table->integer('prioridade')->nullable()->default(0);
			$table->integer('vinculo')->nullable();
			$table->integer('etapa')->nullable()->default(1);
			$table->integer('vinculoPendencia')->nullable();
			$table->date('dataLimite')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pendencias');
	}
}
