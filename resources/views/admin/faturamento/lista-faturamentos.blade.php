@extends('adminlte::page')

@section('title', 'Painel Gerencial de Faturamento')

@section('content_header')
<h1>Painel Gerencial de Faturamento</h1>
@stop

@section('css')
<style>
	.dashboard-card {
		border-radius: 8px;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
		padding: 20px;
		margin-bottom: 20px;
		transition: transform 0.2s;
		border-left: 5px solid #354256;
		background-color: #fff;
	}

	.dashboard-card:hover {
		transform: translateY(-5px);
	}

	.card-label {
		color: #6a84aa;
		font-size: 0.9em;
		font-weight: bold;
		text-transform: uppercase;
	}

	.card-value {
		font-size: 1.8em;
		font-weight: bold;
		color: #354256;
		margin: 5px 0;
	}

	.card-sub {
		color: #7aa2c9;
		font-size: 0.85em;
	}

	.bg-castro-gray {
		background-color: #eaeaec;
	}

	.text-castro-dark {
		color: #354256;
	}

	.progress-bar-castro {
		background-color: #7aa2c9;
	}

	.filter-box {
		background: #fff;
		padding: 15px;
		border-radius: 8px;
		margin-bottom: 20px;
		border: 1px solid #eaeaec;
	}
</style>
@stop

