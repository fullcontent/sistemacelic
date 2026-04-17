<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();

$baseUrl = config('services.plugnotas.base_url');
$apiKey = config('services.plugnotas.api_key');

if (empty($apiKey)) {
    fwrite(STDERR, "PLUGNOTAS api key nao configurada.\n");
    exit(1);
}

$tomador = [
    'cpfCnpj' => '08187168000160',
    'razaoSocial' => 'TECNOSPEED S/A',
    'email' => 'teste@tecnospeed.com.br',
    'endereco' => [
        'logradouro' => 'AV DUQUE DE CAXIAS',
        'numero' => '882',
        'complemento' => 'SALA 1708 TORRE II',
        'bairro' => 'CENTRO',
        'codigoCidade' => '4115200',
        'descricaoCidade' => 'MARINGA',
        'estado' => 'PR',
        'cep' => '87111520',
    ],
    'telefone' => [
        'ddd' => '44',
        'numero' => '999999999',
    ],
];

$payload = [
    'dataCompetencia' => date('Y-m-d'),
    'emitirComo' => 'Prestador',
    'regimeApuracao' => '1',
    'tomadorTipo' => 'Brasil',
    'intermediarioTipo' => 'Intermediario nao informado',
    'localPrestacao' => 'Brasil',
    'municipio' => 'MARINGA',
    'codigoTributacaoNacional' => '170202',
    'suspensaoExigibilidadeIssqn' => false,
    'itemNbs' => '0000',
    'issqnExigibilidadeSuspensa' => false,
    'issqnRetido' => false,
    'beneficioMunicipal' => false,
    'pisCofinsSituacao' => '1',
    'aliquotaSimples' => 0.0,
    'prestador' => [
        'cpfCnpj' => '08187168000160',
        'inscricaoMunicipal' => '716',
        'certificado' => '5f18b5d18c862d6452fxxxx',
    ],
    'tomador' => $tomador,
    'servico' => [
        [
            'codigo' => '170202',
            'discriminacao' => 'TESTE DIRETO VIA SCRIPT - NFSe 2528',
            'valor' => [
                'servico' => 10.00,
            ],
        ],
    ],
];

$clientClass = 'GuzzleHttp\\Client';
$client = new $clientClass([
    'base_uri' => $baseUrl,
    'timeout' => 60,
    'http_errors' => false,
]);

$response = $client->post('/nfse', [
    'headers' => [
        'x-api-key' => $apiKey,
        'Accept' => 'application/json',
    ],
    'json' => [$payload],
]);

$status = $response->getStatusCode();
$body = (string) $response->getBody();
$headers = $response->getHeaders();
$requestId = '';

foreach (['x-request-id', 'x-correlation-id', 'request-id'] as $headerName) {
    if (!empty($headers[$headerName])) {
        $requestId = implode(',', $headers[$headerName]);
        break;
    }
}

echo "BASE_URL: {$baseUrl}\n";
echo "STATUS: {$status}\n";
echo "REQUEST_ID: " . ($requestId !== '' ? $requestId : 'N/A') . "\n";
echo "HEADERS:\n";
foreach ($headers as $name => $values) {
    echo "  {$name}: " . implode(',', $values) . "\n";
}
echo "RESPONSE: {$body}\n";
