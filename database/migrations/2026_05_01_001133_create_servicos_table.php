<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('servicos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('responsavel_id')->unsigned()->nullable()->index('servicos_responsavel_id_foreign');
			$table->integer('empresa_id')->unsigned()->nullable()->index('servicos_empresa_id_foreign');
			$table->integer('unidade_id')->unsigned()->nullable()->index('servicos_unidade_id_foreign');
			$table->timestamps();
			$table->string('tipo');
			$table->string('nome');
			$table->string('os');
			$table->string('protocolo_anexo')->nullable();
			$table->string('protocolo_numero')->nullable();
			$table->date('protocolo_emissao')->nullable();
			$table->string('licenca_anexo')->nullable();
			$table->date('licenca_emissao')->nullable();
			$table->date('licenca_validade')->nullable();
			$table->integer('coresponsavel_id')->unsigned()->nullable();
			$table->integer('analista1_id')->nullable();
			$table->integer('analista2_id')->nullable();
			$table->string('situacao');
			$table->text('observacoes')->nullable();
			$table->string('laudo_numero')->nullable();
			$table->date('laudo_emissao')->nullable();
			$table->string('laudo_anexo')->nullable();
			$table->string('solicitante', 50)->nullable();
			$table->string('departamento', 50)->nullable();
			$table->integer('servico_lpu')->nullable();
			$table->string('tipoLicenca', 50)->nullable()->default('renovavel');
			$table->text('escopo')->nullable();
			$table->string('proposta', 50)->nullable();
			$table->integer('servicoPrincipal')->nullable();
			$table->integer('propostaServico_id')->nullable();
			$table->integer('proposta_id')->nullable();
			$table->string('nf', 50)->nullable();
			$table->string('licenciamento', 50)->nullable();
			$table->date('dataFinal')->nullable();
			$table->date('dataLimiteCiclo')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('servicos');
	}
}
