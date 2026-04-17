<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlugnotasSyncStatusToNfseConfigurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nfse_configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('nfse_configurations', 'plugnotas_empresa_sincronizada')) {
                $table->boolean('plugnotas_empresa_sincronizada')->default(false)->after('producao');
            }

            if (!Schema::hasColumn('nfse_configurations', 'plugnotas_empresa_sync_at')) {
                $table->dateTime('plugnotas_empresa_sync_at')->nullable()->after('plugnotas_empresa_sincronizada');
            }

            if (!Schema::hasColumn('nfse_configurations', 'plugnotas_empresa_sync_error')) {
                $table->text('plugnotas_empresa_sync_error')->nullable()->after('plugnotas_empresa_sync_at');
            }
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
            $columns = [];

            if (Schema::hasColumn('nfse_configurations', 'plugnotas_empresa_sync_error')) {
                $columns[] = 'plugnotas_empresa_sync_error';
            }

            if (Schema::hasColumn('nfse_configurations', 'plugnotas_empresa_sync_at')) {
                $columns[] = 'plugnotas_empresa_sync_at';
            }

            if (Schema::hasColumn('nfse_configurations', 'plugnotas_empresa_sincronizada')) {
                $columns[] = 'plugnotas_empresa_sincronizada';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
}
