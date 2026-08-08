@extends('adminlte::page')

@section('title', 'Listagem de reembolsos')

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

	.content-header .breadcrumb { display: none !important; }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-6">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Listagem de Reembolsos</h1>
	</div>
	<div class="col-sm-6 text-right">
		<a class="btn btn-primary" href="{{route('reembolso.create')}}" style="border-radius: 50px; padding: 8px 20px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
			<i class="fa fa-plus"></i> Cadastrar
		</a>
	</div>
</div>
@stop

@section('content')
	<div class="table-container">
		<table id="lista-reembolsos" class="table table-hover" style="width: 100%;">
			<thead>
				<tr style="background: #fcfcfc;">
					<th>ID</th>
					<th>Obs</th>
					<th>Cliente</th>
					<th>Data</th>
					<th>Total</th>
					<th width="150" class="text-center">Ações</th>
				</tr>
			</thead>
			<tbody>
				@foreach($reembolsos as $r)
					@php
						$controller = new \App\Http\Controllers\ReembolsoController;
					@endphp
					<tr>
						<td><a href="{{route('reembolso.show', $r->id)}}" style="font-weight: 600; color: #3c8dbc;">{{$controller->fillWithZeros($r->id)}}</a></td>
						<td><a href="{{route('reembolso.show', $r->id)}}" style="color: #555;">{{$r->nome}}</a></td>
						<td>{{$r->empresa->nomeFantasia ?? '-'}}</td>
						<td><span
								style="display:none;">{{$r->created_at}}</span>{{ \Carbon\Carbon::parse($r->created_at)->format('d/m/Y')}}
						</td>
						<td>R$ {{number_format($r->valorTotal, 2, '.', ',')}}</td>
						<td class="text-center" style="white-space: nowrap;">
							<a href="{{route('reembolso.show', $r->id)}}" class="btn btn-default btn-action" title="Ver Detalhes">
								<i class="fa fa-eye text-info"></i>
							</a>
							<a href="{{route('reembolso.download', $r->id)}}" class="btn btn-default btn-action" target="_blank" title="Baixar PDF">
								<i class="fa fa-file-pdf text-success"></i>
							</a>
							
							<div class="btn-group" style="display: inline-block;">
								<button type="button" class="btn btn-default btn-action dropdown-toggle" data-toggle="dropdown" aria-expanded="false" title="Mais Opções">
									<i class="fa fa-ellipsis-v text-muted"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-right pull-right" role="menu">
									<li>
										<a href="#" data-toggle="modal" data-target="#myModal"
											data-reembolso_id="{{ $r->id }}" data-dados_id="{{ $r->dadosCastro_id}}">
											<i class="fa fa-building"></i> Alterar CNPJ
										</a>
									</li>
									<li>
										<a href="{{route('reembolso.downloadZip', $r->id)}}">
											<i class="fa fa-file-archive"></i> Baixar ZIP
										</a>
									</li>
									<li class="divider"></li>
									<li>
										<a href="{{route('reembolso.destroy', $r->id)}}" class="confirmation text-danger">
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

	<!-- The modal -->
	<div class="modal fade" id="myModal">
		<div class="modal-dialog">
			<div class="modal-content">

				<!-- Modal Header -->
				<div class="modal-header">
					<h4 class="modal-title">Select a Company</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>

				<!-- Modal body -->
				<div class="modal-body">
					<form id="company-select-form">
						@csrf
						<select name="dadosCastro_id" class="form-control"></select>

					</form>
				</div>

				<!-- Modal footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary save-selected-item" data-dismiss="modal">Save</button>
				</div>

			</div>
		</div>
	</div>


@endsection



@section('js')


<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script>
	$(function () {
		$('#lista-reembolsos').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json",

			},
			"order": [[3, 'desc']],
		});
		$('.confirmation').on('click', function () {
			return confirm('Você deseja excluir o reembolso?');
		});

	});



</script>

<script>
	$(document).ready(function () {
		$('#myModal').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget); // Button that triggered the modal
			var reembolso_id = button.data('reembolso_id'); // Extract info from data-* attributes
			var dados_id = button.data('dados_id')
			var modal = $(this);

			// Make AJAX call to get the list of companies
			$.get('/api/getDadosCastro', function (data) {
				// Populate the select element with the received data
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
				name: "reembolso_id",
				value: reembolso_id
			});
			$("#company-select-form").append(hiddenInput);



		});

		// When the Save button is clicked, send an AJAX request to save the selected company
		$('.modal-footer .save-selected-item').click(function () {
			var form = $('#company-select-form');
			var data = form.serialize();
			var url = '/api/saveDadosCastro/';

			$.get(url, data, function (response) {
				console.log(data);
			});
		});
	});
</script>
@stop