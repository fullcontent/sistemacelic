@extends('adminlte::page')

@section('title', 'Listagem de Propostas')

@section('css')
<style>
	.dashboard-card {
		border-radius: 8px;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
		padding: 20px;
		margin-bottom: 25px;
		transition: all 0.3s ease;
		border-left: 4px solid #354256;
		background-color: #fff;
		height: 100%;
		display: flex;
		flex-direction: column;
		justify-content: center;
	}

	.dashboard-card:hover {
		transform: translateY(-5px);
		box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
		text-decoration: none;
	}

	.card-label {
		color: #7f8c8d;
		font-size: 0.8em;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		margin-bottom: 5px;
	}

	.card-value {
		font-size: 2.2em;
		font-weight: 700;
		color: #2c3e50;
		line-height: 1;
	}

	.card-sub {
		color: #95a5a6;
		font-size: 0.85em;
		margin-top: 5px;
	}

	.active-filter {
		border: 2px solid #3c8dbc;
		background-color: #f8fbff;
	}

	.filter-box {
		background: #fff;
		padding: 20px;
		border-radius: 8px;
		margin-bottom: 25px;
		border: 1px solid #ebf0f5;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
	}

	.btn-action {
		width: 32px;
		height: 32px;
		line-height: 32px;
		padding: 0;
		text-align: center;
		border-radius: 6px;
		margin: 0 2px;
		display: inline-block;
		transition: all 0.2s;
	}

	.btn-action:hover {
		transform: scale(1.1);
	}

	.status-badge {
		padding: 4px 10px;
		border-radius: 50px;
		font-size: 0.85em;
		font-weight: 600;
	}

	@media (min-width: 992px) {
		.col-md-5th {
			width: 20%;
			float: left;
			padding: 0 10px;
		}
	}

	.table-container {
		background: #fff;
		border-radius: 8px;
		padding: 15px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
	}
</style>
@stop

@section('content_header')
<div class="row">
	<div class="col-sm-6">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Listagem de Propostas</h1>
	</div>
	<div class="col-sm-6 text-right">
		<a class="btn btn-primary" href="{{route('proposta.create')}}"
			style="border-radius: 50px; padding: 8px 25px; font-weight: 600;">
			<i class="fa fa-plus"></i> Nova Proposta
		</a>
	</div>
</div>
@stop

