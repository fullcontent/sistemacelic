@extends('adminlte::page')

@section('title', 'Listagem de unidades')

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

	.legend-container {
		display: flex;
		gap: 25px;
		flex-wrap: wrap;
		margin-bottom: 20px;
		padding: 12px 20px;
		background: #fafbfc;
		border-radius: 6px;
		border: 1px solid #ebf0f5;
		font-size: 0.85em;
		color: #555;
	}

	.legend-group {
		display: flex;
		gap: 15px;
	}

	.content-header .breadcrumb { display: none !important; }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-6">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Listagem de Unidades</h1>
	</div>
	<div class="col-sm-6 text-right">
		<a class="btn btn-primary" href="{{route('unidade.cadastro')}}" style="border-radius: 50px; padding: 8px 20px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
			<i class="fa fa-plus"></i> Cadastrar
		</a>
	</div>
</div>
@stop

@section('content')
<div class="table-container">
	<div class="legend-container">
		<div class="legend-group">
			<span><b>CB:</b> AVCB</span>
			<span><b>AS:</b> Alvará Sanitário</span>
			<span><b>LE:</b> Licença de Elevador</span>
		</div>
		<div class="legend-group">
			<span><b>AF:</b> Alvará de Funcionamento</span>
			<span><b>AP:</b> Alvará de Publicidade</span>
			<span><b>AL:</b> AMLURB</span>
		</div>
		<div class="legend-group">
			<span><b>PC:</b> Alvará da Polícia Civil</span>
			<span><b>LA:</b> Licença Ambiental</span>
			<span><b>CR:</b> CREFITO</span>
		</div>
	</div>

	<table id="lista-unidades" class="table table-hover" style="width: 100%;">
		<thead>
			<tr style="background: #fcfcfc;">
				<th>Empresa</th>
				<th>Cod.</th>
				<th>Nome Fantasia</th>
				<th>CNPJ</th>
				<th>Endereço</th>
				<th>Bairro</th>
				<th>Cidade/UF</th>
				<th>Licenças</th>
				<th width="150" class="text-center">Ações</th>
			</tr>
		</thead>
		<tbody>
			@foreach($unidades as $unidade)
				<tr>
					<td>{{$unidade->empresa->nomeFantasia ?? 'N/A'}}</td>
					<td><code>{{$unidade->codigo}}</code></td>
					<td><a href="{{route('unidades.show', $unidade->id)}}" style="font-weight: 600; color: #3c8dbc;">{{$unidade->nomeFantasia}}</a></td>
					<td>{{$unidade->cnpj}}</td>
					<td>{{$unidade->endereco}}</td>
					<td>{{$unidade->bairro}}</td>
					<td>{{$unidade->cidade}}/{{$unidade->uf}}</td>
					<td>
						@if($unidade->licencas_processadas)
							@foreach($unidade->licencas_processadas as $licenca)
								<a href='/admin/servicos/{{$licenca['id']}}' type="button" class="{{$licenca['label']}}" style="margin: 1px; font-size: 0.85em; padding: 2px 6px; border-radius: 4px;">{{$licenca['name']}}</a>
							@endforeach
						@endif
					</td>
					<td class="text-center" style="white-space: nowrap;">
						<a href="{{route('unidades.show', $unidade->id)}}" class="btn btn-default btn-action" title="Detalhes">
							<i class="fa fa-info-circle text-info"></i>
						</a>
						<a href="{{route('unidades.edit', $unidade->id)}}" class="btn btn-default btn-action" title="Editar">
							<i class="fa fa-edit text-warning"></i>
						</a>
						<a href="{{route('unidade.delete', $unidade->id)}}" class="btn btn-default btn-action confirmation" title="Excluir">
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
		$('#lista-unidades').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
			}
		});
		$('.confirmation').on('click', function () {
			return confirm('Você deseja excluir a unidade?\nTodos os dados relacionados a ela serão excluidos.');
		});
	});
</script>
@stop