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

        try {
            $client = app(PlugNotasClient::class);
            return $client->getCidadeByNome($cityName, $uf);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("IbgeHelper: Falha ao instanciar ou buscar cliente PlugNotas: " . $e->getMessage());
            return null;
        }
    }
}
