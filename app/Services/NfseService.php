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

            $body = json_decode($response->getBody(), true);
            
            return [
                'success' => true,
                'status' => $response->getStatusCode(),
                'data' => $body
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
