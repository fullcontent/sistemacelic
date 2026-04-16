<?php

namespace App\Helpers;

class IbgeHelper
{
    /**
     * Mapeamento de nomes de cidades para códigos IBGE (7 dígitos)
     * Principalmente cidades de SC, SP e RS onde o sistema atua.
     */
    protected static $mapping = [
        'SC' => [
            'BALNEARIO CAMBORIU' => '4202008',
            'CAMBORIU' => '4203204',
            'ITAJAI' => '4208203',
            'FLORIANOPOLIS' => '4205407',
            'BLUMENAU' => '4202404',
            'JOINVILLE' => '4209102',
            'ITAPEMA' => '4208302',
            'PORTO BELO' => '4213500',
            'BOMBINHAS' => '4202453',
            'NAVEGANTES' => '4211306',
            'PENHA' => '4212502',
            'PIÇARRAS' => '4202305',
            'BALNEARIO PIÇARRAS' => '4202305',
        ],
        'RS' => [
            'GRAMADO' => '4309100',
            'CANELA' => '4304408',
            'PORTO ALEGRE' => '4314902',
            'CAXIAS DO SUL' => '4305108',
            'PELOTAS' => '4314407',
            'CANOAS' => '4304606',
            'SANTA MARIA' => '4316907',
        ],
        'PE' => [
            'CARUARU' => '2604106',
            'RECIFE' => '2611606',
            'JABOATAO DOS GUARARAPES' => '2607901',
            'OLINDA' => '2609600',
        ],
        'SP' => [
            'SAO PAULO' => '3550308',
            'BARUERI' => '3505708',
            'CAMPINAS' => '3509502',
            'GUARULHOS' => '3518800',
            'SAO BERNARDO DO CAMPO' => '3548708',
            'SANTO ANDRE' => '3547809',
            'OSASCO' => '3534401',
            'SANTOS' => '3548500',
            'SOROCABA' => '3552205',
            'RIBEIRAO PRETO' => '3543402',
        ]
    ];

    /**
     * Retorna o código IBGE baseado no nome da cidade e UF.
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

        $uf = strtoupper($uf);
        $cityName = self::normalize($cityName);

        if (isset(self::$mapping[$uf][$cityName])) {
            return self::$mapping[$uf][$cityName];
        }

        return null;
    }

    /**
     * Normaliza o nome da cidade para comparação (Uppercase, remove acentos)
     */
    protected static function normalize($string)
    {
        $string = mb_strtoupper($string, 'UTF-8');
        $map = [
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C'
        ];
        $string = strtr($string, $map);
        $string = trim($string);
        return $string;
    }
}
