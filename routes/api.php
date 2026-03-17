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

// Verifica taxas vencendo hoje/amanhã
Route::get('/check-taxas', 'WebhookCheckController@taxas');

// Verifica pendências com prazo hoje/amanhã
Route::get('/check-pendencias', 'WebhookCheckController@pendencias');

// Verifica licenciamentos vencendo em 30, 60, 90 ou 120 dias
Route::get('/check-licencas', 'WebhookCheckController@licencas');

// Rota geral (mantém todas as verificações em um único retorno)
Route::get('/check-expirations', 'WebhookCheckController@index');
