<?php

Breadcrumbs::for('dashboard', function ($trail) {
    $trail->push('<i class="fas fa-home"></i> Dashboard', route('dashboard'));
});

Breadcrumbs::for('empresas.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-building"></i> Empresas', route('empresas.index'));
});




Breadcrumbs::for('empresa.unidades', function ($trail, $id) {
    $empresa = \App\Models\Empresa::findOrFail($id);
    $trail->parent('empresas.index');
    $trail->push($empresa->nomeFantasia, route('empresa.unidades',$id));
});

Breadcrumbs::for('empresa.editar', function ($trail, $id) {
    $empresa = \App\Models\Empresa::findOrFail($id);
    $trail->parent('empresa.unidades', $id);
    $trail->push('<i class="fas fa-edit"></i> Editar', route('empresa.editar', $id));
});


Breadcrumbs::for('unidades.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-store"></i> Unidades', route('unidades.index'));
});


Breadcrumbs::for('unidades.show', function ($trail,$id) {
	$unidade = \App\Models\Unidade::findOrFail($id);
	$empresa = \App\Models\Empresa::findOrFail($unidade->empresa_id);
    $trail->parent('empresa.unidades',$empresa->id);
    $trail->push($unidade->nomeFantasia, route('unidades.show',$id));
});

Breadcrumbs::for('unidades.edit', function ($trail,$id) {
	$unidade = \App\Models\Unidade::findOrFail($id);
	$empresa = \App\Models\Empresa::findOrFail($unidade->empresa_id);
    $trail->parent('empresa.unidades',$empresa->id);
    $trail->push($unidade->nomeFantasia, route('unidades.show',$id));
});

Breadcrumbs::for('servico.lista', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-tools"></i> Serviços', route('servico.lista'));
});

Breadcrumbs::for('servico.andamento', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Serviços em andamento', route('servico.andamento'));
});

Breadcrumbs::for('servico.vigente', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Serviços vigentes', route('servico.vigente'));
});

Breadcrumbs::for('servico.vencer', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Serviços a vencer', route('servico.vencer'));
});

Breadcrumbs::for('servico.finalizados', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Serviços finalizados', route('servico.finalizados'));
});

Breadcrumbs::for('servico.arquivado', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Serviços arquivados', route('servico.arquivado'));
});

Breadcrumbs::for('servico.vencido', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Serviços vencidos', route('servico.vencido'));
});

Breadcrumbs::for('servico.inativos', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-power-off"></i> Serviços de unidades inativas', route('servico.inativos'));
});

Breadcrumbs::for('servicos.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-list"></i> Listagem geral dos serviços', route('servicos.index'));
});

Breadcrumbs::for('servico.create', function ($trail, $unidade_id) {
    $unidade = \App\Models\Unidade::findOrFail($unidade_id);
    $trail->parent('unidades.show', $unidade_id);
    $trail->push('<i class="fas fa-plus"></i> Novo Serviço', route('servico.create', $unidade_id));
});

Breadcrumbs::for('servicos.show', function ($trail, $id) {

	$servico = \App\Models\Servico::findOrFail($id);
    $trail->parent('unidades.show',$servico->unidade_id);
    $trail->push($servico->os, route('servicos.show',$id));
});

Breadcrumbs::for('servicos.edit', function ($trail, $id) {

	$servico = \App\Models\Servico::findOrFail($id);
    $trail->parent('servicos.show',$servico->id);
    $trail->push($servico->os, route('servicos.show',$id));
});

Breadcrumbs::for('servico.ai-summary', function ($trail, $id) {
    $servico = \App\Models\Servico::findOrFail($id);
    $trail->parent('servicos.show', $id);
    $trail->push('<i class="fas fa-robot"></i> Resumo AI', route('servico.ai-summary', $id));
});

Breadcrumbs::for('taxas.show', function ($trail, $id) {
	$taxa = \App\Models\Taxa::findOrFail($id);
    $trail->parent('servicos.show',$taxa->servico->id);
    $trail->push('<i class="fas fa-receipt"></i> ' . $taxa->nome, route('taxas.show',$taxa->id));
});

