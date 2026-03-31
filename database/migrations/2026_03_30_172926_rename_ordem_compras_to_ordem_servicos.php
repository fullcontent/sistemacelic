<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameOrdemComprasToOrdemServicos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('ordem_compras', 'ordem_servicos');
        Schema::rename('ordem_compra_pagamentos', 'ordem_servico_pagamentos');
        Schema::rename('ordem_compra_vinculos', 'ordem_servico_vinculos');

        Schema::table('ordem_servico_pagamentos', function (Blueprint $table) {
            $table->renameColumn('ordemCompra_id', 'ordemServico_id');
        });

        Schema::table('ordem_servico_vinculos', function (Blueprint $table) {
            $table->renameColumn('ordemCompra_id', 'ordemServico_id');
        });

        Schema::table('prestador_comentarios', function (Blueprint $table) {
            $table->renameColumn('ordemCompra_id', 'ordemServico_id');
        });
    }

    public function down()
    {
        Schema::table('prestador_comentarios', function (Blueprint $table) {
            $table->renameColumn('ordemServico_id', 'ordemCompra_id');
        });

        Schema::table('ordem_servico_vinculos', function (Blueprint $table) {
            $table->renameColumn('ordemServico_id', 'ordemCompra_id');
        });

        Schema::table('ordem_servico_pagamentos', function (Blueprint $table) {
            $table->renameColumn('ordemServico_id', 'ordemCompra_id');
        });

        Schema::rename('ordem_servico_vinculos', 'ordem_compra_vinculos');
        Schema::rename('ordem_servico_pagamentos', 'ordem_compra_pagamentos');
        Schema::rename('ordem_servicos', 'ordem_compras');
    }
}
