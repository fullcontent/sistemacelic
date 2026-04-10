<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNfseEmissionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nfse_emissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('faturamento_id');
            $table->unsignedInteger('dados_castro_id');
            
            $table->string('status')->default('pendente'); // pendente, processando, concluido, erro, cancelado, manual, nao_emitir
            $table->string('numero_nf')->nullable();
            $table->string('tipo_emissao')->default('automatica'); // automatica, manual, nao_emitir
            
            $table->string('plugnotas_id')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('xml_path')->nullable();
            
            $table->decimal('valor_total', 15, 2)->default(0);
            
            // Campos Manuais
            $table->string('responsabilidade_tecnica')->nullable();
            $table->string('documento_referencia')->nullable();
            $table->text('informacoes_complementares')->nullable();
            $table->string('numero_pedido_os')->nullable();
            
            $table->json('log_api')->nullable();
            
            $table->timestamps();
            
            $table->foreign('faturamento_id')->references('id')->on('faturamentos')->onDelete('cascade');
            // $table->foreign('dados_castro_id')->references('id')->on('dados_castros')->onDelete('cascade');
        });

        Schema::create('nfse_emission_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('nfse_emission_id');
            $table->unsignedInteger('servico_id');
            $table->decimal('valor_item', 15, 2)->default(0);
            
            $table->timestamps();
            
            $table->foreign('nfse_emission_id')->references('id')->on('nfse_emissions')->onDelete('cascade');
            $table->foreign('servico_id')->references('id')->on('servicos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfse_emission_items');
        Schema::dropIfExists('nfse_emissions');
    }
}
