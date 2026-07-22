<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NormasCrawlerService;
use App\Services\RagService;
use App\Models\NormaTecnica;
use Illuminate\Support\Facades\Log;

class SyncNormasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rag:sync-normas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca novas normas técnicas (Crawler) e as envia para o banco vetorial do RAG (Ingestão)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(NormasCrawlerService $crawlerService, RagService $ragService)
    {
        $this->info('Iniciando sincronização de normas técnicas (RAG)...');
        Log::info('SyncNormasCommand: Iniciando.');

        // 1. Crawler busca novos PDFs
        $novasAdicionadas = $crawlerService->buscarNovasNormas();
        $this->info("Crawler finalizado. $novasAdicionadas novas normas encontradas.");

        // 2. Ingestão (Indexação das pendentes)
        $normasPendentes = NormaTecnica::where('indexado_rag', false)->get();
        $this->info("Encontradas {$normasPendentes->count()} normas aguardando ingestão vetorial.");

        foreach ($normasPendentes as $norma) {
            $this->info("Indexando Norma ID: {$norma->id} - {$norma->titulo}...");
            $ragService->ingestaoVetorial($norma);
        }

        $this->info('Processo de RAG concluído com sucesso.');
        Log::info('SyncNormasCommand: Concluído.');

        return 0;
    }
}
