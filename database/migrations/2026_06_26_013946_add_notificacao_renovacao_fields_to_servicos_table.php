<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificacaoRenovacaoFieldsToServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->boolean('ativar_notificacao_renovacao')->default(false);
            $table->integer('dias_para_notificacao_renovacao')->nullable();
            $table->timestamp('notificacao_renovacao_enviada_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->dropColumn([
                'ativar_notificacao_renovacao',
                'dias_para_notificacao_renovacao',
                'notificacao_renovacao_enviada_at'
            ]);
        });
    }
}
