@extends('adminlte::page')

@section('title', 'Listagem de serviços')

@section('css')
<style>
	.table-container {
		background: #fff;
		border-radius: 8px;
		padding: 20px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
		border: 1px solid #ebf0f5;
		margin-bottom: 25px;
	}

	.btn-action {
		width: 32px;
		height: 32px;
		line-height: 32px;
		padding: 0;
		text-align: center;
		border-radius: 6px;
		margin: 0 2px;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		transition: all 0.2s;
		border: 1px solid #ddd;
		background: #fff;
	}

	.btn-action:hover {
		transform: scale(1.1);
		box-shadow: 0 2px 5px rgba(0,0,0,0.1);
		text-decoration: none;
	}

	.btn-filter-custom {
		border-radius: 50px;
		padding: 6px 16px;
		font-weight: 600;
		font-size: 0.9em;
		margin-right: 5px;
		margin-bottom: 5px;
		transition: all 0.2s;
	}

	.btn-filter-custom:hover {
		transform: translateY(-1px);
		box-shadow: 0 2px 4px rgba(0,0,0,0.05);
	}

	.content-header .breadcrumb { display: none !important; }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-12">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Listagem de Serviços</h1>
	</div>
</div>
@stop

@section('content')
<div class="table-container">
	@if(Route::is('servico.meus') || isset($situacaoAtual))
		<div style="margin-bottom: 20px; display: flex; flex-wrap: wrap;">
			<a href="{{ route('servico.meus', ['situacao' => 'ativos']) }}" class="btn btn-filter-custom {{ ($situacaoAtual ?? 'ativos') == 'ativos' ? 'btn-primary' : 'btn-default' }}">Ativos</a>
			<a href="{{ route('servico.meus', ['situacao' => 'andamento']) }}" class="btn btn-filter-custom {{ ($situacaoAtual ?? '') == 'andamento' ? 'btn-primary' : 'btn-default' }}">Em Andamento</a>
			<a href="{{ route('servico.meus', ['situacao' => 'finalizado']) }}" class="btn btn-filter-custom {{ ($situacaoAtual ?? '') == 'finalizado' ? 'btn-primary' : 'btn-default' }}">Finalizados</a>
			<a href="{{ route('servico.meus', ['situacao' => 'arquivado']) }}" class="btn btn-filter-custom {{ ($situacaoAtual ?? '') == 'arquivado' ? 'btn-primary' : 'btn-default' }}">Arquivados</a>
			<a href="{{ route('servico.meus', ['situacao' => 'antigos']) }}" class="btn btn-filter-custom {{ ($situacaoAtual ?? '') == 'antigos' ? 'btn-primary' : 'btn-default' }}"><i class="fa fa-archive"></i> Antigos (Concluídos/Arquivados)</a>
			<a href="{{ route('servico.meus', ['situacao' => 'todos']) }}" class="btn btn-filter-custom {{ ($situacaoAtual ?? '') == 'todos' ? 'btn-primary' : 'btn-default' }}">Todos</a>
		</div>
	@endif

	<table id="lista-servicos" class="table table-hover" style="width: 100%;">
		<thead>
			<tr style="background: #fcfcfc;">
				<th>Prioridade</th>
				<th>Cliente</th>
				<th>OS</th>
				<th>Serviço</th>
				<th>Venc. Licença</th>
				<th>Cod. Unid.</th>
				<th>Unidade</th>
				<th>Cidade</th>
				<th>Solicitante</th>
				<th>N° Protocolo</th>
				<th>Situação</th>
				<th width="100" class="text-center">Ações</th>
			</tr>
		</thead>
		<tbody>
			@foreach($servicos as $servico)
				<tr>
					<?php
					$tmp = \App\Models\Empresa::find($servico->unidade->empresa_id);
					?>
					
					<td>
						@if(isset($servico->prioridade) && $servico->prioridade == 1)
							<span class="label label-danger" data-toggle="tooltip" data-placement="top" title="{{ $servico->observacoes ?: 'Serviço com Urgência / Prioridade alta' }}">
								<i class="fa fa-exclamation-triangle"></i> Alta
							</span>
						@else
							<span class="label label-default" data-toggle="tooltip" data-placement="top" title="{{ $servico->observacoes ?: 'Prioridade Normal' }}">
								Normal
							</span>
						@endif
					</td>
					<td>{{ $tmp->nomeFantasia }}</td>
					<td>{{$servico->os}}@if($servico->servicoPrincipal) <small class="label bg-red" style="border-radius: 3px;">S</small>@endif</td>
					<td><a href="{{route('servicos.show', $servico->id)}}" style="font-weight: 600; color: #3c8dbc;">{{$servico->nome}}</a></td>
					<td>{{ \Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</td>

					@php
						if($servico->unidade_id){
							$empresa = $servico->unidade->nomeFantasia;
							$route = route('unidades.show',$servico->unidade->id);
						}
						elseif($servico->empresa_id){
							$empresa = $servico->empresa->nomeFantasia;
							$route = route('empresas.show',$servico->empresa->id);
						}
					@endphp
					<td><code>{{$servico->unidade->codigo ?? ''}}</code></td>
					<td><a href="{{$route}}" style="color: #555;">{{$empresa}}</a></td>
					<td>{{$servico->unidade->cidade}}/{{$servico->unidade->uf}}</td>
					<td>
						@if(!is_numeric($servico->solicitante))
							{{$servico->solicitante}}
						@else
							{{\App\Models\Solicitante::where('id',$servico->solicitante)->value('nome')}}
						@endif
					</td>
					<td>{{$servico->protocolo_numero}}</td>
					<td>
						@switch($servico->situacao)
							@case('andamento')
								@if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))
									<span class="label label-success" style="border-radius: 4px; padding: 3px 8px;">Andamento</span>
								@elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'licencaOperacao'))
									<span class="label label-danger" style="border-radius: 4px; padding: 3px 8px;">Andamento</span>
								@elseif($servico->tipo == 'nRenovaveis')
									<span class="label label-warning" style="border-radius: 4px; padding: 3px 8px;">Andamento</span>
								@endif
								@break

							@case('finalizado')
								@if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))
									<span class="label label-success" style="border-radius: 4px; padding: 3px 8px;">Finalizado</span>
								@elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'licencaOperacao'))
									<span class="label label-danger" style="border-radius: 4px; padding: 3px 8px;">Finalizado</span>
								@elseif($servico->tipo == 'nRenovaveis')
									<span class="label label-warning" style="border-radius: 4px; padding: 3px 8px;">Finalizado</span>
								@endif
								@break

							@case('arquivado')
								<span class="label label-default" style="border-radius: 4px; padding: 3px 8px; background-color: #777;">Arquivado</span>
								@break

							@case('standBy')
								<span class="label label-default" style="border-radius: 4px; padding: 3px 8px; background-color: #a0a0a0;">Stand By</span>
								@break

							@case('nRenovado')
								<span class="label label-default" style="border-radius: 4px; padding: 3px 8px; background-color: #666;">Não renovado</span>
								@break

							@case('cancelado')
								<span class="label label-danger" style="border-radius: 4px; padding: 3px 8px;">Cancelado</span>
								@break
						@endswitch

						@if (\Request::is('admin/servico/vencer'))  
							<a href="{{route('servico.renovar',$servico->id)}}" class="btn btn-xs btn-primary" style="margin-top: 5px; display: block; border-radius: 4px;">Renovar</a>
							<a href="{{route('servico.desconsiderar',$servico->id)}}" class="btn btn-xs btn-info" style="margin-top: 5px; display: block; border-radius: 4px;">Desconsiderar</a>
						@endif
					</td>
					<td class="text-center" style="white-space: nowrap;">
						<a href="{{route('servicos.show', $servico->id)}}" class="btn btn-default btn-action" title="Detalhes">
							<i class="fa fa-info-circle text-info"></i>
						</a>
						<a href="{{route('servico.delete', $servico->id)}}" class="btn btn-default btn-action confirmation" title="Excluir">
							<i class="fa fa-trash text-danger"></i>
						</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>   
</div>
@stop

@section('js')
<script>
	$(function () {
		$('[data-toggle="tooltip"]').tooltip();
		$('#lista-servicos').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": true,
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
			}           
		});

		$('.confirmation').on('click', function () {
			return confirm('Você deseja excluir o serviço?');
		});
	});
</script>
@stop