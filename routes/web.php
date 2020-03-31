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

		
		

		Route::get('/home', 'AdminController@index')->name('dashboard');



		Route::resource('/empresas','EmpresasController');
		Route::resource('/unidades','UnidadesController');
		Route::resource('/servicos','ServicosController');
		Route::resource('/taxas','TaxasController');
		Route::resource('/pendencia','PendenciasController');
		Route::resource('/arquivo','ArquivosController');
		


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
		Route::get('/servico/delete/{id}','ServicosController@delete')->name('servico.delete');

		Route::get('/pendencia/done/{id}', 'PendenciasController@done')->name('pendencia.done');
		Route::get('/pendencia/undone/{id}', 'PendenciasController@undone')->name('pendencia.undone');
		Route::get('/pendencia/create/{servico_id}', 'PendenciasController@create')->name('pendencia.create');
		Route::get('/pendencia/delete/{id}', 'PendenciasController@delete')->name('pendencia.delete');
		Route::get('/taxa/delete/{id}', 'TaxasController@delete')->name('taxas.delete');


		Route::get('/servico/andamento/', 'ServicosController@listaAndamento')->name('servico.andamento');
		Route::get('/servico/finalizados/', 'ServicosController@listaFinalizados')->name('servico.finalizado');
		Route::get('/servico/vigentes/', 'ServicosController@listaVigentes')->name('servico.vigente');
		Route::get('/servico/vencidos/', 'ServicosController@listaVencidos')->name('servico.vencido');
		Route::get('/servico/vencer/', 'ServicosController@listaVencer')->name('servico.vencer');
		Route::get('/servico/inativos/', 'ServicosController@listaInativo')->name('servico.inativo');
		Route::get('/servico/lista/', 'ServicosController@lista')->name('servico.lista');
		Route::get('/servico/arquivados/', 'ServicosController@listaArquivados')->name('servico.arquivado');

		Route::get('/servico/renovar/{id}', 'ServicosController@renovar')->name('servico.renovar');


		Route::get('/arquivo/download/{id}', 'ArquivosController@download')->name('arquivo.download');
		Route::get('/arquivo/delete/{id}', 'ArquivosController@delete')->name('arquivo.delete');


		Route::post('salvarInteracao', 'ServicosController@salvarInteracao')->name('interacao.store');


		
		Route::get('/servico/{id}/interacoes', 'ServicosController@interacoes')->name('interacoes.lista');


		
		

	});

	Route::prefix('cliente')->group(function () {

Route::get('/home', 'ClienteController@index')->name('cliente.home');
Route::get('/empresas', 'ClienteController@empresas')->name('cliente.empresas');
Route::get('/empresa/{id}', 'ClienteController@empresaShow')->name('cliente.empresa.show');
Route::get('/empresa/{id}/unidades', 'ClienteController@empresaUnidades')->name('cliente.empresa.unidades');
Route::get('/unidades', 'ClienteController@unidades')->name('cliente.unidades');
Route::get('/unidade/{id}', 'ClienteController@unidadeShow')->name('cliente.unidade.show');
Route::get('/servicos', 'ClienteController@servicos')->name('cliente.servicos');
Route::get('/servico/andamento/', 'ClienteController@listaAndamento')->name('cliente.servico.andamento');
Route::get('/servico/finalizados/', 'ClienteController@listaFinalizados')->name('cliente.servico.finalizado');
Route::get('/servico/vigentes/', 'ClienteController@listaVigentes')->name('cliente.servico.vigente');
Route::get('/servico/vencidos/', 'ClienteController@listaVencidos')->name('cliente.servico.vencido');
Route::get('/servico/vencer/', 'ClienteController@listaVencer')->name('cliente.servico.vencer');
Route::get('/servico/inativos/', 'ClienteController@listaInativo')->name('cliente.servico.inativo');
Route::get('/servico/{id}', 'ClienteController@servicoShow')->name('cliente.servico.show');
Route::post('salvarInteracao', 'ClienteController@salvarInteracao')->name('cliente.interacao.salvar');
Route::get('/servico/{id}/interacoes', 'ClienteController@interacoes')->name('cliente.interacoes.lista');

			

	});

Route::get('/teste', function() {

		return \Carbon\Carbon::now()->addDays(60);

});


Route::get('/taxa/notify', 'TaxasController@notifyUser');


Route::get('/markAsRead', function()
{
	
	$notif_id = $_GET['notif_id'];
	auth()->user()->unreadNotifications->where('id', $notif_id)->markAsRead();
	return \Response::json($notif_id, 200);

});

Route::get('clearNotifications', function(){

	auth()->user()->notifications()->delete();
	return redirect()->back();
})->name('clearNotifications');
