<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDadosCastrosTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dados_castros', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('cnpj')->nullable();
			$table->text('razaoSocial')->nullable();
			$table->text('chavePix')->nullable();
			$table->text('banco')->nullable();
			$table->text('agencia')->nullable();
			$table->text('conta')->nullable();
			$table->boolean('ativo')->default('1');
			$table->text('nomeFantasia')->nullable();
			$table->text('email')->nullable();
			$table->text('inscricaoEstadual')->nullable();
			$table->text('inscricaoMunicipal')->nullable();
			$table->longText('endereco_json')->nullable();
			$table->longText('telefone_json')->nullable();
			$table->longText('logotipo_json')->nullable();
			$table->longText('nfse_json')->nullable();
			$table->longText('nfe_json')->nullable();
			$table->longText('nfce_json')->nullable();
			$table->longText('mdfe_json')->nullable();
			$table->longText('cfe_json')->nullable();
			$table->longText('nfcom_json')->nullable();
			$table->boolean('incentivoFiscal')->nullable();
			$table->boolean('incentivadorCultural')->nullable();
			$table->boolean('simplesNacional')->nullable();
			$table->integer('regimeTributario')->nullable();
			$table->integer('regimeTributarioEspecial')->nullable();
			$table->longText('certificado')->nullable();
			$table->string('origem_id')->nullable();
			$table->datetime('createdAt')->nullable();
			$table->datetime('updatedAt')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('dados_castros');
	}
}