@section('content')

	<!-- Dashboard Section -->
	<div class="row" style="margin: 0 -10px;">
		<div class="col-md-5th col-sm-6 mb-4">
			<a href="{{ route('proposta.index', ['status' => 'Revisando', 'periodo' => $stats['periodo_atual']]) }}"
				class="dashboard-card {{ $stats['status_atual'] == 'Revisando' ? 'active-filter' : '' }}"
				style="border-left-color: #3c8dbc;">
				<span class="card-label">Em Elaboração</span>
				<span class="card-value">{{ $stats['elaboracao_count'] }}</span>
			</a>
		</div>
		<div class="col-md-5th col-sm-6 mb-4">
			<a href="{{ route('proposta.index', ['status' => 'Em análise', 'periodo' => $stats['periodo_atual']]) }}"
				class="dashboard-card {{ $stats['status_atual'] == 'Em análise' ? 'active-filter' : '' }}"
				style="border-left-color: #00c0ef;">
				<span class="card-label">Em Análise</span>
				<span class="card-value">{{ $stats['analise_count'] }}</span>
			</a>
		</div>
		<div class="col-md-5th col-sm-6 mb-4">
			<a href="{{ route('proposta.index', ['status' => 'Aprovada', 'periodo' => $stats['periodo_atual']]) }}"
				class="dashboard-card {{ $stats['status_atual'] == 'Aprovada' ? 'active-filter' : '' }}"
				style="border-left-color: #00a65a;">
				<span class="card-label">Aprovadas (Mês)</span>
				<span class="card-value">{{ $stats['aprovadas_mes_count'] }}</span>
			</a>
		</div>
		<div class="col-md-5th col-sm-6 mb-4">
			<a href="{{ route('proposta.index', ['status' => 'Recusada', 'periodo' => $stats['periodo_atual']]) }}"
				class="dashboard-card {{ $stats['status_atual'] == 'Recusada' ? 'active-filter' : '' }}"
				style="border-left-color: #dd4b39;">
				<span class="card-label">Recusadas</span>
				<span class="card-value">{{ $stats['recusadas_count'] }}</span>
			</a>
		</div>
		<div class="col-md-5th col-sm-6 mb-4">
			<div class="dashboard-card" style="border-left-color: #354256;">
				<span class="card-label">Taxa de Conversão</span>
				<span class="card-value">{{ number_format($stats['conversao'], 1) }}%</span>
			</div>
		</div>
	</div>

	<!-- Filter Box -->
	<div class="filter-box">
		<form action="{{ route('proposta.index') }}" method="GET" class="row">
			@if($stats['status_atual'])
				<input type="hidden" name="status" value="{{ $stats['status_atual'] }}">
			@endif
			<div class="col-md-4">
				<label style="color: #7f8c8d; font-size: 0.9em;">Período de Referência</label>
				<select name="periodo" class="form-control" onchange="this.form.submit()" style="border-radius: 6px;">
					<option value="todos" {{ $stats['periodo_atual'] == 'todos' ? 'selected' : '' }}>Todos os Registros
					</option>
					<option value="mes_vigente" {{ $stats['periodo_atual'] == 'mes_vigente' ? 'selected' : '' }}>Mês Atual
					</option>
					<option value="mes_anterior" {{ $stats['periodo_atual'] == 'mes_anterior' ? 'selected' : '' }}>Mês
						Anterior</option>
					<option value="ano_atual" {{ $stats['periodo_atual'] == 'ano_atual' ? 'selected' : '' }}>Ano Atual
					</option>
				</select>
			</div>
			<div class="col-md-8 text-right" style="padding-top: 25px;">
				@if($stats['status_atual'] || $stats['periodo_atual'] != 'todos')
					<a href="{{ route('proposta.index') }}" class="btn btn-default" style="border-radius: 50px;">
						<i class="fa fa-eraser"></i> Limpar Filtros
					</a>
				@endif
			</div>
		</form>
	</div>

	<div class="table-container">
		<table id="lista-propostas" class="table table-hover" style="width:100%">
			<thead>
				<tr style="background: #fcfcfc;">
					<th width="80">ID</th>
					<th>Cliente / Unidade</th>
					<th>Total</th>
					<th width="120">Status</th>
					<th width="120">Faturamento</th>
					<th width="150" class="text-center">Ações</th>
				</tr>
			</thead>
			<tbody>
				@foreach($propostas as $p)
					<tr>
						<td style="vertical-align: middle;">
							<a href="{{route('proposta.edit', $p->id)}}"
								style="font-weight: 700; color: #3c8dbc;">#{{$p->id}}</a>
						</td>
						<td style="vertical-align: middle;">
							<div style="font-weight: 600; color: #333;">{{$p->empresa->nomeFantasia ?? 'N/A'}}</div>
							<small style="color: #7f8c8d;">{{$p->unidade->nomeFantasia ?? ''}}
								({{$p->unidade->codigo ?? ''}})</small>
						</td>
						<td style="vertical-align: middle;">
							<span style="font-weight: 600;">R$ {{number_format($p->servicos->sum('valor'), 2, ',', '.')}}</span>
						</td>
						<td style="vertical-align: middle;">
							@php
								$badgeClass = 'label-default';
								if ($p->status == 'Em análise')
									$badgeClass = 'label-info';
								elseif ($p->status == 'Aprovada')
									$badgeClass = 'label-success';
								elseif ($p->status == 'Recusada')
									$badgeClass = 'label-danger';
							@endphp
							<span class="label {{ $badgeClass }}"
								style="padding: 5px 10px; border-radius: 4px;">{{ $p->status }}</span>
						</td>
						<td style="vertical-align: middle;">
							@if($p->servicosFaturados_count == 0)
								<span class="label label-default" style="font-size: 0.9em;"><i class="fa fa-clock-o"></i>
									Aberto</span>
							@elseif($p->servicosFaturados_count < $p->servicosCriados_count)
								<span class="label label-warning" style="font-weight: 600; font-size: 0.9em;"><i
										class="fa fa-adjust"></i> Parcial</span>
							@elseif($p->servicosFaturados_count >= $p->servicosCriados_count && $p->servicosCriados_count > 0)
								<span class="label label-success" style="font-weight: 600; font-size: 0.9em;"><i
										class="fa fa-check-circle"></i> Faturado</span>
							@else
								<span class="label label-default" style="font-size: 0.9em;">Sem Serviços</span>
							@endif
						</td>
						<td style="vertical-align: middle;" class="text-center">
							<a href="{{route('propostaPDF', $p->id)}}" class="btn btn-default btn-action" title="PDF"
								target="_blank">
								<i class="fa fa-file-pdf text-danger"></i>
							</a>

							@if($p->status == 'Revisando' || $p->status == 'Recusada')
								<a href="{{route('proposta.edit', $p->id)}}" class="btn btn-default btn-action" title="Editar">
									<i class="fa fa-edit text-primary"></i>
								</a>
							@endif

							@if($p->status == 'Recusada')
								<a href="#" data-id="{{$p->id}}" class="btn btn-default btn-action revisar" title="Revisar">
									<i class="fa fa-undo text-info"></i>
								</a>
							@endif

							@if($p->status == 'Revisando')
								<a href="#" data-id="{{$p->id}}" class="btn btn-default btn-action analisar"
									title="Enviar para Análise">
									<i class="fa fa-paper-plane text-info"></i>
								</a>
							@endif

							@if($p->status == 'Em análise')
								<a href="#" class="btn btn-default btn-action aprovar" data-id="{{$p->id}}" title="Aprovar">
									<i class="fa fa-check text-success"></i>
								</a>
								<a href="#" class="btn btn-default btn-action recusar" data-id="{{$p->id}}" title="Recusar">
									<i class="fa fa-thumbs-down text-warning"></i>
								</a>
							@endif

							@if($p->status != "Arquivada")
								<a href="{{route('removerProposta', $p->id)}}" class="btn btn-default btn-action confirmation"
									data-id="{{$p->id}}" title="Excluir">
									<i class="fa fa-trash text-danger"></i>
								</a>
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

		<div class="row" style="margin-top: 20px;">
			<div class="col-sm-12 text-center">
				{{ $propostas->appends(request()->input())->links() }}
			</div>
		</div>
	</div>

