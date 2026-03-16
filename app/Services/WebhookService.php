<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    protected $client;
    protected $webhookUrl;

    public function __construct()
    {
        $this->client = new Client();
        // Definir a URL do webhook no .env como WEBHOOK_EMAIL_URL
        $this->webhookUrl = env('WEBHOOK_EMAIL_URL');
    }

    public function sendMentionEmail($user, $servico, $resumo, $interactionText = '')
    {
        if (!$this->webhookUrl) {
            Log::warning('WebhookService: URL do webhook não configurada (WEBHOOK_EMAIL_URL).');
            return false;
        }

        try {
            $response = $this->client->post($this->webhookUrl, [
                'json' => [
                    'event' => 'user_mentioned',
                    'to_email' => $user->email,
                    'to_name' => $user->name,
                    'servico' => [
                        'id' => $servico->id,
                        'os' => $servico->os,
                        'nome' => $servico->nome,
                        'situacao' => $servico->situacao,
                        'tipo' => $servico->tipo,
                        'unidade' => $servico->unidade ? $servico->unidade->nomeFantasia : 'N/A',
                        'cliente' => $servico->unidade && $servico->unidade->empresa ? $servico->unidade->empresa->nomeFantasia : 'N/A',
                        'link' => route($user->privileges == 'admin' ? 'servicos.show' : 'cliente.servico.show', $servico->id)
                    ],
                    'interaction' => [
                        'text' => $interactionText,
                        'ai_summary' => $resumo
                    ],
                    'system_context' => [
                        'app_name' => config('app.name'),
                        'app_url' => config('app.url'),
                        'timestamp' => now()->toDateTimeString()
                    ]
                ]
            ]);

            return $response->getStatusCode() === 200 || $response->getStatusCode() === 201;
        } catch (\Exception $e) {
            Log::error('WebhookService: Erro ao disparar webhook: ' . $e->getMessage());
            return false;
        }
    }
}
