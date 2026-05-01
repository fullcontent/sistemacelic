<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToFaturamentoServicosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('faturamento_servicos', function(Blueprint $table)
		{
			$table->foreign('faturamento_id')->references('id')->on('faturamentos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
		Schema::table('faturamento_servicos', function(Blueprint $table)
		{
			$table->dropForeign('faturamento_servicos_faturamento_id_foreign');
			$table->dropForeign('faturamento_servicos_servico_id_foreign');
		});
	}
}
