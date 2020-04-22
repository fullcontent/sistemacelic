<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaturamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faturamentos', function (Blueprint $table) {
            $table->integer('id');
            $table->timestamps();

            $table->integer('cliente_id');


            $table->float('valorFaturado',10,2)->nullable();
            $table->float('valorFaturar',10,2)->nullable();
            $table->float('valorAberto',10,2)->nullable();

            $table->string('nf')->nullable();

            $table->date('dataPagamento')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faturamentos');
    }
}
