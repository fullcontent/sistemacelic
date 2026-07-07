@extends('adminlte::page')

@section('title', 'Relatórios')

@section('css')
<style>
	.form-container {
		background: #fff;
		border-radius: 8px;
		padding: 20px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
		border: 1px solid #ebf0f5;
		margin-bottom: 25px;
		min-height: 380px;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
	}

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

	.box-title-custom {
		font-weight: 700;
		color: #333;
		font-size: 1.2em;
		margin-top: 0;
		margin-bottom: 15px;
		display: flex;
		align-items: center;
		gap: 10px;
	}

	.btn-pill {
		border-radius: 50px;
		padding: 6px 20px;
		font-weight: 600;
		transition: all 0.2s;
	}

	.btn-pill:hover {
		transform: translateY(-1px);
		box-shadow: 0 2px 5px rgba(0,0,0,0.1);
	}

	.mb-4 { margin-bottom: 20px !important; }
	.content-header .breadcrumb { display: none !important; }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-12">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Relatórios</h1>
	</div>
</div>
@stop

@section('content')
@if (session('success'))
    <div class="alert alert-success" style="border-radius: 6px;">
        {{ session('success') }}
    </div>
@endif

<div class="row">
	<!-- Card 1: Completos -->
	<div class="col-md-4">
		<div class="form-container" style="min-height: 480px;">
			<div>
				<h3 class="box-title-custom"><i class="fa fa-file-csv text-muted"></i> Completos</h3>
				<p class="text-muted" style="font-size: 0.9em; margin-bottom: 15px;">Download em formato .csv</p>

				<a href="{{route('relatorio.completo')}}" class="btn btn-block btn-default btn-pill" style="text-align: left; font-size: 1em; margin-bottom: 10px; padding: 10px 15px;">
					<i class="fa fa-file-invoice text-primary" style="margin-right: 8px; width: 16px;"></i> Serviços
				</a>

				<a href="{{route('relatorio.taxas')}}" target="_blank" class="btn btn-block btn-default btn-pill" style="text-align: left; font-size: 1em; margin-bottom: 10px; padding: 10px 15px;">
					<i class="fa fa-chart-pie text-warning" style="margin-right: 8px; width: 16px;"></i> Taxas
				</a>

				<a href="{{route('relatorio.pendencias')}}" target="_blank" class="btn btn-block btn-default btn-pill" style="text-align: left; font-size: 1em; margin-bottom: 10px; padding: 10px 15px;">
					<i class="fa fa-chart-line text-danger" style="margin-right: 8px; width: 16px;"></i> Pendências
				</a>

				<a href="{{route('relatorio.arquivos')}}" target="_blank" class="btn btn-block btn-default btn-pill" style="text-align: left; font-size: 1em; margin-bottom: 10px; padding: 10px 15px;">
					<i class="fa fa-folder-open text-info" style="margin-right: 8px; width: 16px;"></i> Arquivos
				</a>

				<a href="{{route('relatorioEmpresasCSV')}}" target="_blank" class="btn btn-block btn-default btn-pill" style="text-align: left; font-size: 1em; margin-bottom: 10px; padding: 10px 15px;">
					<i class="fa fa-building text-success" style="margin-right: 8px; width: 16px;"></i> Empresas
				</a>

				<a href="{{route('relatorioPropostasCSV')}}" target="_blank" class="btn btn-block btn-default btn-pill" style="text-align: left; font-size: 1em; margin-bottom: 10px; padding: 10px 15px;">
					<i class="fa fa-file-signature text-info" style="margin-right: 8px; width: 16px;"></i> Propostas
				</a>

				<a href="{{route('relatorioFaturamentosCSV')}}" target="_blank" class="btn btn-block btn-default btn-pill" style="text-align: left; font-size: 1em; margin-bottom: 10px; padding: 10px 15px;">
					<i class="fa fa-dollar-sign text-success" style="margin-right: 8px; width: 16px;"></i> Faturamentos
				</a>

				<a href="{{route('relatorioReembolsosCSV')}}" target="_blank" class="btn btn-block btn-default btn-pill" style="text-align: left; font-size: 1em; margin-bottom: 10px; padding: 10px 15px;">
					<i class="fa fa-money-bill-wave text-warning" style="margin-right: 8px; width: 16px;"></i> Reembolsos
				</a>
			</div>
		</div>
	</div>

	<!-- Card 2: Pendências -->
	<div class="col-md-4">
		<div class="form-container" style="min-height: 480px;">
			{!! Form::open(['route'=>'relatorioPendenciasFilter','method'=>"post", 'style'=>'display:flex; flex-direction:column; justify-content:space-between; height:100%; width:100%;']) !!}
			<div>
				<h3 class="box-title-custom"><i class="fa fa-exclamation-triangle text-muted"></i> Pendências</h3>
				<p class="text-muted" style="font-size: 0.9em; margin-bottom: 15px;">Download em formato .csv</p>
				
				<div class="form-group">
					{!! Form::label('empresa_id', 'Empresas:', array('class'=>'control-label', 'style'=>'color: #7f8c8d; font-size: 0.9em;')) !!}
					{{ Form::select('empresa_id[]', $empresas, null,['multiple'=>'multiple','class'=>'form-control','id'=>'empresas','style'=>'width:100%']) }}
					<div style="margin-top:8px; display: flex; gap: 5px;">
						<a href="#" id="selectAll" class="btn btn-xs btn-default" style="border-radius: 4px; padding: 3px 10px;">Todas</a> 
						<a href="#" id="selectNone" class="btn btn-xs btn-default" style="border-radius: 4px; padding: 3px 10px;">Limpar</a>
					</div>
				</div>

				<div class="form-group" style="margin-top:15px;">
					{!! Form::label('status', 'Status:', array('class'=>'control-label', 'style'=>'color: #7f8c8d; font-size: 0.9em;')) !!}
					{!! Form::select('status', array('pendente' => 'Pendente', 'concluido' => 'Concluído'), null, ['class'=>'form-control','id'=>'status', 'style'=>'border-radius: 6px;']) !!}
				</div>
			</div>

			<div style="margin-top: auto; padding-top: 15px;">
				<button type="submit" class="btn btn-info btn-block btn-pill" id="gerarRelatorio">Gerar Relatório</button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>

	<!-- Card 3: Serviços -->
	<div class="col-md-4">
		<div class="form-container" style="min-height: 480px;">
			{!! Form::open(['route'=>'relatorioServicosFilter','method'=>"post", 'style'=>'display:flex; flex-direction:column; justify-content:space-between; height:100%; width:100%;']) !!}
			<div>
				<h3 class="box-title-custom"><i class="fa fa-cogs text-muted"></i> Serviços</h3>
				<p class="text-muted" style="font-size: 0.9em; margin-bottom: 15px;">Download em formato .csv</p>
				
				<div class="form-group">
					{!! Form::label('empresa_id', 'Empresas:', array('class'=>'control-label', 'style'=>'color: #7f8c8d; font-size: 0.9em;')) !!}
					{{ Form::select('empresa_id[]', $empresas, null,['multiple'=>'multiple','class'=>'form-control','id'=>'empresas2','style'=>'width:100%']) }}
					<div style="margin-top:8px; display: flex; gap: 5px;">
						<a href="#" id="selectAll2" class="btn btn-xs btn-default" style="border-radius: 4px; padding: 3px 10px;">Todas</a> 
						<a href="#" id="selectNone2" class="btn btn-xs btn-default" style="border-radius: 4px; padding: 3px 10px;">Limpar</a>
					</div>
				</div>
			</div>

			<div style="margin-top: auto; padding-top: 15px;">
				<button type="submit" class="btn btn-info btn-block btn-pill" id="gerarRelatorio">Gerar Relatório</button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>

