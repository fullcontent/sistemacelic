<?php

namespace App\Services\Nfse;

class PlugNotasClient
{
    /** @var object|null */
    private $http;

    /** @var array */
    private $settings;
    public function __construct($http = null, array $settings = [])
    {
        $default = [
            'base_url' => function_exists('config') ? config('services.plugnotas.base_url', 'https://api.plugnotas.com.br') : 'https://api.plugnotas.com.br',
            'api_key' => function_exists('config') ? config('services.plugnotas.api_key') : null,
            'timeout' => function_exists('config') ? (int) config('services.plugnotas.timeout', 30) : 30,
            'mock_mode' => function_exists('config') ? (bool) config('services.plugnotas.mock_mode', false) : false,
            'retry_attempts' => function_exists('config') ? (int) config('services.plugnotas.retry_attempts', 3) : 3,
            'retry_delay_ms' => function_exists('config') ? (int) config('services.plugnotas.retry_delay_ms', 600) : 600,
        ];

        $this->settings = array_merge($default, $settings);

        // Auto-disable mock if we have a key and it wasn't explicitly forced
        if (!empty($this->settings['api_key']) && env('PLUGNOTAS_MOCK_MODE') === null) {
            $this->settings['mock_mode'] = false;
        }

        if ($this->settings['mock_mode']) {
            $this->http = null;
            return;
        }

        if ($http !== null) {
            $this->http = $http;
            return;
        }

        if (!class_exists('GuzzleHttp\\Client')) {
            throw new \RuntimeException('Dependência Guzzle não encontrada para modo real da PlugNotas.');
        }

        $clientClass = 'GuzzleHttp\\Client';
        $this->http = new $clientClass([
            'base_uri' => $this->settings['base_url'],
            'timeout' => $this->settings['timeout'],
        ]);
    }

    public function emitirNfse(array $payload)
    {
        if ($this->settings['mock_mode']) {
            return [
                'id' => 'mock_' . uniqid(),
                'status' => 'emitida',
                'numero' => 'SIM-' . strtoupper(substr(md5(json_encode($payload) . microtime(true)), 0, 8)),
                'mock' => true,
            ];
        }

        if (empty($this->settings['api_key'])) {
            throw new \RuntimeException('PLUGNOTAS_API_KEY não configurada e mock desabilitado.');
        }

        $attempts = max(1, (int) ($this->settings['retry_attempts'] ?? 3));
        $baseDelayMs = max(100, (int) ($this->settings['retry_delay_ms'] ?? 600));
        $lastException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $response = $this->http->post('/nfse', [
                    'headers' => [
                        'x-api-key' => $this->settings['api_key'],
                        'Accept' => 'application/json',
                    ],
                    'json' => [$payload],
                ]);

                return json_decode((string) $response->getBody(), true);
            } catch (\Exception $e) {
                $lastException = $e;
                $statusCode = $this->extractStatusCode($e);
                $retryable = $this->isRetryableEmissionError($statusCode);
                $isLastAttempt = $attempt >= $attempts;

                if ($retryable && !$isLastAttempt) {
                    $this->logRetryAttempt($attempt, $attempts, $statusCode, $e);
                    $delayMs = $baseDelayMs * $attempt;
                    usleep($delayMs * 1000);
                    continue;
                }

                $suffix = $isLastAttempt && $retryable
                    ? " após {$attempts} tentativas"
                    : '';

                throw new \RuntimeException('Falha na chamada PlugNotas' . $suffix . ': ' . $e->getMessage(), 0, $e);
            }
        }

        throw new \RuntimeException('Falha na chamada PlugNotas: erro desconhecido.', 0, $lastException);
    }

    private function extractStatusCode($exception)
    {
        if (!is_object($exception) || !method_exists($exception, 'getResponse')) {
            return null;
        }

        $response = $exception->getResponse();
        if (!$response || !method_exists($response, 'getStatusCode')) {
            return null;
        }

        return (int) $response->getStatusCode();
    }

    private function isRetryableEmissionError($statusCode)
    {
        if ($statusCode === null) {
            return true;
        }

        return $statusCode >= 500;
    }

    private function logRetryAttempt($attempt, $attempts, $statusCode, \Exception $e)
    {
        if (!function_exists('logger')) {
            return;
        }

        logger()->warning('PlugNotas: tentativa de reenvio da emissao NFSe', [
            'attempt' => $attempt,
            'attempts' => $attempts,
            'status_code' => $statusCode,
            'error' => $e->getMessage(),
        ]);
    }

    public function consultarNfse($id)
    {
        if ($this->settings['mock_mode']) {
            return [
                'id' => $id, 
                'status' => 'concluido', 
                'numero' => '999',
                'pdf' => 'https://plugnotas.com.br/pdf/exemplo',
                'xml' => 'https://plugnotas.com.br/xml/exemplo',
                'mock' => true
            ];
        }

        try {
            $response = $this->http->get("/nfse/{$id}", [
                'headers' => [
                    'x-api-key' => $this->settings['api_key'],
                    'Accept' => 'application/json',
                ]
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            throw new \RuntimeException('Falha ao consultar nota na PlugNotas: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getBaseUrl()
    {
        return $this->settings['base_url'];
    }

    public function cancelarNfse($id, $motivo = 'Cancelamento solicitado pelo usuário.')
    {
        if ($this->settings['mock_mode']) {
            return ['id' => $id, 'status' => 'cancelado', 'mensagem' => 'Nota cancelada (MOCK)', 'mock' => true];
        }

        try {
            $response = $this->http->post("/nfse/cancelar", [
                'headers' => [
                    'x-api-key' => $this->settings['api_key'],
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'id' => $id,
                    'justificativa' => $motivo
                ]
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            throw new \RuntimeException('Falha ao cancelar nota na PlugNotas: ' . $e->getMessage(), 0, $e);
        }
    }

    public function downloadFile($id, $type = 'pdf')
    {
        if ($this->settings['mock_mode']) {
            if ($type === 'xml') {
                return '<?xml version="1.0" encoding="UTF-8"?><mock>Conteúdo XML de teste</mock>';
            }
            // Minimal valid PDF 1.4
            return "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";
        }

        try {
            $endpoint = $type === 'pdf' ? "/nfse/pdf/{$id}" : "/nfse/xml/{$id}";
            $response = $this->http->get($endpoint, [
                'headers' => [
                    'x-api-key' => $this->settings['api_key'],
                ]
            ]);

            return (string) $response->getBody();
        } catch (\Exception $e) {
            throw new \RuntimeException("Falha ao baixar arquivo {$type} na PlugNotas: " . $e->getMessage(), 0, $e);
        }
    }
}
