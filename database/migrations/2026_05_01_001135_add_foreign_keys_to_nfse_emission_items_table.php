<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToNfseEmissionItemsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('nfse_emission_items', function(Blueprint $table)
		{
			$table->foreign('nfse_emission_id')->references('id')->on('nfse_emissions')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('nfse_emission_items', function(Blueprint $table)
		{
			$table->dropForeign('nfse_emission_items_nfse_emission_id_foreign');
		});
	}
}
