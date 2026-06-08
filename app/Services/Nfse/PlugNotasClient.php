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
            'timeout' => function_exists('config') ? (int) config('services.plugnotas.timeout', 180) : 180,
            'mock_mode' => function_exists('config') ? (bool) config('services.plugnotas.mock_mode', false) : false,
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
            $message = $e->getMessage();
            if (strpos($message, 'cURL error 28') !== false || strpos($message, 'timed out') !== false) {
                $message .= ' (Dica: Aumente o PLUGNOTAS_TIMEOUT no .env para lidar com lentidão na API)';
            }
            throw new \RuntimeException('Falha na chamada PlugNotas: ' . $message, 0, $e);
        }
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
            $message = $e->getMessage();
            if (strpos($message, 'cURL error 28') !== false || strpos($message, 'timed out') !== false) {
                $message .= ' (Dica: Aumente o PLUGNOTAS_TIMEOUT no .env)';
            }
            throw new \RuntimeException('Falha ao consultar nota na PlugNotas: ' . $message, 0, $e);
        }
    }

    public function listarNfse(array $filtros = [])
    {
        if ($this->settings['mock_mode']) {
            return [];
        }

        try {
            $response = $this->http->get("/nfse", [
                'headers' => [
                    'x-api-key' => $this->settings['api_key'],
                    'Accept' => 'application/json',
                ],
                'query' => $filtros
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (strpos($message, 'cURL error 28') !== false || strpos($message, 'timed out') !== false) {
                $message .= ' (Dica: Aumente o PLUGNOTAS_TIMEOUT no .env)';
            }
            throw new \RuntimeException('Falha ao listar notas na PlugNotas: ' . $message, 0, $e);
        }
    }

    public function getCepInfo($cep)
    {
        $cep = preg_replace('/\D/', '', $cep);
        try {
            $response = $this->http->get("https://brasilapi.com.br/api/cep/v2/{$cep}");
            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            // Fallback para v1 se v2 falhar
            try {
                $response = $this->http->get("https://brasilapi.com.br/api/cep/v1/{$cep}");
                return json_decode((string) $response->getBody(), true);
            } catch (\Exception $e2) {
                return null;
            }
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
            $response = $this->http->post("/nfse/cancelar/{$id}", [
                'headers' => [
                    'x-api-key' => $this->settings['api_key'],
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'codigo' => '9',
                    'motivo' => $motivo
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

    public function getCidadeByNome($nome, $uf)
    {
        if ($this->settings['mock_mode'] || empty($this->settings['api_key'])) {
            return null;
        }

        $uf = trim(strtoupper($uf));
        $cacheKey = 'plugnotas_todas_cidades';

        $cidades = \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400 * 30, function () {
            try {
                $response = $this->http->get('/nfse/cidades', [
                    'headers' => [
                        'x-api-key' => $this->settings['api_key'],
                        'Accept' => 'application/json',
                    ]
                ]);
                return json_decode((string) $response->getBody(), true);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Erro ao consultar /nfse/cidades PlugNotas: " . $e->getMessage());
                return null;
            }
        });

        if (!is_array($cidades)) {
            return null;
        }

        $nomeNormalizado = self::normalizeString($nome);

        // Busca exata primeiro
        foreach ($cidades as $c) {
            if (isset($c['uf'], $c['nome'], $c['id']) && strtoupper($c['uf']) === $uf) {
                if (self::normalizeString($c['nome']) === $nomeNormalizado) {
                    return (string) $c['id'];
                }
            }
        }

        // Busca aproximada (fuzzy match) para lidar com erros de digitação
        $bestMatchId = null;
        $highestSimilarity = 0;

        foreach ($cidades as $c) {
            if (isset($c['uf'], $c['nome'], $c['id']) && strtoupper($c['uf']) === $uf) {
                $cidadeNormalizada = self::normalizeString($c['nome']);
                similar_text($nomeNormalizado, $cidadeNormalizada, $percent);
                
                if ($percent > $highestSimilarity) {
                    $highestSimilarity = $percent;
                    $bestMatchId = (string) $c['id'];
                }
            }
        }

        // Se a similaridade for de 80% ou mais, aceitamos como erro de digitação
        if ($bestMatchId !== null && $highestSimilarity >= 80) {
            \Illuminate\Support\Facades\Log::info("PlugNotasClient: Correção automática de cidade. Digitado: '{$nome}', IBGE encontrado: {$bestMatchId} (Similaridade: {$highestSimilarity}%)");
            return $bestMatchId;
        }

        return null;
    }

    public function getCidadeById($codigoIbge)
    {
        if ($this->settings['mock_mode'] || empty($this->settings['api_key'])) {
            return null;
        }

        $cacheKey = 'plugnotas_cidade_id_' . $codigoIbge;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400 * 30, function () use ($codigoIbge) {
            try {
                $response = $this->http->get('/nfse/cidades/' . $codigoIbge, [
                    'headers' => [
                        'x-api-key' => $this->settings['api_key'],
                        'Accept' => 'application/json',
                    ]
                ]);
                return json_decode((string) $response->getBody(), true);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Erro ao consultar /nfse/cidades/{$codigoIbge} PlugNotas: " . $e->getMessage());
                return null;
            }
        });
    }

    private static function normalizeString($string)
    {
        $string = mb_strtoupper((string) $string, 'UTF-8');
        $map = [
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C'
        ];
        $string = strtr($string, $map);
        return trim($string);
    }
}
