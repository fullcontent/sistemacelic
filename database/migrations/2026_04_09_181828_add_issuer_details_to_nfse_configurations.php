<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIssuerDetailsToNfseConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfse_configurations', function (Blueprint $table) {
            $table->string('inscricao_municipal')->nullable();
            $table->string('email_emitente')->nullable();
            $table->string('telefone_emitente')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('codigo_cidade')->default('4202008'); // IBGE Balneário Camboriú
            $table->string('cep')->nullable();
            $table->string('uf')->default('SC');
            $table->integer('regime_tributario')->default(1); // 1 = Simples Nacional, etc
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nfse_configurations', function (Blueprint $table) {
            $table->dropColumn([
                'inscricao_municipal', 'email_emitente', 'telefone_emitente',
                'logradouro', 'numero', 'bairro', 'codigo_cidade', 'cep', 'uf', 'regime_tributario'
            ]);
        });
    }
}
