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

		Route::resource('/solicitantes','SolicitantesController');

		Route::get('/solicitantes/delete/{id}', 'SolicitantesController@destroy')->name('solicitantes.destroy');
		

		
		Route::get('/faturamentos', 'FaturamentoController@index')->name('faturamentos.index');
		Route::get('/faturamento/show/{id}', 'FaturamentoController@show')->name('faturamento.show');

		Route::get('/faturamento/delete/{id}', 'FaturamentoController@destroy')->name('faturamento.destroy');

		Route::post('/faturamento/addNF','FaturamentoController@addNF')->name('faturamento.addNF');

		Route::post('/faturamento/editNF','FaturamentoController@update')->name('faturamento.editNF');


		Route::get('/faturamento/create','FaturamentoController@create')->name('faturamento.create');
		Route::post('/faturamento/step2','FaturamentoController@step2')->name('faturamento.step2');
		Route::post('/faturamento/step3','FaturamentoController@step3')->name('faturamento.step3');
		Route::post('/faturamento/step4','FaturamentoController@step4')->name('faturamento.step4');

		Route::post('/faturamento/faturarServicos','FaturamentoController@faturarServicoSub')->name('faturamento.faturarServicoSub');



		Route::get('/faturamento/servicosFinalizados','FaturamentoController@getAllServicesFinished');

		Route::get('/faturamento/servicosErro','FaturamentoController@getErrors');
		Route::get('/faturamento/getPropostas/{id}','FaturamentoController@getPropostas');



		
		Route::resource('/proposta','PropostasController');
		
		Route::get('/proposta/removerServico/{id}','PropostasController@removerServico');	
		
		
		Route::get('/proposta/analisar/{id}','PropostasController@analisar');

		Route::get('/proposta/recusar/{id}','PropostasController@recusar');
		Route::get('/proposta/aprovar/{id}/{s}','PropostasController@aprovar');

		Route::get('/proposta/remover/{id}', 'PropostasController@removerProposta')->name('removerProposta');

		Route::get('/proposta/pdf/{id}','PropostasController@printPDF')->name('propostaPDF');


		
		Route::get('/reembolsos', 'ReembolsoController@index')->name('reembolsos.index');
		Route::get('/reembolso/show/{id}', 'ReembolsoController@show')->name('reembolso.show');
		Route::get('/reembolso/delete/{id}', 'ReembolsoController@destroy')->name('reembolso.destroy');

		Route::get('/reembolso/create','ReembolsoController@create')->name('reembolso.create');
		Route::post('/reembolso/step2','ReembolsoController@step2')->name('reembolso.step2');
		Route::post('/reembolso/step3','ReembolsoController@step3')->name('reembolso.step3');
		Route::post('/reembolso/step4','ReembolsoController@step4')->name('reembolso.step4');

		Route::get('/reembolso/{id}/download','ReembolsoController@download')->name('reembolso.download');
		Route::get('/reembolso/{id}/downloadZip','ReembolsoController@downloadZip')->name('reembolso.downloadZip');


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

		Route::get('/pendencia/priority/{id}', 'PendenciasController@priority')->name('pendencia.priority');
		Route::get('/pendencia/unPriority/{id}', 'PendenciasController@unPriority')->name('pendencia.unPriority');
		
		Route::get('/pendencia/removerVinculo/{id}/{servico_id}', 'PendenciasController@removerVinculo')->name('pendencia.removerVinculo');


		Route::get('/pendencia/create/{servico_id}', 'PendenciasController@create')->name('pendencia.create');
		Route::get('/pendencia/delete/{id}', 'PendenciasController@delete')->name('pendencia.delete');
		
		Route::get('/pendencias/minhas', 'PendenciasController@minhas')->name('pendencias.minhas');
		Route::get('/pendencias/outras', 'PendenciasController@outras')->name('pendencias.outras');
		Route::get('/pendencias/vinculadas', 'PendenciasController@vinculadas')->name('pendencias.vinculadas');


		Route::get('/taxa/delete/{id}', 'TaxasController@delete')->name('taxas.delete');
		Route::get('/taxa/removerComprovante/{id}', 'TaxasController@removerComprovante')->name('taxas.removerComprovante');
		Route::get('/taxa/removerBoleto/{id}', 'TaxasController@removerBoleto')->name('taxas.removerBoleto');



		Route::get('/servico/andamento/', 'ServicosController@listaAndamento')->name('servico.andamento');
		Route::get('/servico/finalizados/', 'ServicosController@listaFinalizados')->name('servico.finalizado');
		Route::get('/servico/vigentes/', 'ServicosController@listaVigentes')->name('servico.vigente');
		Route::get('/servico/vencidos/', 'ServicosController@listaVencidos')->name('servico.vencido');
		Route::get('/servico/vencer/', 'ServicosController@listaVencer')->name('servico.vencer');
		Route::get('/servico/inativos/', 'ServicosController@listaInativo')->name('servico.inativo');
		Route::get('/servico/lista/', 'ServicosController@lista')->name('servico.lista');
		Route::get('/servico/arquivados/', 'ServicosController@listaArquivados')->name('servico.arquivado');
		Route::get('/servico/nRenovados/', 'ServicosController@listaNrenovados')->name('servico.nRenovado');

		Route::get('/servico/renovar/{id}', 'ServicosController@renovar')->name('servico.renovar');
		Route::get('/servico/desconsiderar/{id}', 'ServicosController@desconsiderar')->name('servico.desconsiderar');

		Route::get('/servico/download/{tipo}/{servico_id}', 'ArquivosController@downloadFile')->name('servico.downloadFile');

		Route::get('/servico/download/{file}', 'ArquivosController@downloadBoleto')->name('servico.downloadBoleto');


		Route::get('/arquivo/download/{id}', 'ArquivosController@download')->name('arquivo.download');
		
		
		Route::get('/arquivo/delete/{id}', 'ArquivosController@delete')->name('arquivo.delete');


		Route::post('salvarInteracao', 'ServicosController@salvarInteracao')->name('interacao.store');


		Route::get('/servico/removerLaudo/{id}', 'ServicosController@removerLaudo')->name('servico.removerLaudo');
		Route::get('/servico/removerProtocolo/{id}', 'ServicosController@removerProtocolo')->name('servico.removerProtocolo');
		Route::get('/servico/removerLicenca/{id}', 'ServicosController@removerLicenca')->name('servico.removerLicenca');


		
		Route::get('/servico/{id}/interacoes', 'ServicosController@interacoes')->name('interacoes.lista');


		Route::get('/users/list','UsersController@usersList')->name('users.list');

		Route::get('/relatorios','DashboardController@getLicencasVigentes');
		Route::get('/mapa','DashboardController@getOurClientsLocation');
		

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
Route::get('/users/list','ClienteController@usersList')->name('cliente.users.list');
Route::get('/relatorios',function(){

	return view('admin.relatorio');

});

	});

