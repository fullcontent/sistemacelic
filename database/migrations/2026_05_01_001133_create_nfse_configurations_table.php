<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNfseConfigurationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('nfse_configurations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('dados_castro_id')->unsigned()->nullable();
			$table->string('provider')->default('plugnotas');
			$table->string('emit_as');
			$table->string('simples_regime');
			$table->string('tomador_tipo');
			$table->string('intermediario_tipo')->nullable()->default('Intermediario nao informado');
			$table->string('local_prestacao')->nullable()->default('Brasil');
			$table->string('municipio_nome');
			$table->string('municipio_ibge')->nullable();
			$table->string('codigo_tributacao_nacional');
			$table->boolean('suspensao_exigibilidade_issqn')->default('0');
			$table->string('item_nbs');
			$table->boolean('issqn_exigibilidade_suspensa')->default('0');
			$table->boolean('issqn_retido')->default('0');
			$table->boolean('beneficio_municipal')->default('0');
			$table->string('pis_cofins_situacao');
			$table->decimal('aliquota_simples', 6)->default(9.90);
			$table->decimal('valor_aproximado_tributos', 10)->nullable();
			$table->boolean('ativo')->default('1');
			$table->string('inscricao_municipal')->nullable();
			$table->string('email_emitente')->nullable();
			$table->string('telefone_emitente')->nullable();
			$table->string('logradouro')->nullable();
			$table->string('numero')->nullable();
			$table->string('bairro')->nullable();
			$table->string('codigo_cidade')->nullable()->default('4202008');
			$table->string('cep')->nullable();
			$table->string('uf')->nullable()->default('SC');
			$table->integer('regime_tributario')->nullable()->default(1);
			$table->string('login_prefeitura')->nullable();
			$table->string('senha_prefeitura')->nullable();
			$table->string('certificado')->nullable();
			$table->boolean('producao')->nullable()->default('0');
			$table->timestamps();
			$table->boolean('plugnotas_empresa_sincronizada')->default('0');
			$table->datetime('plugnotas_empresa_sync_at')->nullable();
			$table->text('plugnotas_empresa_sync_error')->nullable();
			$table->index(['dados_castro_id','ativo'], 'idx_nfse_config_dados_ativo');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('nfse_configurations');
	}
}
