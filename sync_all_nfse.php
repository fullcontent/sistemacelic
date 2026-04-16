<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NfseEmission;
use App\Services\Nfse\NfseEmissionService;

$service = app(NfseEmissionService::class);

$emissions = NfseEmission::whereIn('status', ['CONCLUIDA', 'concluido', 'emitida', 'EMITIDA'])->get();

echo "Encontradas " . $emissions->count() . " emissões para sincronizar.\n";

foreach ($emissions as $e) {
    echo "Sincronizando Emissão #{$e->id}... ";
    try {
        $service->consultarStatus($e->id);
        echo "OK! (Status: {$e->status}, PDF: " . ($e->pdf_url ? 'Sim' : 'Não') . ")\n";
    } catch (\Exception $ex) {
        echo "Erro: " . $ex->getMessage() . "\n";
    }
}

echo "\nSincronização concluída.\n";