Breadcrumbs::for('pendencia.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-tasks"></i> Pendências', route('pendencia.index'));
});

Breadcrumbs::for('pendencias.minhas', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-user-check"></i> Minhas Pendências', route('pendencias.minhas'));
});

Breadcrumbs::for('pendencias.outras', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-users-cog"></i> Outras Pendências', route('pendencias.outras'));
});

Breadcrumbs::for('pendencias.vinculadas', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-link"></i> Pendências Vinculadas', route('pendencias.vinculadas'));
});

Breadcrumbs::for('pendencia.show', function ($trail, $id) {
    $pendencia = \App\Models\Pendencia::findOrFail($id);
    $trail->parent('servicos.show', $pendencia->servico_id);
    $trail->push('<i class="fas fa-exclamation-circle"></i> Pendência #' . $pendencia->id, route('pendencia.show', $id));
});

Breadcrumbs::for('proposta.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-file-contract"></i> Propostas', route('proposta.index'));
});

Breadcrumbs::for('proposta.create', function ($trail) {
    $trail->parent('proposta.index');
    $trail->push('<i class="fas fa-plus"></i> Nova Proposta', route('proposta.create'));
});

Breadcrumbs::for('proposta.show', function ($trail, $id) {
    $proposta = \App\Models\Proposta::findOrFail($id);
    $trail->parent('proposta.index');
    $trail->push('<i class="fas fa-file-contract"></i> Proposta #' . $proposta->id, route('proposta.show', $id));
});

Breadcrumbs::for('proposta.edit', function ($trail, $id) {
    $proposta = \App\Models\Proposta::findOrFail($id);
    $trail->parent('proposta.index');
    $trail->push('<i class="fas fa-edit"></i> Editar Proposta #' . $id, route('proposta.edit', $id));
});

Breadcrumbs::for('ordemCompra.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-shopping-cart"></i> Ordens de Compra', route('ordemCompra.index'));
});

Breadcrumbs::for('prestador.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-user-tie"></i> Prestadores', route('prestador.index'));
});

Breadcrumbs::for('prestador.create', function ($trail) {
    $trail->parent('prestador.index');
    $trail->push('<i class="fas fa-plus"></i> Novo Prestador', route('prestador.create'));
});

Breadcrumbs::for('prestador.edit', function ($trail, $id) {
    $prestador = \App\Models\Prestador::findOrFail($id);
    $trail->parent('prestador.index');
    $trail->push('<i class="fas fa-edit"></i> Editar: ' . $prestador->nome, route('prestador.edit', $id));
});


Breadcrumbs::for('faturamentos.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-file-invoice-dollar"></i> Faturamentos', route('faturamentos.index'));
});

Breadcrumbs::for('faturamento.create', function ($trail) {
    $trail->parent('faturamentos.index');
    $trail->push('<i class="fas fa-plus"></i> Novo Faturamento', route('faturamento.create'));
});

Breadcrumbs::for('faturamento.step2', function ($trail) {
    $trail->parent('faturamento.create');
    $trail->push('Passo 2: Seleção de Itens');
});

Breadcrumbs::for('faturamento.step3', function ($trail) {
    $trail->parent('faturamento.create');
    $trail->push('Passo 3: Confirmar Detalhes');
});

Breadcrumbs::for('faturamento.step4', function ($trail) {
    $trail->parent('faturamento.create');
    $trail->push('Passo 4: Finalizado');
});

Breadcrumbs::for('faturamento.show', function ($trail, $id) {
	$faturamento = \App\Models\Faturamento::findOrFail($id);
    $trail->parent('faturamentos.index');
    $trail->push($faturamento->nome, route('faturamento.show',$faturamento->id));
});

// Reembolsos
Breadcrumbs::for('reembolsos.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-hand-holding-usd"></i> Reembolsos', route('reembolsos.index'));
});

Breadcrumbs::for('reembolso.create', function ($trail) {
    $trail->parent('reembolsos.index');
    $trail->push('<i class="fas fa-plus"></i> Novo Reembolso', route('reembolso.create'));
});

