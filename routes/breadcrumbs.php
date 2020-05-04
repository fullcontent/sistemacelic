<?php

Breadcrumbs::for('dashboard', function ($trail) {
    $trail->push('Dashboard', route('dashboard'));
});


Breadcrumbs::for('empresas.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Empresas', route('empresas.index'));
});

Breadcrumbs::for('empresa.unidades', function ($trail,$id) {

	$empresa = \App\Models\Empresa::findOrFail($id);
    $trail->parent('dashboard');
    $trail->push($empresa->nomeFantasia, route('empresa.unidades',$empresa->id));
});


Breadcrumbs::for('empresas.show', function ($trail, $id) {
    $empresa = \App\Models\Empresa::findOrFail($id);
    $trail->parent('dashboard');
    $trail->push($empresa->nomeFantasia, route('empresas.show',$id));
});


Breadcrumbs::for('unidades.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Unidades', route('unidades.index'));
});


Breadcrumbs::for('unidades.show', function ($trail,$id) {
	$unidade = \App\Models\Unidade::findOrFail($id);
	$empresa = \App\Models\Empresa::findOrFail($unidade->empresa_id);
    $trail->parent('empresas.show',$empresa->id);
    $trail->push($unidade->nomeFantasia, route('unidades.show',$id));
});

Breadcrumbs::for('servico.lista', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Serviços', route('servico.lista'));
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
    $trail->push('Serviços de unidades inativas', route('servico.inativos'));
});

Breadcrumbs::for('servicos.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Listagem geral dos serviços', route('servicos.index'));
});

Breadcrumbs::for('servicos.show', function ($trail, $id) {

	$servico = \App\Models\Servico::findOrFail($id);
    $trail->parent('unidades.show',$servico->unidade_id);
    $trail->push($servico->os, route('servicos.show',$id));
});

Breadcrumbs::for('taxas.show', function ($trail, $id) {
	$taxa = \App\Models\Taxa::findOrFail($id);
    $trail->parent('servicos.show',$taxa->servico->id);
    $trail->push($taxa->nome, route('taxas.show',$taxa->id));
});


