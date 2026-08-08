@extends('adminlte::page')

@section('title', 'Listagem de empresas')

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
		<h1 style="margin: 0; font-weight: 700; color: #333;">Listagem de Empresas</h1>
	</div>
	<div class="col-sm-6 text-right">
		@if(Auth::id() <= 3)
		<a class="btn btn-primary" href="{{route('empresa.cadastro')}}" style="border-radius: 50px; padding: 8px 20px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
			<i class="fa fa-plus"></i> Cadastrar
		</a>
		@endif
	</div>
</div>
@stop

@section('content')
<div class="table-container">
	<table id="lista-empresas" class="table table-hover" style="width: 100%;">
		<thead>
			<tr style="background: #fcfcfc;">
				<th>Nome Fantasia</th>
				<th>CNPJ</th>
				<th>Cidade/UF</th>
				<th>Telefone</th>
				<th width="150" class="text-center">Ações</th>
			</tr>
		</thead>
		<tbody>
			@foreach($empresas as $empresa)
				<tr>
					<td><a href="{{route('empresa.unidades', $empresa->id)}}" style="font-weight: 600; color: #3c8dbc;">{{$empresa->nomeFantasia}}</a></td>
					<td>{{$empresa->cnpj}}</td>
					<td>{{$empresa->cidade}}/{{$empresa->uf}}</td>
					<td>{{$empresa->telefone}}</td>
					<td class="text-center" style="white-space: nowrap;">
						<a href="{{route('empresa.unidades',$empresa->id)}}" class="btn btn-default btn-action" title="Ver Unidades">
							<i class="fa fa-building text-primary"></i>
						</a>
						<a href="{{route('empresas.show', $empresa->id)}}" class="btn btn-default btn-action" title="Detalhes">
							<i class="fa fa-info-circle text-info"></i>
						</a>
						<a href="{{route('empresas.edit', $empresa->id)}}" class="btn btn-default btn-action" title="Editar">
							<i class="fa fa-edit text-warning"></i>
						</a>
						<a href="{{route('empresa.delete', $empresa->id)}}" class="btn btn-default btn-action confirmation" title="Excluir">
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
		$('#lista-empresas').DataTable({
			"paging": true,
			"lengthChange": false,
			"searching": true,
			"ordering": true,
			"info": false,
			"autoWidth": false,
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
			}
		});

		$('.confirmation').on('click', function () {
			return confirm('Você deseja excluir a empresa?\nTodos os dados relacionados a ela serão excluidos.');
		});
	});
</script>
@stop