Breadcrumbs::for('reembolso.show', function ($trail, $id) {
    $reembolso = \App\Models\Reembolso::findOrFail($id);
    $trail->parent('reembolsos.index');
    $trail->push('<i class="fas fa-file-invoice"></i> Reembolso #' . $reembolso->id, route('reembolso.show', $id));
});

Breadcrumbs::for('reembolso.step2', function ($trail) {
    $trail->parent('reembolso.create');
    $trail->push('Passo 2: Seleção de Itens');
});

Breadcrumbs::for('reembolso.step3', function ($trail) {
    $trail->parent('reembolso.create');
    $trail->push('Passo 3: Confirmar Detalhes');
});

Breadcrumbs::for('reembolso.step4', function ($trail) {
    $trail->parent('reembolso.create');
    $trail->push('Passo 4: Finalizado');
});

// Solicitantes
Breadcrumbs::for('solicitantes.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-user-tag"></i> Solicitantes', route('solicitantes.index'));
});

Breadcrumbs::for('solicitantes.create', function ($trail) {
    $trail->parent('solicitantes.index');
    $trail->push('<i class="fas fa-plus"></i> Cadastrar Solicitante', route('solicitantes.create'));
});

Breadcrumbs::for('solicitantes.edit', function ($trail, $id) {
    $solicitante = \App\Models\Solicitante::findOrFail($id);
    $trail->parent('solicitantes.index');
    $trail->push('<i class="fas fa-edit"></i> Editar: ' . $solicitante->nome, route('solicitantes.edit', $id));
});

// Usuários
Breadcrumbs::for('usuarios.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-users"></i> Usuários', route('usuarios.index'));
});

Breadcrumbs::for('usuario.cadastro', function ($trail) {
    $trail->parent('usuarios.index');
    $trail->push('<i class="fas fa-user-plus"></i> Novo Usuário', route('usuario.cadastro'));
});

Breadcrumbs::for('usuario.editar', function ($trail, $id) {
    $usuario = \App\User::findOrFail($id);
    $trail->parent('usuarios.index');
    $trail->push('<i class="fas fa-user-edit"></i> Editar: ' . $usuario->name, route('usuario.editar', $id));
});

// Outras ações
Breadcrumbs::for('empresa.cadastro', function ($trail) {
    $trail->parent('empresas.index');
    $trail->push('<i class="fas fa-plus"></i> Nova Empresa', route('empresa.cadastro'));
});

Breadcrumbs::for('unidade.cadastro', function ($trail) {
    $trail->parent('unidades.index');
    $trail->push('<i class="fas fa-plus"></i> Nova Unidade', route('unidade.cadastro'));
});

Breadcrumbs::for('ordemCompra.criar', function ($trail, $servico_id) {
    $servico = \App\Models\Servico::findOrFail($servico_id);
    $trail->parent('servicos.show', $servico_id);
    $trail->push('<i class="fas fa-shopping-cart"></i> Nova Ordem de Compra', route('ordemCompra.criar', $servico_id));
});

// Relatórios
Breadcrumbs::for('relatorios.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('<i class="fas fa-file-invoice"></i> Relatórios', route('relatorios.index'));
});

Breadcrumbs::for('relatorio.taxas', function ($trail) {
    $trail->parent('relatorios.index');
    $trail->push('<i class="fas fa-chart-pie"></i> Relatório de Taxas', route('relatorio.taxas'));
});

Breadcrumbs::for('relatorio.pendencias', function ($trail) {
    $trail->parent('relatorios.index');
    $trail->push('<i class="fas fa-chart-line"></i> Relatório de Pendências', route('relatorio.pendencias'));
});

Breadcrumbs::for('relatorio.arquivos', function ($trail) {
    $trail->parent('relatorios.index');
    $trail->push('<i class="fas fa-file-archive"></i> Relatório de Arquivos', route('relatorio.arquivos'));
});

//Breadcrumbs for cliente


