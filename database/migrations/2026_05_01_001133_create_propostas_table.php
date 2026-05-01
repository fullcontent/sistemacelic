<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropostasTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('propostas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->integer('proposta')->nullable();
			$table->string('status')->nullable()->index('idx_propostas_status');
			$table->timestamp('sent_to_analysis_at')->nullable();
			$table->timestamp('approved_at')->nullable();
			$table->timestamp('refused_at')->nullable();
			$table->string('faturamento')->nullable();
			$table->integer('responsavel_id')->nullable();
			$table->integer('unidade_id')->nullable();
			$table->integer('empresa_id')->nullable();
			$table->double('valorTotal', 10, 2)->nullable();
			$table->string('solicitante')->nullable();
			$table->text('documentos')->nullable();
			$table->text('condicoesGerais')->nullable();
			$table->text('condicoesPagamento')->nullable();
			$table->text('dadosPagamento')->nullable();
			$table->integer('dadosCastro_id')->nullable()->default(1);
			$table->index(['created_at','sent_to_analysis_at','approved_at'], 'idx_propostas_dates');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('propostas');
	}
}
