<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Faturamento;
use App\Models\FaturamentoServico;
use App\Services\Nfse\NfseEmissionService;

$faturamentoId = 2602;

$faturamento = Faturamento::with([
    'empresa', 
    'servicosFaturados.detalhes.unidade', 
    'servicosFaturados.detalhes.financeiro'
])->find($faturamentoId);

if (!$faturamento) {
    die("Faturamento $faturamentoId não encontrado.\n");
}

$faturamentoServicos = FaturamentoServico::with('detalhes.unidade', 'detalhes.financeiro')
            ->where('faturamento_id', $faturamento->id)
            ->get();

$service = app(NfseEmissionService::class);

// Como não sabemos a configuração exata do request, vamos simular os dados
$data = [
    'faturamento_id' => $faturamentoId,
    'opcao_automatica' => '3', // Agrupado (1 payload apenas, mais fácil para testar no curl)
    'dados_castro_id' => 1, // Assumindo dados castro 1
    'campos_adicionais' => []
];

try {
    // Usamos reflection para chamar o método privado resolveConfig
    $reflection = new \ReflectionClass($service);
    
    $methodConfig = $reflection->getMethod('resolveConfig');
    $methodConfig->setAccessible(true);
    $config = $methodConfig->invoke($service, $data);
    
    $methodTomador = $reflection->getMethod('resolveTomadorData');
    $methodTomador->setAccessible(true);
    
    // Pegar o primeiro serviço para base do tomador se opcao for 3 (agrupado pega do primeiro)
    $primeiroServico = $faturamentoServicos->first()->detalhes ?? null;
    $tomadorData = $methodTomador->invoke($service, $faturamento, $primeiroServico, '3', null);
    
    $methodGrouped = $reflection->getMethod('buildGroupedItem');
    $methodGrouped->setAccessible(true);
    $groupedItem = $methodGrouped->invoke($service, $faturamentoServicos, $tomadorData['cpfCnpj']);
    
    $payload = \App\Services\Nfse\NfsePayloadFactory::buildBasePayload($config->toArray(), $groupedItem, []);
    $payload['tomador'] = $tomadorData;
    
    $json = json_encode([$payload], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    $apiKey = config('services.plugnotas.api_key', 'SUA_API_KEY_AQUI');
    
    echo "curl -X POST https://api.plugnotas.com.br/nfse \\\n";
    echo "     -H \"Content-Type: application/json\" \\\n";
    echo "     -H \"x-api-key: $apiKey\" \\\n";
    echo "     -d '" . str_replace("'", "'\\''", $json) . "'\n";
    
} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