Breadcrumbs::for('cliente.home', function($trail){
    $trail->push('<i class="fas fa-home"></i> Dashboard', route('cliente.home'));
});

Breadcrumbs::for('cliente.empresa.unidades',function($trail, $id){

    $empresa = \App\Models\Empresa::findOrFail($id);
    $trail->parent('cliente.home');
    $trail->push($empresa->nomeFantasia, route('cliente.empresa.show',$empresa->id));
});

Breadcrumbs::for('cliente.unidades',function($trail){
    $trail->parent('cliente.home');
    $trail->push('<i class="fas fa-store"></i> Unidades', route('cliente.unidades'));
});

Breadcrumbs::for('cliente.empresas',function($trail){
    $trail->parent('cliente.home');
    $trail->push('<i class="fas fa-building"></i> Empresas', route('cliente.empresas'));
});


Breadcrumbs::for('cliente.unidade.show',function($trail, $id){

    $unidade = \App\Models\Unidade::findOrFail($id);
    $trail->parent('cliente.empresa.unidades',$unidade->empresa_id);
    $trail->push($unidade->nomeFantasia, route('cliente.unidade.show',$unidade->id));
});

Breadcrumbs::for('cliente.empresa.show',function($trail, $id){

    $empresa = \App\Models\Empresa::findOrFail($id);
    $trail->parent('cliente.home',$empresa->id);
    $trail->push($empresa->nomeFantasia, route('cliente.empresa.show',$empresa->id));
});




Breadcrumbs::for('cliente.servico.show', function ($trail, $id) {

	$servico = \App\Models\Servico::findOrFail($id);
    $trail->parent('cliente.unidade.show',$servico->unidade_id);
    $trail->push($servico->os, route('cliente.servico.show',$id));
});

Breadcrumbs::for('cliente.taxa.show', function ($trail, $id, $taxa_id) {
	$taxa = \App\Models\Taxa::findOrFail($taxa_id);
    $trail->parent('cliente.servico.show',$taxa->servico_id);
    $trail->push('<i class="fas fa-receipt"></i> ' . $taxa->nome, route('cliente.taxa.show',['id'=>$taxa->servico_id,'taxa'=>$taxa->id]));
});

Breadcrumbs::for('cliente.pendencia.show', function ($trail, $id) {
    $pendencia = \App\Models\Pendencia::findOrFail($id);
    $trail->parent('cliente.servico.show', $pendencia->servico_id);
    $trail->push('<i class="fas fa-exclamation-circle"></i> Pendência #' . $pendencia->id, route('cliente.pendencia.show', $id));
});



Breadcrumbs::for('cliente.interacoes.lista', function ($trail, $id) {
	$servico = \App\Models\Servico::findOrFail($id);
    $trail->parent('cliente.servico.show',$servico->id);
    $trail->push('<i class="fas fa-comments"></i> Interações', route('cliente.interacoes.lista',$id));
});

Breadcrumbs::for('cliente.servico.finalizado', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('<i class="fas fa-check-double"></i> Serviços finalizados', route('cliente.servico.finalizado'));
});

Breadcrumbs::for('cliente.servico.andamento', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('<i class="fas fa-spinner"></i> Serviços em andamento', route('cliente.servico.andamento'));
});

Breadcrumbs::for('cliente.servico.inativo', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('<i class="fas fa-power-off"></i> Serviços inativos', route('cliente.servico.inativo'));
});

Breadcrumbs::for('cliente.servico.vencer', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('<i class="fas fa-hourglass-half"></i> Serviços vencer', route('cliente.servico.vencer'));
});

Breadcrumbs::for('cliente.servico.vencido', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('<i class="fas fa-calendar-times"></i> Serviços vencidos', route('cliente.servico.vencido'));
});

Breadcrumbs::for('cliente.servico.vigente', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('<i class="fas fa-calendar-check"></i> Serviços vigentes', route('cliente.servico.vigente'));
});

Breadcrumbs::for('cliente.servicos', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('<i class="fas fa-list"></i> Todos os serviços', route('cliente.servicos'));
});