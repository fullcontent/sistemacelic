<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicoFinalizadosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('servico_finalizados', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('servico_id')->unsigned()->index('servico_finalizados_servico_id_foreign');
			$table->timestamp('finalizado')->useCurrent();
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
		Schema::drop('servico_finalizados');
	}
}
