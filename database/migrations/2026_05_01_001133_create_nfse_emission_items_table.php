<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNfseEmissionItemsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('nfse_emission_items', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('nfse_emission_id')->unsigned()->index('nfse_emission_items_nfse_emission_id_foreign');
			$table->integer('servico_id')->unsigned()->nullable();
			$table->integer('faturamento_servico_id')->unsigned()->nullable();
			$table->string('cnpj_tomador')->nullable();
			$table->text('descricao_servico')->nullable();
			$table->decimal('valor_servico', 14)->nullable();
			$table->string('numero_nf')->nullable();
			$table->string('external_id')->nullable()->index('idx_nfse_items_external_id');
			$table->string('status')->default('pendente');
			$table->string('pdf_path')->nullable();
			$table->string('xml_path')->nullable();
			$table->text('mensagem_erro')->nullable();
			$table->longText('additional_data')->nullable();
			$table->text('pdf_url')->nullable();
			$table->text('xml_url')->nullable();
			$table->timestamps();
			$table->index(['servico_id','status'], 'idx_nfse_items_servico_status');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('nfse_emission_items');
	}
}
