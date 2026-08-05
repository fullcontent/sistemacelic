<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtivoToDadosCastrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('dados_castros')) {
            Schema::table('dados_castros', function (Blueprint $table) {
                if (!Schema::hasColumn('dados_castros', 'ativo')) {
                    $table->boolean('ativo')->default(true)->after('conta');
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
        if (Schema::hasTable('dados_castros')) {
            Schema::table('dados_castros', function (Blueprint $table) {
                $table->dropColumn('ativo');
            });
        }
    }
}