@section('content')

	<!-- Seção de Indicadores (Cards) -->
	<div class="row">
		<!-- Total no Ano -->
		<div class="col-md-3">
			<div class="dashboard-card">
				<div class="card-label">Total Faturado no Ano ({{ $stats['anoReferencia'] }})</div>
				<div class="card-value">R$ {{ number_format($stats['totalAno'], 2, ',', '.') }}</div>

				<div class="card-variation">
					@if($stats['percentualTotalAno'] > 0)
						<span style="color: #27ae60; font-size: 0.8em;">
							<i class="fa fa-arrow-up"></i> {{ number_format($stats['percentualTotalAno'], 1) }}%
						</span>
					@elseif($stats['percentualTotalAno'] < 0)
						<span style="color: #c0392b; font-size: 0.8em;">
							<i class="fa fa-arrow-down"></i> {{ number_format(abs($stats['percentualTotalAno']), 1) }}%
						</span>
					@else
						<span style="color: #7f8c8d; font-size: 0.8em;">
							<i class="fa fa-minus"></i> 0%
						</span>
					@endif
					<small style="color: #95a5a6; font-size: 0.7em;">vs. ano {{ $stats['anoReferencia'] - 1 }}</small>
				</div>

				<div class="card-sub" style="margin-top: 8px;">Acumulado do ano de referência</div>
			</div>
		</div>

		<!-- Total no Período -->
		<div class="col-md-3">
			<div class="dashboard-card" style="border-left-color: #7aa2c9;">
				<div class="card-label">Total Faturado no Período ({{ $stats['labelPeriodo'] }})</div>
				<div class="card-value">R$ {{ number_format($stats['totalPeriodo'], 2, ',', '.') }}</div>

				<div class="card-variation">
					@if($stats['percentualFaturamento'] > 0)
						<span style="color: #27ae60; font-size: 0.8em;">
							<i class="fa fa-arrow-up"></i> {{ number_format($stats['percentualFaturamento'], 1) }}%
						</span>
					@elseif($stats['percentualFaturamento'] < 0)
						<span style="color: #c0392b; font-size: 0.8em;">
							<i class="fa fa-arrow-down"></i> {{ number_format(abs($stats['percentualFaturamento']), 1) }}%
						</span>
					@else
						<span style="color: #7f8c8d; font-size: 0.8em;">
							<i class="fa fa-minus"></i> 0%
						</span>
					@endif
					<small style="color: #95a5a6; font-size: 0.7em;">vs. período anterior</small>
				</div>

				<div class="card-value" style="font-size: 1.4em; border-top: 1px solid #eee; padding-top: 5px;">
					R$ {{ number_format($stats['totalAno'], 2, ',', '.') }}
					<small style="font-size: 0.5em; color: #6a84aa;">ano {{ $stats['anoReferencia'] }}</small>
				</div>
			</div>
		</div>

		<!-- Top Clientes -->
		<div class="col-md-3">
			<div class="dashboard-card" style="border-left-color: #354256;">
				<div class="card-label">Cliente Top (Período/Ano)</div>
				<div class="card-value" style="font-size: 1.2em; height: 2.2em; display: flex; align-items: center;">
					{{ $stats['clienteTopPeriodo']->empresa->nomeFantasia ?? 'N/A' }}
				</div>
				<div class="card-sub">Top Ano: {{ $stats['clienteTopAno']->empresa->nomeFantasia ?? 'N/A' }}</div>
			</div>
		</div>

		<!-- Total Notas -->
		<div class="col-md-3">
			<div class="dashboard-card">
				<div class="card-label">Notas Emitidas</div>
				<div class="card-value">{{ $stats['notasPeriodo'] }} <small
						style="font-size: 0.5em; color: #6a84aa;">período</small></div>

				<div class="card-variation">
					@if($stats['percentualNotas'] > 0)
						<span style="color: #27ae60; font-size: 0.8em;">
							<i class="fa fa-arrow-up"></i> {{ number_format($stats['percentualNotas'], 1) }}%
						</span>
					@elseif($stats['percentualNotas'] < 0)
						<span style="color: #c0392b; font-size: 0.8em;">
							<i class="fa fa-arrow-down"></i> {{ number_format(abs($stats['percentualNotas']), 1) }}%
						</span>
					@else
						<span style="color: #7f8c8d; font-size: 0.8em;">
							<i class="fa fa-minus"></i> 0%
						</span>
					@endif
					<small style="color: #95a5a6; font-size: 0.7em;">vs. período anterior</small>
				</div>

				<div class="card-value" style="font-size: 1.4em; border-top: 1px solid #eee; padding-top: 5px;">
					{{ $stats['notasAno'] }} <small style="font-size: 0.5em; color: #6a84aa;">ano</small>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<!-- Ranking Gráfico -->
		<div class="col-md-8">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Ranking de Clientes (Top 5 no {{ $stats['labelPeriodo'] }})</h3>
				</div>
				<div class="box-body">
					<canvas id="rankingChart" height="100"></canvas>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="dashboard-card"
				style="height: 185px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
				<a class="btn btn-app btn-lg" href="{{route('faturamento.create')}}"
					style="width: 100%; height: 140px; display: flex; flex-direction: column; justify-content: center;">
					<i class="fa fa-plus" style="font-size: 3em; color: #354256;"></i>
					<span style="font-size: 1.3em; font-weight: bold;">Novo Faturamento</span>
				</a>
			</div>
		</div>
	</div>

	<!-- Filtros -->
	<div class="filter-box">
		<form action="{{ route('faturamentos.index') }}" method="GET" class="row">
			<div class="col-md-3">
				<label>Período</label>
				<select name="periodo" class="form-control" onchange="this.form.submit()">
					<option value="mes_vigente" {{ $stats['periodo'] == 'mes_vigente' ? 'selected' : '' }}>Mês Vigente
					</option>
					<option value="mes_anterior" {{ $stats['periodo'] == 'mes_anterior' ? 'selected' : '' }}>Mês Anterior
					</option>
					<option value="trimestre" {{ $stats['periodo'] == 'trimestre' ? 'selected' : '' }}>Último Trimestre
					</option>
					<option value="ano_atual" {{ $stats['periodo'] == 'ano_atual' ? 'selected' : '' }}>Ano Atual</option>
					<option value="ano_passado" {{ $stats['periodo'] == 'ano_passado' ? 'selected' : '' }}>Ano Passado
					</option>
				</select>
			</div>
			<!-- Outros filtros podem ser adicionados aqui -->
			<div class="col-md-9 text-right" style="padding-top: 25px;">
				<a href="{{ route('relatorioFaturamentosCSV', ['periodo' => $stats['periodo']]) }}" class="btn btn-success"
					target="_blank">
					<i class="fa fa-file-excel-o"></i> Exportar Relatório (CSV)
				</a>
			</div>
		</form>
	</div>

	<div class="box" style="padding: 10px;">
		<table id="lista-faturamentos" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th># Faturamento</th>
					<th>Cliente</th>
					<th>Data</th>
					<th>Total</th>
					<th>Status NFS-e</th>
					<th>Pagamento</th>
					<th>Ações</th>
				</tr>
			</thead>
			<tbody>
				@foreach($faturamentos as $f)
					<tr>
						<td><a href="{{route('faturamento.show', $f->id)}}">{{$f->nome}}</a></td>
						<td>{{$f->empresa->nomeFantasia}}</td>
						<td><span
								style="display:none;">{{$f->created_at}}</span>{{ \Carbon\Carbon::parse($f->created_at)->format('d/m/Y')}}
						</td>
						<td>R$ {{number_format($f->valorTotal, 2, ',', '.')}}</td>
						<td>
							@if($f->nf || $f->servicos->whereNotNull('nf')->count() > 0)
								<span class="label label-success">Emitida</span>
							@else
								<a href="#" class="label label-warning cadastroNF" data-id="{{ $f->id }}"
									data-cliente="{{ $f->empresa->nomeFantasia }}" data-nome="{{ $f->nome }}">
									Não Emitida
								</a>
							@endif
						</td>
						<td>
							@php
								$statusFaturamento = 'PAGO';
								$corStatus = 'success';
								$hasParcial = false;
								$hasAberto = false;

								foreach ($f->servicos as $s) {
									if (isset($s->financeiro)) {
										if ($s->financeiro->status == 'parcial') {
											$hasParcial = true;
										}
										if ($s->financeiro->status == 'aberto') {
											$hasAberto = true;
										}
									}
								}

								if ($hasParcial) {
									$statusFaturamento = 'PARCIAL';
									$corStatus = 'warning';
								} elseif ($hasAberto) {
									$statusFaturamento = 'EM ABERTO';
									$corStatus = 'danger';
								}
							@endphp

							<span class="label label-{{ $corStatus }}">{{ $statusFaturamento }}</span>
						</td>
						<td>
							<div class="btn-group">
								<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"
									aria-expanded="false">
									Ações <span class="caret"></span>
								</button>
								<ul class="dropdown-menu pull-right" role="menu">
									<li>
										<a href="{{route('faturamento.show', $f->id)}}">
											<i class="fa fa-eye"></i> Ver Detalhes
										</a>
									</li>
									<li>
										<a href="{{route('faturamento.pdf', $f->id)}}" target="_blank">
											<i class="fa fa-file"></i> Baixar PDF
										</a>
									</li>
									<li>
										<a href="#" data-toggle="modal" data-target="#myModal"
											data-faturamento_id="{{ $f->id }}" data-dados_id="{{ $f->dadosCastro_id}}">
											<i class="fa fa-building"></i> Alterar CNPJ
										</a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="{{route('faturamento.destroy', $f->id)}}" class="confirmation">
											<i class="fa fa-trash"></i> Excluir
										</a>
									</li>
								</ul>
							</div>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@include('admin.faturamento.modals')

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	$(function () {
		$('#lista-faturamentos').DataTable({
			"paging": true,
			"lengthChange": true,
			"pageLength": 25,
			"searching": true,
			"ordering": true,
			"order": [[2, "desc"]],
			"info": true,
			"autoWidth": true,
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
			}
		});

		$('.confirmation').on('click', function () {
			return confirm('Você deseja excluir o faturamento?');
		});

		// Gráfico de Ranking
		var ctx = document.getElementById('rankingChart').getContext('2d');
		var rankingChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: {!! json_encode($stats['topClientesPeriodo']->map(function ($c) {
	return $c->empresa->nomeFantasia; })) !!},
				datasets: [{
					label: 'Valor Faturado (R$)',
					data: {!! json_encode($stats['topClientesPeriodo']->map(function ($c) {
	return $c->total; })) !!},
					backgroundColor: '#7aa2c9',
					borderColor: '#354256',
					borderWidth: 1
				}]
			},
			options: {
				indexAxis: 'y',
				responsive: true,
				plugins: {
					legend: { display: false }
				},
				scales: {
					x: { beginAtZero: true }
				}
			}
		});
	});

	$(document).on("click", ".cadastroNF", function (e) {
		e.preventDefault();
		var faturamentoID = $(this).data('id');
		var faturamentoCliente = $(this).data('cliente');
		var faturamentoNome = $(this).data('nome');

		$(".modal-body #faturamentoID").val(faturamentoID);
		$(".modal-body #faturamentoCliente").val(faturamentoCliente);
		$(".modal-body #faturamentoNome").val(faturamentoNome);
		$(".modal-body #faturamentoNF").val(null);

		$('#cadastroNF').modal('show');
	});
</script>

<script>
	$(document).ready(function () {
		$('#myModal').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget);
			var faturamento_id = button.data('faturamento_id');
			var dados_id = button.data('dados_id')
			var modal = $(this);

			$.get('/api/getDadosCastro', function (data) {
				var select = modal.find('#company-select-form select');
				select.empty();
				for (var i = 0; i < data.length; i++) {
					var option = $('<option></option>');
					option.attr('value', data[i].id);
					option.text(data[i].razaoSocial);
					if (data[i].id == dados_id) {
						option.attr('selected', 'selected');
					}
					select.append(option);
				}
			});

			var hiddenInput = $("<input>").attr({
				type: "hidden",
				name: "faturamento_id",
				value: faturamento_id
			});
			$("#company-select-form").find('input[name="faturamento_id"]').remove();
			$("#company-select-form").append(hiddenInput);
		});

		$('.modal-footer .save-selected-item').click(function () {
			var form = $('#company-select-form');
			var data = form.serialize();
			var url = '/api/saveDadosCastro/';
			$.get(url, data, function (response) {
				location.reload();
			});
		});
	});
</script>
@stop