@endsection

@section('js')
<script>
	$(function () {
		var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

		$('#lista-propostas').DataTable({
			"paging": false,
			"lengthChange": false,
			"searching": true,
			"ordering": true,
			"info": false,
			"autoWidth": true,
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
			},
			"order": [[0, 'desc']],
		});

		$('.confirmation').on('click', function (e) {
			e.preventDefault();
			var url = $(this).attr('href');
			if (confirm('Você deseja excluir a proposta?')) {
				window.location.href = url;
			};
		});

		$(document).on('click', '.analisar', function (e) {
			e.preventDefault();
			var id = $(this).data('id');
			if (confirm('Enviar esta proposta para análise?')) {
				$.get("/admin/proposta/analisar/" + id, function () {
					location.reload();
				});
			}
		});

		$(document).on('click', '.revisar', function (e) {
			e.preventDefault();
			var id = $(this).data('id');
			$.get("/admin/proposta/revisar/" + id, function () {
				location.reload();
			});
		});

		$(document).on('click', '.aprovar', function (e) {
			e.preventDefault();
			var id = $(this).data('id');
			var s = confirm("Gostaria de criar os serviços automaticamente?") ? 1 : 0;
			$.get("/admin/proposta/aprovar/" + id + "/" + s, function () {
				location.reload();
			});
		});

		$(document).on('click', '.recusar', function (e) {
			e.preventDefault();
			var id = $(this).data('id');
			if (confirm('Tem certeza que deseja recusar esta proposta?')) {
				$.get("/admin/proposta/recusar/" + id, function () {
					location.reload();
				});
			}
		});
	});
</script>
@stop