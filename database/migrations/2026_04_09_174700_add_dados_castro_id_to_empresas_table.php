<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDadosCastroIdToEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
            if (!Schema::hasColumn('empresas', 'dados_castro_id')) {
                $table->unsignedInteger('dados_castro_id')->nullable()->after('status');
            }
            
            // Assuming dados_castros is the table name for DadosCastro model
            // We'll skip the foreign key constraint if the table doesn't have a PK
            // $table->foreign('dados_castro_id')->references('id')->on('dados_castros')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['dados_castro_id']);
            $table->dropColumn('dados_castro_id');
        });
    }
}
