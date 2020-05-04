<?php

Breadcrumbs::for('dashboard', function ($trail) {
    $trail->push('Dashboard', route('dashboard'));
});


Breadcrumbs::for('empresas.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Empresas', route('empresas.index'));
});


Breadcrumbs::for('empresa.unidades', function ($trail, $id) {
    $empresa = \App\Models\Empresa::findOrFail($id);
    $trail->parent('dashboard');
    $trail->push($empresa->nomeFantasia, route('empresa.unidades',$id));
});


Breadcrumbs::for('unidades.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Unidades', route('unidades.index'));
});


Breadcrumbs::for('unidades.show', function ($trail,$id) {
	$unidade = \App\Models\Unidade::findOrFail($id);
	$empresa = \App\Models\Empresa::findOrFail($unidade->empresa_id);
    $trail->parent('empresa.unidades',$empresa->id);
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


//Breadcrumbs for cliente


Breadcrumbs::for('cliente.home', function($trail){

    $trail->push('Dashboard', route('cliente.home'));
});

Breadcrumbs::for('cliente.empresa.unidades',function($trail, $id){

    $empresa = \App\Models\Empresa::findOrFail($id);
    $trail->parent('cliente.home');
    $trail->push($empresa->nomeFantasia, route('cliente.empresa.show',$empresa->id));
});

Breadcrumbs::for('cliente.unidades',function($trail){

    $trail->parent('cliente.home');
    $trail->push('Unidades', route('cliente.unidades'));
});

Breadcrumbs::for('cliente.empresas',function($trail){

    $trail->parent('cliente.home');
    $trail->push('Empresas', route('cliente.empresas'));
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
    $trail->push($taxa->nome, route('cliente.taxa.show',['id'=>$taxa->servico_id,'taxa'=>$taxa->id]));
});



Breadcrumbs::for('cliente.interacoes.lista', function ($trail, $id) {

	$servico = \App\Models\Servico::findOrFail($id);
    $trail->parent('cliente.servico.show',$servico->id);
    $trail->push('Interações', route('cliente.interacoes.lista',$id));
});

Breadcrumbs::for('cliente.servico.finalizado', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('Serviços finalizados', route('cliente.servico.finalizado'));
});

Breadcrumbs::for('cliente.servico.andamento', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('Serviços em andamento', route('cliente.servico.andamento'));
});

Breadcrumbs::for('cliente.servico.inativo', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('Serviços inativos', route('cliente.servico.inativo'));
});

Breadcrumbs::for('cliente.servico.vencer', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('Serviços vencer', route('cliente.servico.vencer'));
});

Breadcrumbs::for('cliente.servico.vencido', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('Serviços vencidos', route('cliente.servico.vencido'));
});

Breadcrumbs::for('cliente.servico.vigente', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('Serviços vigentes', route('cliente.servico.vigente'));
});

Breadcrumbs::for('cliente.servicos', function ($trail) {
    $trail->parent('cliente.home');
    $trail->push('Todos os serviços', route('cliente.servicos'));
});