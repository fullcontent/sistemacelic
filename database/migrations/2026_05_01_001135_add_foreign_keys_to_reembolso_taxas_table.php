<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToReembolsoTaxasTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('reembolso_taxas', function(Blueprint $table)
		{
			$table->foreign('reembolso_id')->references('id')->on('reembolsos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('taxa_id')->references('id')->on('taxas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('reembolso_taxas', function(Blueprint $table)
		{
			$table->dropForeign('reembolso_taxas_reembolso_id_foreign');
			$table->dropForeign('reembolso_taxas_taxa_id_foreign');
		});
	}
}
