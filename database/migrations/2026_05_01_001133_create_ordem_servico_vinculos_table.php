<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdemServicoVinculosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ordem_servico_vinculos', function(Blueprint $table)
		{
			$table->bigInteger('ordemServico_id')->unsigned();
			$table->integer('servico_id');
			$table->decimal('valor', 10)->nullable();
			$table->string('reembolso', 50)->nullable();
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
		Schema::drop('ordem_servico_vinculos');
	}
}
