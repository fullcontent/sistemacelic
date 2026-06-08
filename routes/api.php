<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Busca serviços por termo, nome da empresa ou nome da unidade
Route::get('/search-service', 'ApiController@searchService');

// Adiciona uma nova interação ao histórico de um serviço
Route::post('/add-history', 'ApiController@addHistory');

// Retorna uma lista com os IDs de todos os serviços (limitado a 20)
Route::get('/servicos/ids', 'ApiController@getAllServiceIds');

// Rota geral (mantém todas as verificações em um único retorno)
Route::get('/check-expirations', 'WebhookCheckController@index');

// Relatórios Gerenciais (Novas APIs)
Route::get('/management/summary', 'ManagementReportController@summary');
Route::get('/management/proposals-aging', 'ManagementReportController@proposalsAging');
Route::get('/management/expirations', 'ManagementReportController@expirations');
Route::get('/management/collaborators-pendencias', 'ManagementReportController@collaboratorsPendencias');
Route::get('/management/analyst-workload', 'ManagementReportController@analystWorkload');
Route::get('/management/operational-indicators', 'ManagementReportController@operationalIndicators');
Route::get('/management/tax-conference', 'ManagementReportController@taxConference');
Route::get('/management/system-inconsistencies', 'ManagementReportController@systemInconsistencies');

// NFS-e (backend only)
Route::prefix('nfse')->group(function () {
    Route::get('/configuracoes', 'NfseController@listConfigs');
    Route::post('/configuracoes', 'NfseController@upsertConfig');

    Route::get('/faturamentos/{faturamentoId}/servicos', 'NfseController@faturamentoServicos');
    Route::get('/faturamentos/{faturamentoId}/status', 'NfseController@statusFaturamento');

    Route::post('/emitir-automatico', 'NfseController@emitirAutomatico');
    Route::post('/anexar-manual', 'NfseController@anexarManual');
    Route::post('/nao-emitir', 'NfseController@naoEmitir');

    Route::post('/webhooks/plugnotas', 'NfseController@webhookPlugNotas');
    Route::get('/emissoes/{emissionId}/zip', 'NfseController@gerarZip');
});
