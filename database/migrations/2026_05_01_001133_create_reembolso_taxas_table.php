<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReembolsoTaxasTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reembolso_taxas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('reembolso_id')->unsigned()->index('reembolso_taxas_reembolso_id_foreign');
			$table->integer('taxa_id')->unsigned()->index('reembolso_taxas_taxa_id_foreign');
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
		Schema::drop('reembolso_taxas');
	}
}
