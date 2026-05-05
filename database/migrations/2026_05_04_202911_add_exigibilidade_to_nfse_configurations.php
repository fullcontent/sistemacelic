<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExigibilidadeToNfseConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfse_configurations', function (Blueprint $table) {
            $table->integer('tipo_tributacao_iss')->nullable()->comment('1=Tributável, 2=Fora, 3=Isenção, 4=Imunidade, 5=Susp. Jud., 6=Susp. Adm., 7=Não Incidência, 8=Exportação');
            $table->integer('exigibilidade_iss')->nullable()->comment('1=Exigível, 2=Imunidade, 3=Isenção, 4=Não Incidência, 5=Susp. Jud., 6=Susp. Adm.');
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
            $table->dropColumn(['tipo_tributacao_iss', 'exigibilidade_iss']);
        });
    }
}
