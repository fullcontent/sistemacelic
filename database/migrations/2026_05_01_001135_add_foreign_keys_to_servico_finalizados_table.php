<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToServicoFinalizadosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('servico_finalizados', function(Blueprint $table)
		{
			$table->foreign('servico_id')->references('id')->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('servico_finalizados', function(Blueprint $table)
		{
			$table->dropForeign('servico_finalizados_servico_id_foreign');
		});
	}
}
