@extends('adminlte::page')

@section('title', 'Listagem de solicitantes')

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
		<h1 style="margin: 0; font-weight: 700; color: #333;">Listagem de Solicitantes</h1>
	</div>
	<div class="col-sm-6 text-right">
		<a class="btn btn-primary" href="{{route('solicitantes.create')}}" style="border-radius: 50px; padding: 8px 20px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
			<i class="fa fa-plus"></i> Cadastrar
		</a>
	</div>
</div>
@stop

@section('content')
	<div class="table-container">
		<table id="lista-solicitantes" class="table table-hover" style="width: 100%;">
			<thead>
				<tr style="background: #fcfcfc;">
					<th>Nome</th>
					<th>Empresa(s)</th>
					<th>Departamento</th>
					<th>Email</th>
					<th>Telefone</th>
					<th width="120" class="text-center">Ações</th>
				</tr>
			</thead>
			<tbody>
				@foreach($solicitantes->unique('nome') as $s)
					<tr>
						<td style="font-weight: 600; color: #555;">{{$s->nome}}</td>
						<td>
							@foreach($s->empresas as $e)
								<span class="btn btn-info btn-xs" style="margin: 1px; border-radius: 4px;">{{$e->nomeFantasia}}</span>
							@endforeach
						</td>
						<td>{{$s->departamento}}</td>
						<td>{{$s->email}}</td>
						<td>{{$s->telefone}}</td>
						<td class="text-center" style="white-space: nowrap;">
							<a href="{{route('solicitantes.edit', $s->id)}}" class="btn btn-default btn-action" title="Editar">
								<i class="fa fa-edit text-warning"></i>
							</a>
							<a href="{{route('solicitantes.destroy', $s->id)}}" class="btn btn-default btn-action confirmation" title="Excluir">
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
<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script>
		$(function () {
		    $('#lista-solicitantes').DataTable({
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
        		return confirm('Você deseja excluir o usuario?');
    			});
		     
		   
  });

    </script>
  @stop