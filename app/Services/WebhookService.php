<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    protected $client;
    protected $webhookUrl;
    protected $statusWebhookUrl;

    public function __construct()
    {
        $this->client = new Client();
        // Definir as URLs dos webhooks no .env
        $this->webhookUrl = env('WEBHOOK_EMAIL_URL');
        $this->statusWebhookUrl = env('WEBHOOK_STATUS_URL', env('WEBHOOK_EMAIL_URL'));
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
                        'ai_summary' => $resumo,
                        'user_name' => auth()->user() ? auth()->user()->name : 'Sistema'
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

    public function sendStatusChangeEmail($servico, $previousStatus, $newStatus, $observacoes = '')
    {
        if (!$this->statusWebhookUrl) {
            Log::warning('WebhookService: URL do webhook de status não configurada (WEBHOOK_STATUS_URL / WEBHOOK_EMAIL_URL).');
            return false;
        }

        try {
            if (!$servico->relationLoaded('unidade')) {
                $servico->load('unidade.empresa');
            }
            if (!$servico->relationLoaded('responsavel')) {
                $servico->load('responsavel');
            }

            $anexosDiretos = [];
            if ($servico->protocolo_anexo) {
                $anexosDiretos[] = [
                    'tipo' => 'protocolo',
                    'numero' => $servico->protocolo_numero ?? 'N/A',
                    'url' => asset('storage/' . $servico->protocolo_anexo)
                ];
            }
            if ($servico->laudo_anexo) {
                $anexosDiretos[] = [
                    'tipo' => 'laudo',
                    'numero' => $servico->laudo_numero ?? 'N/A',
                    'url' => asset('storage/' . $servico->laudo_anexo)
                ];
            }
            if ($servico->licenca_anexo) {
                $anexosDiretos[] = [
                    'tipo' => 'licenca',
                    'numero' => $servico->licenciamento ?? 'N/A',
                    'url' => asset('storage/' . $servico->licenca_anexo)
                ];
            }

            $arquivosCadastrados = [];
            if ($servico->arquivos) {
                foreach ($servico->arquivos as $arq) {
                    $arquivosCadastrados[] = [
                        'id' => $arq->id,
                        'nome' => $arq->nome ?? $arq->arquivo,
                        'url' => asset('storage/' . $arq->arquivo)
                    ];
                }
            }

            $ciclosAnalise = [];
            if ($servico->analiseProtocoloLaudos) {
                foreach ($servico->analiseProtocoloLaudos as $ciclo) {
                    $item = [
                        'id' => $ciclo->id,
                        'status' => $ciclo->status,
                        'descricao' => $ciclo->descricao,
                        'protocolo' => null,
                        'laudo' => null,
                        'documentos' => []
                    ];

                    if ($ciclo->protocolo) {
                        $item['protocolo'] = [
                            'numero' => $ciclo->protocolo->numero_protocolo ?? 'N/A',
                            'url' => isset($ciclo->protocolo->arquivo) ? asset('storage/' . $ciclo->protocolo->arquivo) : null
                        ];
                    }

                    if ($ciclo->laudo) {
                        $item['laudo'] = [
                            'numero' => $ciclo->laudo->numero_laudo ?? 'N/A',
                            'url' => isset($ciclo->laudo->arquivo) ? asset('storage/' . $ciclo->laudo->arquivo) : null
                        ];
                    }

                    if ($ciclo->documentos) {
                        foreach ($ciclo->documentos as $doc) {
                            $item['documentos'][] = [
                                'nome' => $doc->nome ?? $doc->arquivo,
                                'url' => asset('storage/' . $doc->arquivo)
                            ];
                        }
                    }

                    $ciclosAnalise[] = $item;
                }
            }

            $route = auth()->check() && auth()->user()->privileges == 'cliente' ? 'cliente.servico.show' : 'servicos.show';

            $payload = [
                'event' => 'service_status_changed',
                'servico' => [
                    'id' => $servico->id,
                    'os' => $servico->os,
                    'nome' => $servico->nome,
                    'status_anterior' => $previousStatus ?: 'N/A',
                    'novo_status' => $newStatus,
                    'situacao' => $servico->situacao,
                    'tipo' => $servico->tipo,
                    'unidade' => $servico->unidade ? $servico->unidade->nomeFantasia : 'N/A',
                    'cliente' => $servico->unidade && $servico->unidade->empresa ? $servico->unidade->empresa->nomeFantasia : 'N/A',
                    'responsavel' => $servico->responsavel ? $servico->responsavel->name : 'N/A',
                    'link' => route($route, $servico->id)
                ],
                'observacoes' => $observacoes,
                'anexos_diretos' => $anexosDiretos,
                'arquivos_cadastrados' => $arquivosCadastrados,
                'ciclos_analise' => $ciclosAnalise,
                'system_context' => [
                    'app_name' => config('app.name'),
                    'app_url' => config('app.url'),
                    'user' => auth()->check() ? auth()->user()->name : 'Sistema',
                    'timestamp' => now()->toDateTimeString()
                ]
            ];

            $response = $this->client->post($this->statusWebhookUrl, [
                'json' => $payload
            ]);

            return $response->getStatusCode() === 200 || $response->getStatusCode() === 201;
        } catch (\Exception $e) {
            Log::error('WebhookService: Erro ao disparar webhook de mudança de status: ' . $e->getMessage());
            return false;
        }
    }
}
