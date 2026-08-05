<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateAnaliseProtocoloLaudosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analise_protocolo_laudos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('servico_id')->unsigned()->index();
            $table->string('status'); // em_andamento, aprovada, com_exigencia
            $table->string('descricao')->nullable();
            $table->timestamps();

            $table->foreign('servico_id')->references('id')->on('servicos')->onDelete('cascade');
        });

        Schema::create('protocolos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('analise_protocolo_laudo_id')->unsigned()->index();
            $table->string('numero')->nullable();
            $table->date('data_protocolo')->nullable();
            $table->string('anexo')->nullable();
            $table->string('tipo')->nullable(); // inicial, reapresentacao
            $table->timestamps();

            $table->foreign('analise_protocolo_laudo_id')->references('id')->on('analise_protocolo_laudos')->onDelete('cascade');
        });

        Schema::create('laudos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('analise_protocolo_laudo_id')->unsigned()->index();
            $table->string('numero')->nullable();
            $table->date('data_emissao')->nullable();
            $table->string('anexo')->nullable();
            $table->timestamps();

            $table->foreign('analise_protocolo_laudo_id')->references('id')->on('analise_protocolo_laudos')->onDelete('cascade');
        });

        Schema::create('documentos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('analise_protocolo_laudo_id')->unsigned()->index();
            $table->string('nome');
            $table->string('arquivo');
            $table->string('tipo')->nullable(); // juntada, comprovante
            $table->timestamps();

            $table->foreign('analise_protocolo_laudo_id')->references('id')->on('analise_protocolo_laudos')->onDelete('cascade');
        });

        // Data Migration: Import existing services data safely in chunks
        DB::table('servicos')->orderBy('id')->chunk(200, function ($servicos) {
            foreach ($servicos as $servico) {
                $hasProtocol = !empty($servico->protocolo_numero) || !empty($servico->protocolo_anexo);
                $hasLaudo = !empty($servico->laudo_numero) || !empty($servico->laudo_anexo);

                if ($hasProtocol || $hasLaudo) {
                    $status = 'em_andamento';
                    if ($servico->situacao === 'finalizado') {
                        $status = 'aprovada';
                    } elseif ($hasLaudo) {
                        $status = 'com_exigencia';
                    }

                    $analiseId = DB::table('analise_protocolo_laudos')->insertGetId([
                        'servico_id' => $servico->id,
                        'status' => $status,
                        'descricao' => 'Primeira Análise',
                        'created_at' => $servico->created_at ?: Carbon::now(),
                        'updated_at' => $servico->updated_at ?: Carbon::now(),
                    ]);

                    if ($hasProtocol) {
                        DB::table('protocolos')->insert([
                            'analise_protocolo_laudo_id' => $analiseId,
                            'numero' => $servico->protocolo_numero,
                            'data_protocolo' => $servico->protocolo_emissao,
                            'anexo' => $servico->protocolo_anexo,
                            'tipo' => 'inicial',
                            'created_at' => $servico->created_at ?: Carbon::now(),
                            'updated_at' => $servico->updated_at ?: Carbon::now(),
                        ]);
                    }

                    if ($hasLaudo) {
                        DB::table('laudos')->insert([
                            'analise_protocolo_laudo_id' => $analiseId,
                            'numero' => $servico->laudo_numero,
                            'data_emissao' => $servico->laudo_emissao,
                            'anexo' => $servico->laudo_anexo,
                            'created_at' => $servico->created_at ?: Carbon::now(),
                            'updated_at' => $servico->updated_at ?: Carbon::now(),
                        ]);
                    }
                }
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
        Schema::dropIfExists('documentos');
        Schema::dropIfExists('laudos');
        Schema::dropIfExists('protocolos');
        Schema::dropIfExists('analise_protocolo_laudos');
    }
}
