<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('unidades', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('empresa_id')->unsigned()->index('unidades_empresa_id_foreign');
			$table->string('nomeFantasia');
			$table->string('razaoSocial');
			$table->string('cnpj');
			$table->string('inscricaoEst')->nullable();
			$table->string('inscricaoMun')->nullable();
			$table->string('inscricaoImo')->nullable();
			$table->string('rip', 50)->nullable();
			$table->string('status');
			$table->string('matriculaRI')->nullable();
			$table->string('tipoImovel')->nullable();
			$table->string('codigo');
			$table->string('area')->nullable();
			$table->string('areaTerreno')->nullable();
			$table->string('cidade');
			$table->string('uf', 2);
			$table->string('endereco');
			$table->string('numero');
			$table->string('complemento')->nullable();
			$table->string('cep');
			$table->string('bairro');
			$table->string('telefone')->nullable();
			$table->string('responsavel')->nullable();
			$table->string('email');
			$table->timestamps();
			$table->date('dataInauguracao')->nullable();
			$table->string('latitude', 50)->nullable();
			$table->string('longitude', 50)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('unidades');
	}
}
