@extends('adminlte::page')

@section('content')

	<div class="box">
		
		<div class="box-header">
					<a class="btn btn-app" href="{{route('solicitantes.create')}}">
	                		<i class="fa fa-plus"></i> Cadastrar
	         			</a>
				</div>

		<table id="lista-solicitantes" class="table table-bordered table-hover">

			<thead>
				<td>Nome</td>
				<td>Empresa(s)</td>
				<td>Departamento</td>
				<td>Email</td>
                <td>Telefone</td>
                <td></td>
			</thead>

			<tbody>
				
            @foreach($solicitantes->unique('nome') as $s)
                <tr>
                    <td>{{$s->nome}}</td>
                    <td>
						@foreach($s->empresas as $e)
							<span class="btn btn-info btn-xs">{{$e->nomeFantasia}}</span>
						@endforeach
					</td>
                    <td>{{$s->departamento}}</td>
                    <td>{{$s->email}}</td>
                    <td>{{$s->telefone}}</td>
                    <td>
						<a href="{{route('solicitantes.edit', $s->id)}}" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-edit"></i></a>
						<a href="{{route('solicitantes.destroy', $s->id)}}" class="btn btn-danger btn-xs confirmation"> <i class="glyphicon glyphicon-trash"></i></a>
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