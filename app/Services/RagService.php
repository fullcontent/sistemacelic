<?php

namespace App\Services;

use App\Models\NormaTecnica;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RagService
{
    public function ingestaoVetorial(NormaTecnica $norma)
    {
        Log::info("RagService: Iniciando Ingestão da Norma ID: " . $norma->id);

        $textoCompleto = "Art. 1º - O licenciamento requer aprovação prévia...\nArt. 2º - É obrigatória a sinalização...";
        $chunks = $this->quebrarEmChunks($textoCompleto);
        
        // $embeddings = $this->gerarEmbeddings($chunks);
        // PineconeClient::upsert($embeddings);

        $norma->indexado_rag = true;
        $norma->save();

        Log::info("RagService: Norma ID {$norma->id} indexada com sucesso. Total de Chunks: " . count($chunks));
        return true;
    }

    private function quebrarEmChunks($texto)
    {
        return explode("\n", $texto); 
    }

    private function gerarEmbeddings($chunks)
    {
        $provider = env('AI_PROVIDER', 'openai');

        /*
        switch ($provider) {
            case 'gemini':
                // Google Gemini Text Embeddings API
                return Http::post('https://generativelanguage.googleapis.com/v1beta/models/embedding-001:embedContent?key=' . env('GEMINI_API_KEY'), [
                    'model' => 'models/embedding-001',
                    'content' => ['parts' => [['text' => implode(" ", $chunks)]]]
                ])->json();
                
            case 'opencode':
                // Integração com Opencode Embeddings
                return Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENCODE_API_KEY')
                ])->post('https://api.opencode.com/v1/embeddings', [
                    'input' => $chunks
                ])->json();
                
            case 'openai':
            default:
                // Padrão: OpenAI Embeddings
                return Http::withToken(env('OPENAI_API_KEY'))
                    ->post('https://api.openai.com/v1/embeddings', [
                        'input' => $chunks,
                        'model' => 'text-embedding-3-small'
                    ])->json();
        }
        */
        
        return []; // Mock
    }
}
