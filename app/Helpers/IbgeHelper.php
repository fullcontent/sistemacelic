<?php

namespace App\Helpers;

use App\Services\Nfse\PlugNotasClient;

class IbgeHelper
{
    /**
     * Retorna o código IBGE baseado no nome da cidade e UF.
     * Utiliza a API da PlugNotas com cache de 30 dias para evitar requisições repetidas.
     * Caso não encontre, retorna null.
     * 
     * @param string $cityName
     * @param string $uf
     * @return string|null
     */
    public static function getIbgeCode($cityName, $uf)
    {
        if (empty($cityName) || empty($uf)) {
            return null;
        }

        $uf = trim(strtoupper($uf));
        $cacheKey = 'brasilapi_cidades_' . $uf;

        $cidades = \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400 * 30, function () use ($uf) {
            try {
                $client = new \GuzzleHttp\Client(['timeout' => 10]);
                $response = $client->get("https://brasilapi.com.br/api/ibge/municipios/v1/{$uf}");
                return json_decode((string) $response->getBody(), true);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Erro ao consultar BrasilAPI para cidades de {$uf}: " . $e->getMessage());
                return null;
            }
        });

        if (!is_array($cidades)) {
            return null;
        }

        $nomeNormalizado = self::normalizeString($cityName);

        // Busca exata primeiro
        foreach ($cidades as $c) {
            if (isset($c['nome'], $c['codigo_ibge'])) {
                if (self::normalizeString($c['nome']) === $nomeNormalizado) {
                    return (string) $c['codigo_ibge'];
                }
            }
        }

        // Busca aproximada (fuzzy match) para lidar com erros de digitação
        $bestMatchId = null;
        $highestSimilarity = 0;

        foreach ($cidades as $c) {
            if (isset($c['nome'], $c['codigo_ibge'])) {
                $cidadeNormalizada = self::normalizeString($c['nome']);
                similar_text($nomeNormalizado, $cidadeNormalizada, $percent);
                
                if ($percent > $highestSimilarity) {
                    $highestSimilarity = $percent;
                    $bestMatchId = (string) $c['codigo_ibge'];
                }
            }
        }

        // Se a similaridade for de 80% ou mais, aceitamos como erro de digitação
        if ($bestMatchId !== null && $highestSimilarity >= 80) {
            \Illuminate\Support\Facades\Log::info("IbgeHelper: Correção automática de cidade via BrasilAPI. Digitado: '{$cityName}', IBGE encontrado: {$bestMatchId} (Similaridade: {$highestSimilarity}%)");
            return $bestMatchId;
        }

        return null;
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
