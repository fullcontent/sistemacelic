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


		Route::get('/relatorio','AdminController@relatorioCompleto');


		Route::resource('/empresas','EmpresasController');
		Route::resource('/unidades','UnidadesController');
		Route::resource('/servicos','ServicosController');
		Route::resource('/taxas','TaxasController');
		Route::resource('/pendencia','PendenciasController');
		Route::resource('/arquivo','ArquivosController');
		

		
		Route::get('/faturamentos', 'FaturamentoController@index')->name('faturamentos.index');
		Route::get('/faturamento/show/{id}', 'FaturamentoController@show')->name('faturamento.show');

		Route::get('/faturamento/delete/{id}', 'FaturamentoController@destroy')->name('faturamento.destroy');

		Route::post('/faturamento/addNF','FaturamentoController@addNF')->name('faturamento.addNF');


		Route::get('/faturamento/create','FaturamentoController@create')->name('faturamento.create');
		Route::post('/faturamento/step2','FaturamentoController@step2')->name('faturamento.step2');
		Route::post('/faturamento/step3','FaturamentoController@step3')->name('faturamento.step3');
		Route::post('/faturamento/step4','FaturamentoController@step4')->name('faturamento.step4');


		
		Route::get('/reembolsos', 'ReembolsoController@index')->name('reembolsos.index');
		Route::get('/reembolso/show/{id}', 'ReembolsoController@show')->name('reembolso.show');
		Route::get('/reembolso/delete/{id}', 'ReembolsoController@destroy')->name('reembolso.destroy');

		Route::get('/reembolso/create','ReembolsoController@create')->name('reembolso.create');
		Route::post('/reembolso/step2','ReembolsoController@step2')->name('reembolso.step2');
		Route::post('/reembolso/step3','ReembolsoController@step3')->name('reembolso.step3');
		Route::post('/reembolso/step4','ReembolsoController@step4')->name('reembolso.step4');


		Route::get('/empresa/{empresa}/unidades','EmpresasController@unidades')->name('empresa.unidades');
		Route::get('/empresa/cadastro', 'EmpresasController@cadastro')->name('empresa.cadastro');
		Route::get('/unidade/cadastro', 'UnidadesController@cadastro')->name('unidade.cadastro');
		Route::post('/unidade/{id}', 'UnidadesController@editar')->name('unidade.editar');
		Route::get('/unidade/{id}', 'UnidadesController@delete')->name('unidade.delete');
		Route::post('/empresa/{id}', 'EmpresasController@editar')->name('empresa.editar');

		Route::get('/empresa/{id}', 'EmpresasController@delete')->name('empresa.delete');

		Route::get('/usuarios', 'UsersController@index')->name('usuarios.index');
		Route::get('/usuario/cadastro', 'UsersController@cadastro')->name('usuario.cadastro');
		Route::get('/usuario/editar/{id}', 'UsersController@editar')->name('usuario.editar');
		Route::post('/usuario/editar/{id}', 'UsersController@update')->name('usuario.update');
		Route::post('/usuario','UsersController@store')->name('usuario.store');
		Route::get('/usuario/delete/{id}', 'UsersController@delete')->name('usuario.delete');

		Route::get('/servico/delete/{id}','ServicosController@delete')->name('servico.delete');

		Route::get('/pendencia/done/{id}', 'PendenciasController@done')->name('pendencia.done');
		Route::get('/pendencia/undone/{id}', 'PendenciasController@undone')->name('pendencia.undone');
		Route::get('/pendencia/create/{servico_id}', 'PendenciasController@create')->name('pendencia.create');
		Route::get('/pendencia/delete/{id}', 'PendenciasController@delete')->name('pendencia.delete');
		
		Route::get('/pendencias/minhas', 'PendenciasController@minhas')->name('pendencias.minhas');
		Route::get('/pendencias/outras', 'PendenciasController@outras')->name('pendencias.outras');


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
Route::get('/usuario/editar/', 'ClienteController@editarUsuario')->name('cliente.usuario.editar');
Route::post('/usuario/editar/', 'ClienteController@updateUsuario')->name('cliente.usuario.update');
Route::get('/servico/{id}/taxas/{taxa}','ClienteController@showTaxa')->name('cliente.taxa.show');

Route::post('/arquivo/anexar','ArquivosController@anexar')->name('cliente.arquivo.anexar');

			

	});

Route::get('/teste', function() {

		// $licenca = Servico::where('tipo','primario')->where('situacao','finalizado')->whereDate('licenca_validade','=',Carbon::now()->addDays(60))->get();

			$l = App\Models\Servico::find(589);

            $user = App\User::find($l->responsavel_id);
            $user->notify(new App\Notifications\Licenca60days($l,$user)); 

            return new App\Mail\VencimentoLicenca60days($l);

       


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
