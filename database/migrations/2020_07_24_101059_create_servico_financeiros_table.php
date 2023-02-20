<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicoFinanceirosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servico_financeiros', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('servico_id');
            $table->float('valorTotal');
            $table->float('valorFaturado');
            $table->float('valorFaturar');
            $table->float('valorAberto');
            $table->string('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servico_financeiros');
    }
}
