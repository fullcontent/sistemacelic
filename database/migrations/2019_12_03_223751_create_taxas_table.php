<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('servico_id');

            
            $table->string('nome');
            $table->float('valor',10,2);
            $table->string('boleto')->nullable();
            $table->string('comprovante')->nullable();
            $table->text('observacoes');
            $table->date('emissao');
            $table->date('vencimento');
            
            $table->string('situacao');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxas');
    }
}
