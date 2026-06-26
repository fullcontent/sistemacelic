<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibilidadeAndPendenciaToHistoricosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historicos', function (Blueprint $table) {
            $table->string('visibilidade')->default('publico')->after('observacoes');
            $table->integer('pendencia_id')->unsigned()->nullable()->index()->after('visibilidade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historicos', function (Blueprint $table) {
            $table->dropColumn(['visibilidade', 'pendencia_id']);
        });
    }
}
