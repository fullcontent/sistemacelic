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
