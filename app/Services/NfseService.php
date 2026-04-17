<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class NfseService
{
    private $client;
    private $token;
    private $baseUrl;

    public function __construct()
    {
        $this->token = env('PLUGNOTAS_TOKEN');
        $env = env('PLUGNOTAS_ENV', 'sandbox');
        $this->baseUrl = $env === 'production' 
            ? 'https://api.plugnotas.com.br' 
            : 'https://api.sandbox.plugnotas.com.br';

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'x-api-key' => $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
        ]);
    }

    /**
     * Cadastrar ou atualizar empresa na PlugNotas
     */
    public function cadastrarEmpresa($payload)
    {
        try {
            $response = $this->client->post('/empresa', [
                'json' => $payload
            ]);

            $statusCode = (int) $response->getStatusCode();
            $body = json_decode((string) $response->getBody(), true);

            if ($statusCode === 409) {
                return [
                    'success' => true,
                    'status' => $statusCode,
                    'message' => 'Empresa já estava cadastrada na PlugNotas.',
                    'data' => $body,
                ];
            }

            if ($statusCode >= 200 && $statusCode < 300) {
                return [
                    'success' => true,
                    'status' => $statusCode,
                    'data' => $body,
                ];
            }

            $message = is_array($body)
                ? ($body['error']['message'] ?? $body['message'] ?? json_encode($body))
                : 'Erro desconhecido ao cadastrar empresa na PlugNotas.';

            Log::error('NfseService@cadastrarEmpresa: ' . $message, ['status' => $statusCode, 'body' => $body]);

            return [
                'success' => false,
                'status' => $statusCode,
                'message' => 'Erro na PlugNotas: ' . $message,
            ];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $statusCode = 0;

            if ($e instanceof \GuzzleHttp\Exception\ClientException) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $bodyStr = $response->getBody()->getContents();
                $body = json_decode($bodyStr, true);
                
                // Se já existir (409), consideramos sucesso na sincronização
                if ($statusCode == 409) {
                    return [
                        'success' => true,
                        'message' => 'Empresa já estava cadastrada na PlugNotas.',
                        'data' => $body
                    ];
                }
                
                $message = $body['error']['message'] ?? $bodyStr;
            }

            \Log::error('NfseService@cadastrarEmpresa: ' . $message);
            return [
                'success' => false,
                'message' => 'Erro na PlugNotas: ' . $message
            ];
        }
    }

    /**
     * Consultar se empresa já está cadastrada na organização da PlugNotas.
     */
    public function consultarEmpresaPorCnpj($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', (string) $cnpj);
        if (empty($cnpj)) {
            return [
                'success' => false,
                'exists' => false,
                'message' => 'CNPJ inválido para consulta.',
            ];
        }

        try {
            $response = $this->client->get('/empresa/' . $cnpj);
            $statusCode = (int) $response->getStatusCode();
            $body = json_decode((string) $response->getBody(), true);

            if ($statusCode >= 200 && $statusCode < 300) {
                $cnpjRetornado = $this->extractCnpjFromEmpresaResponse($body);
                $exists = !empty($cnpjRetornado) && $cnpjRetornado === $cnpj;

                if ($exists) {
                    return [
                        'success' => true,
                        'exists' => true,
                        'status' => $statusCode,
                        'data' => $body,
                    ];
                }

                Log::warning('NfseService@consultarEmpresaPorCnpj: resposta 2xx sem CNPJ confirmado, usando fallback de listagem.', [
                    'cnpj_consultado' => $cnpj,
                    'status' => $statusCode,
                    'cnpj_extraido' => $cnpjRetornado,
                    'body' => $body,
                ]);

                return $this->consultarEmpresaPorCnpjViaListagem($cnpj);
            }

            // 404: empresa não localizada por consulta direta.
            if ($statusCode === 404) {
                return $this->consultarEmpresaPorCnpjViaListagem($cnpj);
            }

            // Alguns ambientes/contas podem não expor GET /empresa/{cnpj}; faz fallback.
            if ($statusCode === 400 || $statusCode === 405) {
                return $this->consultarEmpresaPorCnpjViaListagem($cnpj);
            }

            $message = is_array($body)
                ? ($body['error']['message'] ?? $body['message'] ?? json_encode($body))
                : 'Falha ao consultar empresa na PlugNotas.';

            if ($this->isEmpresaNotFoundMessage($message)) {
                return [
                    'success' => true,
                    'exists' => false,
                    'status' => $statusCode,
                    'message' => $message,
                ];
            }

            return [
                'success' => false,
                'exists' => false,
                'status' => $statusCode,
                'message' => $message,
            ];
        } catch (\Exception $e) {
            // Fallback defensivo para listagem quando ocorrer erro na consulta direta.
            return $this->consultarEmpresaPorCnpjViaListagem($cnpj);
        }
    }

    private function extractCnpjFromEmpresaResponse($body)
    {
        if (!is_array($body)) {
            return null;
        }

        $candidates = [
            $body['cpfCnpj'] ?? null,
            $body['cnpj'] ?? null,
            isset($body['data']) && is_array($body['data']) ? ($body['data']['cpfCnpj'] ?? null) : null,
            isset($body['data']) && is_array($body['data']) ? ($body['data']['cnpj'] ?? null) : null,
        ];

        foreach ($candidates as $candidate) {
            $clean = preg_replace('/\D/', '', (string) $candidate);
            if (!empty($clean)) {
                return $clean;
            }
        }

        return null;
    }

    private function consultarEmpresaPorCnpjViaListagem($cnpj)
    {
        try {
            $hashProximaPagina = null;

            // Limite de segurança para paginação.
            for ($page = 0; $page < 30; $page++) {
                $options = [];
                if (!empty($hashProximaPagina)) {
                    $options['query'] = ['hashProximaPagina' => $hashProximaPagina];
                }

                $response = $this->client->get('/empresa', $options);
                $statusCode = (int) $response->getStatusCode();
                $body = json_decode((string) $response->getBody(), true);

                if ($statusCode < 200 || $statusCode >= 300) {
                    $message = is_array($body)
                        ? ($body['error']['message'] ?? $body['message'] ?? json_encode($body))
                        : 'Falha ao consultar empresas na PlugNotas.';

                    if ($this->isEmpresaNotFoundMessage($message)) {
                        return [
                            'success' => true,
                            'exists' => false,
                            'status' => $statusCode,
                            'message' => $message,
                        ];
                    }

                    return [
                        'success' => false,
                        'exists' => false,
                        'status' => $statusCode,
                        'message' => $message,
                    ];
                }

                if (!is_array($body) || empty($body)) {
                    return [
                        'success' => true,
                        'exists' => false,
                    ];
                }

                foreach ($body as $empresa) {
                    $cpfCnpj = preg_replace('/\D/', '', (string) ($empresa['cpfCnpj'] ?? ''));
                    if ($cpfCnpj === $cnpj) {
                        return [
                            'success' => true,
                            'exists' => true,
                            'data' => $empresa,
                        ];
                    }
                }

                $ultimaEmpresa = end($body);
                $proximoHash = is_array($ultimaEmpresa) ? ($ultimaEmpresa['_id'] ?? null) : null;

                if (empty($proximoHash) || $proximoHash === $hashProximaPagina) {
                    break;
                }

                $hashProximaPagina = $proximoHash;
            }

            return [
                'success' => true,
                'exists' => false,
            ];
        } catch (\Exception $e) {
            Log::error('NfseService@consultarEmpresaPorCnpjViaListagem: ' . $e->getMessage(), ['cnpj' => $cnpj]);

            return [
                'success' => false,
                'exists' => false,
                'message' => 'Erro ao consultar empresa na PlugNotas: ' . $e->getMessage(),
            ];
        }
    }

    private function isEmpresaNotFoundMessage($message)
    {
        if (empty($message) || !is_string($message)) {
            return false;
        }

        $normalized = mb_strtolower($message, 'UTF-8');

        return strpos($normalized, 'nao localizamos qualquer empresa') !== false
            || strpos($normalized, 'não localizamos qualquer empresa') !== false
            || strpos($normalized, 'empresa nao localizada') !== false
            || strpos($normalized, 'empresa não localizada') !== false
            || strpos($normalized, 'nenhuma empresa') !== false;
    }

    /**
     * Enviar uma nota para processamento
     */
    public function emitir($payload)
    {
        try {
            $response = $this->client->post('/nfse', [
                'json' => [$payload] // PlugNotas aceita um array de notas
            ]);

            $body = json_decode($response->getBody(), true);
            
            return [
                'success' => $response->getStatusCode() === 200 || $response->getStatusCode() === 201,
                'status' => $response->getStatusCode(),
                'data' => $body
            ];
        } catch (\Exception $e) {
            Log::error('NfseService@emitir: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Consultar status de uma nota
     */
    public function consultar($plugnotasId)
    {
        try {
            $response = $this->client->get("/nfse/{$plugnotasId}");
            $body = json_decode($response->getBody(), true);

            return [
                'success' => $response->getStatusCode() === 200,
                'status' => $response->getStatusCode(),
                'data' => $body
            ];
        } catch (\Exception $e) {
            Log::error('NfseService@consultar: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download do PDF
     */
    public function downloadPdf($plugnotasId)
    {
        try {
            $response = $this->client->get("/nfse/pdf/{$plugnotasId}");
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            Log::error('NfseService@downloadPdf: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download do XML
     */
    public function downloadXml($plugnotasId)
    {
        try {
            $response = $this->client->get("/nfse/xml/{$plugnotasId}");
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            Log::error('NfseService@downloadXml: ' . $e->getMessage());
            return null;
        }
    }
}
