<?php

namespace App\Services;

use App\Models\NormaTecnica;
use Illuminate\Support\Facades\Log;

class NormasCrawlerService
{
    /**
     * Simula um crawler que busca novas legislações.
     * Em produção, isso faria requests para os Diários Oficiais ou APIs Governamentais.
     */
    public function buscarNovasNormas()
    {
        Log::info("NormasCrawlerService: Iniciando busca por novas normas técnicas...");

        // Array simulando as normas encontradas pelo crawler hoje
        $normasEncontradas = [
            [
                'orgao' => 'Corpo de Bombeiros',
                'estado' => 'SP',
                'titulo' => 'Instrução Técnica nº 01/2026 - Procedimentos Administrativos',
                'data_publicacao' => '2026-07-22',
                'arquivo_path' => 'normas/sp_cbpm_it01_2026.pdf',
            ],
            [
                'orgao' => 'Vigilância Sanitária',
                'municipio' => 'São Paulo',
                'estado' => 'SP',
                'titulo' => 'Portaria Municipal CVS 2026 - Licenciamento',
                'data_publicacao' => '2026-07-21',
                'arquivo_path' => 'normas/sp_cvs_portaria_2026.pdf',
            ]
        ];

        $novasAdicionadas = 0;

        foreach ($normasEncontradas as $dados) {
            $existe = NormaTecnica::where('titulo', $dados['titulo'])->exists();
            if (!$existe) {
                NormaTecnica::create($dados);
                $novasAdicionadas++;
            }
        }

        Log::info("NormasCrawlerService: Finalizado. $novasAdicionadas novas normas adicionadas.");
        return $novasAdicionadas;
    }
}
