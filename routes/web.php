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

Auth::routes();

Route::get('/', function () {
	return view('auth.login');
});


	Route::prefix('admin')->group(function () {

		
		Route::get('/home', function() {
		return view('admin.dashboard');
		})->name('home');

		Route::resource('/empresas','EmpresasController');
		Route::resource('/unidades','UnidadesController');
		Route::resource('/servicos','ServicosController');
		Route::get('/empresa/{empresa}/unidades','EmpresasController@unidades')->name('empresa.unidades');
		Route::get('/empresa/cadastro', 'EmpresasController@cadastro')->name('empresa.cadastro');
		Route::get('/unidade/cadastro', 'UnidadesController@cadastro')->name('unidade.cadastro');
		Route::post('/unidade/{id}', 'UnidadesController@editar')->name('unidade.editar');
		Route::post('/empresa/{id}', 'EmpresasController@editar')->name('empresa.editar');
		Route::get('/usuarios', 'UsersController@index')->name('usuarios.index');
		Route::get('/usuario/cadastro', 'UsersController@cadastro')->name('usuario.cadastro');
		Route::get('/usuario/editar/{id}', 'UsersController@editar')->name('usuario.editar');
		Route::post('/usuario/editar/{id}', 'UsersController@update')->name('usuario.update');
		Route::post('/usuario','UsersController@store')->name('usuario.store');
		Route::post('salvarInteracao', 'ServicosController@salvarInteracao')->name('interacao.store');

	});

	Route::prefix('cliente')->group(function () {

			Route::get('/home', 'ClienteController@index');

			Route::get('/empresas', 'ClienteController@empresas')->name('empresas');
			Route::get('/empresa/{id}', 'ClienteController@empresaShow')->name('empresa.show');
			Route::get('/empresa/{id}/unidades', 'ClienteController@empresaUnidades')->name('empresa.unidades');




			Route::get('/unidades', 'ClienteController@unidades')->name('unidades');
			Route::get('/unidade/{id}', 'ClienteController@unidadeShow')->name('unidade.show');


			Route::get('/servicos', 'ClienteController@servicos')->name('servicos');
			Route::get('/servico/{id}', 'ClienteController@servicoShow')->name('servico.show');
			Route::post('salvarInteracao', 'ClienteController@salvarInteracao')->name('interacao.salvar');



			Route::get('/usuarios', 'ClienteController@usuarios')->name('usuarios');

	});


