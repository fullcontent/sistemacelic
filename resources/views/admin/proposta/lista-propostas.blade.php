@extends('adminlte::page')

@section('title', 'Listagem de Propostas')

@section('css')
<style>
	.dashboard-card {
		border-radius: 8px;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
		padding: 15px; /* Reduced from 20px for better fit in 6 columns */
		margin-bottom: 25px;
		transition: all 0.3s ease;
		border-left: 4px solid #354256;
		background-color: #fff;
		height: 100%;
		display: flex;
		flex-direction: column;
		justify-content: center;
		box-sizing: border-box; /* Explicitly ensure borders don't break width */
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

	@media (min-width: 1200px) {
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

	.time-indicator {
		display: flex;
		justify-content: flex-end;
		gap: 8px;
		border-left: 1px solid #f4f4f4;
		padding-left: 10px;
		margin-left: 10px;
	}

	.time-item {
		text-align: center;
		padding: 0 2px;
		flex: 1;
	}

	.time-count {
		font-weight: 700;
		display: block;
		font-size: 1.1em;
	}

	.time-label {
		font-size: 0.7em;
		color: #7f8c8d;
		text-transform: uppercase;
	}

	.text-green { color: #00a65a; }
	.text-yellow { color: #f39c12; }
	.text-red { color: #dd4b39; }

	.analysis-summary {
		display: flex;
		align-items: center;
		justify-content: space-between;
		width: 100%;
	}

	.trend-indicator {
		font-size: 0.75em;
		font-weight: 600;
		margin-top: 4px;
		display: block;
	}

	.progress-meta {
		height: 8px;
		border-radius: 10px;
		background-color: #eee;
		margin-top: 10px;
		overflow: hidden;
	}

	.mb-4 { margin-bottom: 20px !important; }
	.overflow-hidden { overflow: hidden; }

	.progress-bar-meta {
		height: 100%;
		border-radius: 10px;
		transition: width 0.6s ease;
	}
</style>
@stop

@section('content_header')
<div class="row">
	<div class="col-sm-6">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Listagem de Propostas</h1>
	</div>
</div>
<div class="row">
	<div class="pull-right">
		<a class="btn btn-primary" href="{{route('proposta.create')}}"
			style="border-radius: 50px; padding: 8px 25px; font-weight: 600;">
			<i class="fa fa-plus"></i> Nova Proposta
		</a>
	</div>
</div>

	<!-- Modal para Selecionar Vendedor -->
	<div class="modal fade" id="modalVendedor" tabindex="-1" role="dialog" aria-labelledby="modalVendedorLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content" style="border-radius: 8px;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="modalVendedorLabel">Atribuir Vendedor</h4>
				</div>
				<form id="formVendedor">
					@csrf
					<div class="modal-body">
						<input type="hidden" id="modal_proposta_id" name="proposta_id">
						<div class="form-group">
							<label for="vendedor_id">Selecione o Usuário</label>
							<select name="vendedor_id" id="vendedor_id" class="form-control select2" style="width: 100%;">
								<option value="">Selecione...</option>
								@foreach($users as $id => $name)
									<option value="{{ $id }}">{{ $name }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 50px;">Cancelar</button>
						<button type="submit" class="btn btn-primary" style="border-radius: 50px;">Salvar Alteração</button>
					</div>
				</form>
			</div>
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
			<div class="dashboard-card {{ $stats['status_atual'] == 'Em análise' ? 'active-filter' : '' }}"
				style="border-left-color: #00c0ef;">
				<div class="analysis-summary">
					<a href="{{ route('proposta.index', ['status' => 'Em análise', 'periodo' => $stats['periodo_atual']]) }}" style="text-decoration: none; color: inherit; min-width: 50px;">
						<span class="card-label" style="margin-bottom: 2px;">Em Análise</span>
						<span class="card-value" style="font-size: 1.8em;">{{ $stats['analise_count'] }}</span>
					</a>
					<div class="time-indicator">
						<div class="time-item">
							<span class="time-count text-green" style="font-size: 1em;">{{ $stats['analise_0_7'] }}</span>
							<span class="time-label" style="font-size: 0.65em;">0-7d</span>
						</div>
						<div class="time-item">
							<span class="time-count text-yellow" style="font-size: 1em;">{{ $stats['analise_8_15'] }}</span>
							<span class="time-label" style="font-size: 0.65em;">8-15d</span>
						</div>
						<div class="time-item">
							<span class="time-count text-red" style="font-size: 1em;">{{ $stats['analise_15_plus'] }}</span>
							<span class="time-label" style="font-size: 0.65em;">+15d</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-5th col-sm-6 mb-4">
			<a href="{{ route('proposta.index', ['status' => 'Aprovada', 'periodo' => $stats['periodo_atual']]) }}"
				class="dashboard-card {{ $stats['status_atual'] == 'Aprovada' ? 'active-filter' : '' }}"
				style="border-left-color: #00a65a;">
				<span class="card-label">Aprovadas</span>
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
			<a href="{{ route('proposta.index', ['status' => 'Arquivada', 'periodo' => $stats['periodo_atual']]) }}"
				class="dashboard-card {{ $stats['status_atual'] == 'Arquivada' ? 'active-filter' : '' }}"
				style="border-left-color: #7f8c8d;">
				<span class="card-label">Arquivadas</span>
				<span class="card-value">{{ \App\Models\Proposta::where('status', 'Arquivada')->count() }}</span>
			</a>
		</div>
	</div>

	<!-- Stats Row 2 (Half Width Cards) -->
	<div class="row" style="margin: 0 -10px;">
		<div class="col-md-6 col-sm-6 mb-4">
			<div class="dashboard-card" style="border-left-color: #f39c12; min-height: 100px;">
				<div style="display: flex; justify-content: space-between; align-items: flex-start;">
					<div>
						<span class="card-label">Meta do Mês (R$ 120k)</span>
						<span class="card-value">{{ number_format($stats['meta_percentual'], 1) }}%</span>
					</div>
					<div style="text-align: right;">
						<span class="label label-warning" style="font-size: 0.85em; background-color: #f39c12 !important;">{{ $stats['periodo_label'] }}</span>
					</div>
				</div>
				<div class="progress-meta">
					<div class="progress-bar-meta {{ $stats['meta_percentual'] >= 100 ? 'bg-green' : ($stats['meta_percentual'] >= 50 ? 'bg-yellow' : 'bg-aqua') }}" 
						 role="progressbar" style="width: {{ min($stats['meta_percentual'], 100) }}%"></div>
				</div>
				<small style="color: #7f8c8d; font-size: 0.75em; margin-top: 5px; display: block;">
					Acumulado: R$ {{ number_format($stats['valor_aprovado'], 2, ',', '.') }}
				</small>
			</div>
		</div>
		<div class="col-md-6 col-sm-6 mb-4">
			<div class="dashboard-card" style="border-left-color: #354256; min-height: 100px;">
				<span class="card-label">Taxa de Conversão</span>
				<span class="card-value">{{ number_format($stats['conversao'], 1) }}%</span>
				@if($stats['conversao_diff'] != 0)
					<span class="trend-indicator {{ $stats['conversao_diff'] > 0 ? 'text-green' : 'text-red' }}">
						<i class="fa {{ $stats['conversao_diff'] > 0 ? 'fa-caret-up' : 'fa-caret-down' }}"></i>
						{{ number_format(abs($stats['conversao_diff']), 1) }}% vs mês ant.
					</span>
				@endif
			</div>
		</div>
	</div>


	<div class="filter-box">
		<form action="{{ route('proposta.index') }}" method="GET" class="row">
			<div class="col-md-2">
				<label style="color: #7f8c8d; font-size: 0.9em;">Período</label>
				<select name="periodo" class="form-control select2-filter" onchange="this.form.submit()" style="border-radius: 6px;">
					<option value="todos" {{ $stats['periodo_atual'] == 'todos' ? 'selected' : '' }}>Todos os Registros</option>
					@foreach($meses_filtro as $key => $label)
						<option value="{{ $key }}" {{ $stats['periodo_atual'] == $key ? 'selected' : '' }}>{{ $label }}</option>
					@endforeach
					<option value="ano_atual" {{ $stats['periodo_atual'] == 'ano_atual' ? 'selected' : '' }}>Ano Atual</option>
				</select>
			</div>
			<div class="col-md-2">
				<label style="color: #7f8c8d; font-size: 0.9em;">Status</label>
				<select name="status" class="form-control select2-filter" onchange="this.form.submit()" style="border-radius: 6px;">
					<option value="">Todos</option>
					@foreach($status_list as $st)
						<option value="{{ $st }}" {{ $stats['status_atual'] == $st ? 'selected' : '' }}>{{ $st }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-3">
				<label style="color: #7f8c8d; font-size: 0.9em;">Vendedor</label>
				<select name="vendedor" class="form-control select2-filter" onchange="this.form.submit()" style="border-radius: 6px;">
					<option value="">Todos</option>
					@foreach($vendedores as $id => $name)
						<option value="{{ $id }}" {{ $stats['vendedor_atual'] == $id ? 'selected' : '' }}>{{ $name }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-3">
				<label style="color: #7f8c8d; font-size: 0.9em;">Solicitante</label>
				<select name="solicitante" class="form-control select2-filter" onchange="this.form.submit()" style="border-radius: 6px;">
					<option value="">Todos</option>
					@foreach($solicitantes as $id => $name)
						<option value="{{ $id }}" {{ $stats['solicitante_atual'] == $id ? 'selected' : '' }}>{{ $name }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-2 text-right" style="padding-top: 25px;">
				@if($stats['status_atual'] || $stats['periodo_atual'] != 'todos' || $stats['vendedor_atual'] || $stats['solicitante_atual'])
					<a href="{{ route('proposta.index') }}" class="btn btn-default" style="border-radius: 50px;">
						<i class="fa fa-eraser"></i> Limpar
					</a>
				@endif
			</div>
		</form>
	</div>

	<div class="table-container">
		<table id="lista-propostas" class="table table-hover" style="width:100%">
			<thead>
				<tr style="background: #fcfcfc;">
					<th width="60">ID</th>
					<th>Vendedor</th>
					<th>Cliente / Unidade</th>
					<th>Solicitante</th>
					<th>Total</th>
					<th width="120">Status</th>
					<th width="100">Dias em Análise</th>
					<th width="120">Faturamento</th>
					<th width="180" class="text-center">Ações</th>
				</tr>
			</thead>
			<tbody>
				{{-- DataTables Server-Side will populate this --}}
			</tbody>
		</table>
	</div>

@endsection

@section('js')
<script>
	$(function () {
		var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

		// Initialize Select2 for filters
		$('.select2-filter').select2({
			width: '100%'
		});

		// Initialize Select2 for modal
		$('#vendedor_id').select2({
			dropdownParent: $('#modalVendedor'),
			width: '100%'
		});

		var table = $('#lista-propostas').DataTable({
			"processing": true,
			"serverSide": true,
			"pageLength": 25,
			"ajax": {
				"url": "{{ route('proposta.listData') }}",
				"data": function (d) {
					d.periodo = "{{ $stats['periodo_atual'] }}";
					d.status = "{{ $stats['status_atual'] }}";
					d.vendedor = "{{ $stats['vendedor_atual'] }}";
					d.solicitante = "{{ $stats['solicitante_atual'] }}";
				}
			},
			"columns": [
				{
					"data": "id",
					"render": function (data, type, row) {
						return '<a href="' + row.edit_url + '" style="font-weight: 700; color: #3c8dbc;">#' + data + '</a>';
					}
				},
				{
					"data": "vendedor_nome",
					"render": function (data, type, row) {
						if (!data) {
							return '<button class="btn btn-xs btn-default btn-assign-vendedor" data-proposta-id="' + row.id + '" title="Atribuir Vendedor" style="border-radius: 50px; padding: 2px 8px;">' +
								'<i class="fa fa-plus text-primary"></i> Atribuir' +
								'</button>';
						}
						return '<span style="font-size: 0.9em; color: #555;">' + data + '</span>';
					}
				},
				{
					"data": "empresa_nome",
					"render": function (data, type, row) {
						return '<div style="font-weight: 600; color: #333;">' + data + '</div>' +
							'<small style="color: #7f8c8d;">' + row.unidade_nome + ' (' + row.unidade_codigo + ')</small>';
					}
				},
				{
					"data": "solicitante_nome",
					"render": function (data) {
						return '<span style="font-size: 0.9em; color: #555;">' + data + '</span>';
					}
				},
				{
					"data": "valor_total",
					"render": function (data, type, row) {
						return '<span style="font-weight: 600;">R$ ' + data + '</span>';
					}
				},
				{
					"data": "status",
					"render": function (data, type, row) {
						var badgeClass = 'label-default';
						if (data == 'Em análise') badgeClass = 'label-info';
						else if (data == 'Aprovada') badgeClass = 'label-success';
						else if (data == 'Recusada') badgeClass = 'label-danger';
						else if (data == 'Arquivada') badgeClass = 'label-default';

						return '<span class="label ' + badgeClass + '" style="padding: 5px 10px; border-radius: 4px;">' + data + '</span>';
					}
				},
				{
					"data": "dias_analise",
					"className": "text-center",
					"render": function (data, type, row) {
						var html = '';
						if (row.status === 'Em análise') {
							html = '<span class="badge" style="background-color: ' + row.dias_analise_cor + ';">' + data + ' dias</span>';
						} else if (row.status === 'Aprovada') {
							html = '<span style="color: #27ae60; font-size: 0.85em; font-weight: 600;"><i class="fa fa-check-circle"></i> ' + row.approved_at + '</span>';
						} else if (row.status === 'Recusada') {
							html = '<span style="color: #c0392b; font-size: 0.85em; font-weight: 600;"><i class="fa fa-times-circle"></i> ' + row.refused_at + '</span>';
						} else if (row.finalized_at) {
							html = '<span style="color: #7f8c8d; font-size: 0.85em; font-weight: 600;"><i class="fa fa-clock-o"></i> ' + row.finalized_at + '</span>';
						}

						if (row.is_data_aproximada && html !== '') {
							html += '<br><small style="color: #999; font-size: 0.8em; display: block; margin-top: 2px;">Data aproximada</small>';
						}
						
						return html || '-';
					}
				},
				{
					"data": "id",
					"orderable": false,
					"render": function (data, type, row) {
						if (row.servicos_faturados_count == 0) {
							return '<span class="label label-default" style="font-size: 0.9em;"><i class="fa fa-clock-o"></i> Aberto</span>';
						} else if (row.servicos_faturados_count < row.servicos_criados_count) {
							return '<span class="label label-warning" style="font-weight: 600; font-size: 0.9em;"><i class="fa fa-adjust"></i> Parcial</span>';
						} else if (row.servicos_faturados_count >= row.servicos_criados_count && row.servicos_criados_count > 0) {
							return '<span class="label label-success" style="font-weight: 600; font-size: 0.9em;"><i class="fa fa-check-circle"></i> Faturado</span>';
						} else {
							return '<span class="label label-default" style="font-size: 0.9em;">Sem Serviços</span>';
						}
					}
				},
				{
					"data": "id",
					"orderable": false,
					"className": "text-center",
					"render": function (data, type, row) {
						var actions = '<div style="white-space: nowrap;">';
						actions += '<a href="' + row.pdf_url + '" class="btn btn-default btn-action" title="PDF" target="_blank"><i class="fa fa-file-pdf text-danger"></i></a>';

						if (row.can_edit) {
							actions += ' <a href="' + row.edit_url + '" class="btn btn-default btn-action" title="Editar"><i class="fa fa-edit text-primary"></i></a>';
						}

						if (row.is_recusada) {
							actions += ' <a href="#" data-id="' + data + '" class="btn btn-default btn-action revisar" title="Revisar"><i class="fa fa-undo text-info"></i></a>';
						}

						if (row.is_revisando) {
							actions += ' <a href="#" data-id="' + data + '" class="btn btn-default btn-action analisar" title="Enviar para Análise"><i class="fa fa-paper-plane text-info"></i></a>';
						}

						if (row.is_em_analise) {
							actions += ' <a href="#" class="btn btn-default btn-action aprovar" data-id="' + data + '" title="Aprovar"><i class="fa fa-check text-success"></i></a>' +
								' <a href="#" class="btn btn-default btn-action recusar" data-id="' + data + '" title="Recusar"><i class="fa fa-thumbs-down text-warning"></i></a>';
						}

						if (!row.is_arquivada) {
							actions += ' <a href="' + row.remove_url + '" class="btn btn-default btn-action confirmation" data-id="' + data + '" title="Arquivar"><i class="fa fa-archive text-muted"></i></a>';
						}

						actions += '</div>';
						return actions;
					}
				}
			],
			"ordering": true,
			"info": true,
			"lengthChange": false,
			"autoWidth": true,
			"deferRender": true,
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json",
				"search": "Buscar:"
			},
			"order": [[0, 'desc']],
			"drawCallback": function() {
				// Add "Go to page" input if not exists
				if ($('.dataTables_goto').length === 0) {
					var paginate = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
					var gotoHtml = '<div class="dataTables_goto" style="display: inline-block; margin-left: 15px;">' +
								   'Ir para: <input type="number" class="form-control input-sm" style="width: 60px; display: inline-block;" min="1">' +
								   '</div>';
					paginate.append(gotoHtml);
					
					paginate.on('keyup', '.dataTables_goto input', function(e) {
						if (e.keyCode === 13) {
							var page = parseInt($(this).val()) - 1;
							var info = table.page.info();
							if (page >= 0 && page < info.pages) {
								table.page(page).draw('page');
							} else {
								alert('Página inválida (Total: ' + info.pages + ')');
							}
						}
					});
				}
			}
		});

		$(document).on('click', '.confirmation', function (e) {
			e.preventDefault();
			var url = $(this).attr('href');
			if (confirm('Você deseja arquivar a proposta?')) {
				$.get(url, function (response) {
					if (response.success) {
						table.ajax.reload(null, false); // Reload table without resetting pagination
					} else {
						alert('Erro ao arquivar a proposta.');
					}
				});
			};
		});

		// Atribuir Vendedor
		$(document).on('click', '.btn-assign-vendedor', function() {
			var propostaId = $(this).data('proposta-id');
			$('#modal_proposta_id').val(propostaId);
			$('#vendedor_id').val('').trigger('change');
			$('#modalVendedor').modal('show');
		});

		$('#formVendedor').on('submit', function(e) {
			e.preventDefault();
			var formData = $(this).serialize();
			
			$.ajax({
				url: "{{ route('proposta.vendedor.update') }}",
				method: "POST",
				data: formData,
				success: function(response) {
					if (response.success) {
						$('#modalVendedor').modal('hide');
						table.ajax.reload(null, false);
					} else {
						alert(response.message || 'Erro ao atualizar vendedor.');
					}
				},
				error: function() {
					alert('Erro na comunicação com o servidor.');
				}
			});
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