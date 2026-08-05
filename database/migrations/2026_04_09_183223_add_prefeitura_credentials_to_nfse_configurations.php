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
        if (Schema::hasTable('nfse_configurations')) {
            Schema::table('nfse_configurations', function (Blueprint $table) {
                if (!Schema::hasColumn('nfse_configurations', 'login_prefeitura')) {
                    $table->string('login_prefeitura')->nullable();
                    $table->string('senha_prefeitura')->nullable();
                    $table->string('certificado')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('nfse_configurations')) {
            Schema::table('nfse_configurations', function (Blueprint $table) {
                $table->dropColumn(['login_prefeitura', 'senha_prefeitura', 'certificado']);
            });
        }
    }
}
