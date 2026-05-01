<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNfseEmissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('nfse_emissions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('faturamento_id')->unsigned();
			$table->integer('nfse_configuration_id')->unsigned();
			$table->string('modo');
			$table->string('opcao_automatica')->nullable();
			$table->string('status')->default('processando');
			$table->longText('payload')->nullable();
			$table->longText('retorno')->nullable();
			$table->string('zip_path')->nullable();
			$table->text('observacoes')->nullable();
			$table->text('mensagem_erro')->nullable();
			$table->decimal('valor_total', 14)->nullable()->default(0.00);
			$table->text('pdf_url')->nullable();
			$table->text('xml_url')->nullable();
			$table->string('numero_nf')->nullable();
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
		Schema::drop('nfse_emissions');
	}
}
