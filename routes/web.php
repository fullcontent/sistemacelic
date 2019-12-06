<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', function() {
    return view('home');
})->name('home');


//Admin Routes

Route::resource('/empresas','EmpresasController');
Route::resource('/unidades','UnidadesController');
Route::resource('/servicos','ServicosController');


Route::get('/empresa/{empresa}/unidades','EmpresasController@unidades')->name('empresa.unidades');



//Test Routes

Route::get('/empresa/cadastro', function ()
{	
	
	return view ('admin.cadastro-empresa');
});

Route::post('/unidade/{id}', 'UnidadesController@editar')->name('unidade.editar');