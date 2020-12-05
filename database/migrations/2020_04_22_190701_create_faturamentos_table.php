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
           
        $table->increments('id');
            $table->timestamps();

            $table->integer('empresa_id');


           $table->string('nome')->nullable();
            $table->float('valorTotal')->nullable();
            $table->string('nf')->nullable();
            $table->string('obs')->nullable();

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
