<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNfseEmissionsTable extends Migration
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
            $table->unsignedInteger('nfse_configuration_id')->nullable();

            $table->string('modo'); // automatico | manual | nao_emitir
            $table->string('opcao_automatica')->nullable();
            $table->string('status')->default('pendente');

            $table->longText('payload')->nullable();
            $table->longText('retorno')->nullable();
            $table->string('zip_path')->nullable();
            $table->text('observacoes')->nullable();

            $table->timestamps();

            $table->foreign('faturamento_id')->references('id')->on('faturamentos');
            $table->index(['faturamento_id', 'status'], 'idx_nfse_emissions_faturamento_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfse_emissions');
    }
}
