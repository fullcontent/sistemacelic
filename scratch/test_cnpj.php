<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

$cnpj = '39740418000170';
$client = new Client(['timeout' => 10, 'verify' => false]);

echo "=== TENTANDO CNPJ V2 ===\n";
try {
    $response = $client->get("https://brasilapi.com.br/api/cnpj/v2/{$cnpj}");
    $data = json_decode((string) $response->getBody(), true);
    print_r($data);
} catch (\Exception $e) {
    echo "Erro V2: " . $e->getMessage() . "\n";
}

echo "\n=== TENTANDO RECEITAWS ===\n";
try {
    $response = $client->get("https://www.receitaws.com.br/v1/cnpj/{$cnpj}");
    $data = json_decode((string) $response->getBody(), true);
    print_r($data);
} catch (\Exception $e) {
    echo "Erro ReceitaWS: " . $e->getMessage() . "\n";
}
