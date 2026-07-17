<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->client = new Client([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 10.0,
        ]);
    }

    public function generateContextualSummary(array $serviceData, string $observacoes)
    {
        if (empty($this->apiKey)) {
            Log::warning('OpenAIService: OpenAI API Key is missing. Using fallback summary.');
            return $this->getFallbackSummary($serviceData, $observacoes);
        }

        try {
            $prompt = "Você é um assistente do Sistema Celic. "
                . "Gere um resumo contextualizado, conciso e profissional em uma frase sobre a seguinte interação/histórico de serviço. "
                . "O resumo será exibido em notificações para os usuários mencionados.\n\n"
                . "Dados do Serviço:\n"
                . "- Nome: " . ($serviceData['nome'] ?? 'N/A') . "\n"
                . "- Unidade: " . ($serviceData['unidade'] ?? 'N/A') . "\n"
                . "- Situação: " . ($serviceData['situacao'] ?? 'N/A') . "\n"
                . "- Tipo: " . ($serviceData['tipo'] ?? 'N/A') . "\n\n"
                . "Interação:\n"
                . '"' . $observacoes . '"';

            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Você é um assistente do Sistema Celic.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => 150,
                    'temperature' => 0.5,
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            $summary = $body['choices'][0]['message']['content'] ?? '';
            return trim($summary);

        } catch (\Exception $e) {
            Log::error('OpenAIService: Error generating summary: ' . $e->getMessage());
            return $this->getFallbackSummary($serviceData, $observacoes);
        }
    }

    protected function getFallbackSummary(array $serviceData, string $observacoes)
    {
        $preview = mb_strimwidth($observacoes, 0, 80, '...');
        return "Nova interação no serviço " . ($serviceData['nome'] ?? '') . ": " . $preview;
    }
}
