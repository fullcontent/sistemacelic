<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrefeituraCredentialsToNfseConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfse_configurations', function (Blueprint $table) {
            $table->string('login_prefeitura')->nullable();
            $table->string('senha_prefeitura')->nullable();
            $table->string('certificado')->nullable();
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
            $table->dropColumn(['login_prefeitura', 'senha_prefeitura', 'certificado']);
        });
    }
}
