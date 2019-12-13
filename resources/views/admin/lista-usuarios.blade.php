@extends('adminlte::page')





@section('content')

	<div class="box">
		
		<div class="box-header">
					<a class="btn btn-app" href="{{route('usuario.cadastro')}}">
	                		<i class="fa fa-plus"></i> Cadastrar
	         			</a>
				</div>

		<table id="lista-usuarios" class="table table-bordered table-hover">

			<thead>
				<td>ID</td>
				<td>Nome</td>
				<td>Email</td>
				<td>Tipo</td>
				<td>Ações</td>
			</thead>

			<tbody>
				
				@foreach($usuarios as $user)
				<tr>
					<td>{{$user->id}}</td>
					<td>{{$user->name}}</td>
					<td>{{$user->email}}</td>
					<td>{{$user->privileges}}</td>
					<td>
						<div class="btn-group">
                  <button type="button" class="btn btn-default btn-flat">Ações</button>
                  <button type="button" class="btn btn-default btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">

                  	<li><a href="{{route('usuario.editar', $user->id)}}">Editar</a></li>
                                    
                  </ul>
                </div>
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
		    $('#lista-usuarios').DataTable({
		      "paging": true,
		      "lengthChange": false,
		      "searching": true,
		      "ordering": true,
		      "info": true,
		      "autoWidth": false,
		       "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }
		     
		    });
  });
    </script>
  @stop