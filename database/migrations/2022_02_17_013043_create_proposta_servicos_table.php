<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropostaServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposta_servicos', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('proposta_id');
            
            
            $table->text('servico')->nullable();
            $table->text('escopo')->nullable();
            $table->float('valor',10,2)->nullable();

            
            
            $table->integer('posicao')->nullable();
            $table->integer('servicoPrincipal')->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proposta_servicos');
    }
}
