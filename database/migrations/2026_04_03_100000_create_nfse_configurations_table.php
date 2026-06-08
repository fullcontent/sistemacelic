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
        Schema::create('nfse_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dados_castro_id')->nullable();

            $table->string('provider')->default('plugnotas');
            $table->string('emit_as');
            $table->string('simples_regime');
            $table->string('tomador_tipo');
            $table->string('intermediario_tipo');
            $table->string('local_prestacao')->default('Brasil');
            $table->string('municipio_nome');
            $table->string('municipio_ibge')->nullable();
            $table->string('codigo_tributacao_nacional');
            $table->boolean('suspensao_exigibilidade_issqn')->default(false);
            $table->string('item_nbs');
            $table->boolean('issqn_exigibilidade_suspensa')->default(false);
            $table->boolean('issqn_retido')->default(false);
            $table->boolean('beneficio_municipal')->default(false);
            $table->string('pis_cofins_situacao');
            $table->decimal('aliquota_simples', 6, 2)->default(9.90);
            $table->decimal('valor_aproximado_tributos', 10, 2)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['dados_castro_id', 'ativo'], 'idx_nfse_config_dados_ativo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfse_configurations');
    }
}
