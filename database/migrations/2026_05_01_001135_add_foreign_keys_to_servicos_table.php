<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToServicosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('servicos', function(Blueprint $table)
		{
			$table->foreign('empresa_id')->references('id')->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('responsavel_id')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('unidade_id')->references('id')->on('unidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('servicos', function(Blueprint $table)
		{
			$table->dropForeign('servicos_empresa_id_foreign');
			$table->dropForeign('servicos_responsavel_id_foreign');
			$table->dropForeign('servicos_unidade_id_foreign');
		});
	}
}
