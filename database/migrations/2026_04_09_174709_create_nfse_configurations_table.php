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
            
            $table->string('emitir_como')->default('Prestador');
            $table->string('regime_apuracao')->nullable();
            $table->string('tomador_servico')->default('Brasil');
            $table->string('intermediario_servico')->default('Intermediario nao informado');
            $table->string('local_prestacao')->default('Brasil');
            $table->string('municipio_prestacao')->default('Balneário Camboriú/SC');
            $table->string('codigo_tributacao_nacional')->default('17.02.02');
            $table->boolean('suspensao_exigibilidade_issqn')->default(false);
            $table->string('item_nbs')->default('118064000');
            $table->boolean('issqn_exigibilidade_suspensa')->default(false);
            $table->boolean('issqn_retido')->default(false);
            $table->boolean('beneficio_municipal')->default(false);
            $table->string('pis_cofins_situacao')->default('00');
            $table->decimal('aliquota_simples_nacional', 5, 2)->default(9.90);
            
            $table->timestamps();
            
            // Skip foreign key if dados_castros has no PK
            // $table->foreign('dados_castro_id')->references('id')->on('dados_castros')->onDelete('cascade');
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
