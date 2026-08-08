<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIssue478Phase3Fields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pendencias', function (Blueprint $table) {
            if (!Schema::hasColumn('pendencias', 'responsavel_cliente_id')) {
                $table->integer('responsavel_cliente_id')->nullable()->after('responsavel_id');
            }
            if (!Schema::hasColumn('pendencias', 'respondida_em')) {
                $table->timestamp('respondida_em')->nullable()->after('status');
            }
        });

        Schema::table('arquivos', function (Blueprint $table) {
            if (!Schema::hasColumn('arquivos', 'pendencia_id')) {
                $table->integer('pendencia_id')->nullable()->after('servico_id');
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
        Schema::table('pendencias', function (Blueprint $table) {
            if (Schema::hasColumn('pendencias', 'responsavel_cliente_id')) {
                $table->dropColumn('responsavel_cliente_id');
            }
            if (Schema::hasColumn('pendencias', 'respondida_em')) {
                $table->dropColumn('respondida_em');
            }
        });

        Schema::table('arquivos', function (Blueprint $table) {
            if (Schema::hasColumn('arquivos', 'pendencia_id')) {
                $table->dropColumn('pendencia_id');
            }
        });
    }
}
