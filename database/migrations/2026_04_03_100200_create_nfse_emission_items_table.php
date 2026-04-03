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
        Schema::create('nfse_emission_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('nfse_emission_id');
            $table->unsignedInteger('servico_id')->nullable();
            $table->unsignedInteger('faturamento_servico_id')->nullable();

            $table->string('cnpj_tomador')->nullable();
            $table->text('descricao_servico')->nullable();
            $table->decimal('valor_servico', 14, 2)->nullable();

            $table->string('numero_nf')->nullable();
            $table->string('external_id')->nullable();
            $table->string('status')->default('pendente');
            $table->string('pdf_path')->nullable();
            $table->string('xml_path')->nullable();
            $table->text('mensagem_erro')->nullable();
            $table->longText('additional_data')->nullable();
            $table->timestamps();

            $table->foreign('nfse_emission_id')->references('id')->on('nfse_emissions');
            $table->index(['servico_id', 'status'], 'idx_nfse_items_servico_status');
            $table->index(['external_id'], 'idx_nfse_items_external_id');
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
    }
}