<div class="row" style="margin-top: 15px;">
	<div class="col-md-12">
		<div class="table-container">
			<h3 class="box-title-custom"><i class="fa fa-history text-muted"></i> Histórico de Relatórios Gerados</h3>
			
			<table id="reports-table" class="table table-hover" style="width: 100%; margin-bottom: 15px;">
				<thead>
					<tr style="background: #fcfcfc;">
						<th>Nome do Arquivo</th>
						<th width="250">Data de Geração</th>
						<th width="150" class="text-center">Ações</th>
					</tr>
				</thead>
				<tbody>
					<!-- Preenchido dinamicamente via JS -->
				</tbody>
			</table>
			
			<div class="row" style="margin-top: 15px; display: flex; align-items: center;">
				<div class="col-sm-6">
					<span id="reports-info" class="text-muted" style="font-size: 0.9em;"></span>
				</div>
				<div class="col-sm-6 text-right">
					<div id="reports-pagination"></div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function () {
	// Setup CSRF token para requisições AJAX
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	let currentPage = 1;
	const perPage = 10;

	function fetchReports(page) {
		currentPage = page;
		$.ajax({
			url: 'listar-relatorios',
			method: 'GET',
			data: {
				page: page,
				per_page: perPage
			},
			success: function (response) {
				const tableBody = $('#reports-table tbody');
				tableBody.empty();

				if (response.data && response.data.length > 0) {
					response.data.forEach(function (report) {
						const date = new Date(report.date * 1000).toLocaleString('pt-BR');
						const row = `
							<tr>
								<td><code style="font-size: 1.05em; color: #2c3e50; font-family: monospace;">${report.name}</code></td>
								<td><span class="text-muted"><i class="fa fa-calendar-alt" style="margin-right: 5px;"></i> ${date}</span></td>
								<td class="text-center" style="white-space: nowrap;">
									<a href="${report.download_link}" class="btn btn-default btn-action" download title="Download">
										<i class="fa fa-download text-success"></i>
									</a>
									<button class="btn btn-default btn-action delete-report" data-filename="${report.name}" title="Excluir">
										<i class="fa fa-trash text-danger"></i>
									</button>
								</td>
							</tr>
						`;
						tableBody.append(row);
					});

					// Atualiza informações de registros exibidos
					const from = (response.current_page - 1) * response.per_page + 1;
					const to = Math.min(response.current_page * response.per_page, response.total);
					$('#reports-info').text(`Mostrando de ${from} até ${to} de ${response.total} relatórios gerados`);

					// Renderiza controles de paginação
					renderPagination(response.current_page, response.last_page);
				} else {
					tableBody.append('<tr><td colspan="3" class="text-center text-muted" style="padding: 30px;">Nenhum relatório gerado encontrado.</td></tr>');
					$('#reports-info').text('');
					$('#reports-pagination').empty();
				}
			},
			error: function (error) {
				console.error("Erro ao buscar relatórios:", error);
			}
		});
	}

	function renderPagination(currentPage, lastPage) {
		const container = $('#reports-pagination');
		container.empty();

		if (lastPage <= 1) return;

		let html = '<ul class="pagination pagination-sm no-margin" style="margin: 0; display: inline-flex; border-radius: 50px;">';

		// Botão Anterior
		if (currentPage > 1) {
			html += `<li><a href="#" class="page-link" data-page="${currentPage - 1}" style="border-top-left-radius: 50px; border-bottom-left-radius: 50px;"><i class="fa fa-chevron-left"></i></a></li>`;
		} else {
			html += `<li class="disabled"><span style="border-top-left-radius: 50px; border-bottom-left-radius: 50px;"><i class="fa fa-chevron-left"></i></span></li>`;
		}

		// Números de páginas
		for (let i = 1; i <= lastPage; i++) {
			if (i === currentPage) {
				html += `<li class="active"><span>${i}</span></li>`;
			} else {
				html += `<li><a href="#" class="page-link" data-page="${i}">${i}</a></li>`;
			}
		}

		// Botão Próximo
		if (currentPage < lastPage) {
			html += `<li><a href="#" class="page-link" data-page="${currentPage + 1}" style="border-top-right-radius: 50px; border-bottom-right-radius: 50px;"><i class="fa fa-chevron-right"></i></a></li>`;
		} else {
			html += `<li class="disabled"><span style="border-top-right-radius: 50px; border-bottom-right-radius: 50px;"><i class="fa fa-chevron-right"></i></span></li>`;
		}

		html += '</ul>';
		container.append(html);
	}

	// Clique na paginação
	$(document).on('click', '.page-link', function (e) {
		e.preventDefault();
		const page = $(this).data('page');
		fetchReports(page);
	});

	// Inicia listagem
	fetchReports(1);

	// Evento de clique no botão de exclusão
	$(document).on('click', '.delete-report', function () {
		const filename = $(this).data('filename');

		if (confirm('Tem certeza que deseja excluir este relatório?')) {
			$.ajax({
				url: `/deleteRelatorio/${filename}`,
				type: 'DELETE',
				success: function (response) {
					alert(response.message);
					fetchReports(currentPage); // Recarrega os relatórios mantendo na página atual
				},
				error: function (xhr) {
					alert(xhr.responseJSON.message || 'Erro ao excluir o relatório.');
					console.error("Erro ao excluir o relatório:", xhr);
				}
			});
		}
	});

	// Select2 configs
	$("#empresas").select2({
		placeholder: 'Selecione a empresa',
		allowClear: true,
		multiple: true,
	});
	$("#empresas").val('').trigger('change');

	$("#empresas2").select2({
		placeholder: 'Selecione a empresa',
		allowClear: true,
		multiple: true,
	});
	$("#empresas2").val('').trigger('change');

	// Selecionar tudo / Limpar
	$("#selectAll").click(function(e) {
		e.preventDefault();
		$("#empresas option").prop('selected', true);
		$("#empresas").trigger('change');
	});

	$("#selectAll2").click(function(e) {
		e.preventDefault();
		$("#empresas2 option").prop('selected', true);
		$("#empresas2").trigger('change');
	});

	$("#selectNone").click(function(e) {
		e.preventDefault();
		$('#empresas').val(null).trigger('change');
	});

	$("#selectNone2").click(function(e) {
		e.preventDefault();
		$('#empresas2').val(null).trigger('change');
	});
});
</script>
@endsection