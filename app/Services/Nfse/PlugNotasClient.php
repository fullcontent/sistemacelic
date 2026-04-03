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
            'mock_mode' => function_exists('config') ? (bool) config('services.plugnotas.mock_mode', true) : true,
        ];

        $this->settings = array_merge($default, $settings);

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

        try {
            $response = $this->http->post('/nfse', [
                'headers' => [
                    'x-api-key' => $this->settings['api_key'],
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            throw new \RuntimeException('Falha na chamada PlugNotas: ' . $e->getMessage(), 0, $e);
        }
    }
}