Route::get('admin/teste',function(){

		if($pdo = DB::connection('mysql')->getPdo()){
			dump("Server Hostinger OK");
		}
		else{
			dump("Server Hostinger OFF");	
		}
		if($pdo = DB::connection('mysql2')->getPdo()){
			dump("Server Locaweb OK");
		}
		else{
			dump("Server Locaweb OFF");
		}
				
		
	
	


});

Route::get('/notAllowed' , function(){

	return view('errors.403');

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

Route::get('clearMentions', function(){

	auth()->user()->notifications()->where('type','App\Notifications\UserMentioned')->delete();
	return redirect()->back();
})->name('clearMentions');



// APIS ROUTES


Route::get('api/unidades/get', 'ApiController@getUnidades');
Route::get('api/responsaveis/get', 'ApiController@getResponsaveis');
Route::get('api/servicosLpu/get', 'ApiController@getServicosLpu');
Route::get('api/servicosLpu/find', 'ApiController@getServicoLpuById');



//Test Routes

Route::get('solicitantes', function(){

	$solicitantes = \App\Models\Solicitante::get();

	foreach($solicitantes as $s)
	{
		dump($s->empresa);
	}


});



//Get all solicitantes from servicos

Route::get('solicitantes/todos', function(){

	$solicitantes = \App\Models\Servico::orderBy('solicitante')->get()->groupBy('solicitante');


	dump($solicitantes);
	

	

});