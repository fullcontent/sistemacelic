@extends('adminlte::page')


@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

			<h1>Empresas</h1>
   
              <table id="lista-empresas" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Nome Fantasia</th>
                  <th>CNPJ</th>
                  <th>Cidade/UF</th>
                  <th>Telefone</th>
                  <th>Unidades</th>
                  <th>Serviços</th>
                </thead>
                <tbody>
				@foreach($empresas as $empresa)
                	<tr>
	              	<td>{{$empresa->id}}</td>
	              	<td>{{$empresa->nomeFantasia}}</td>
	              	<td>{{$empresa->cnpj}}</td>
	              	<td>{{$empresa->cidade}}/{{$empresa->uf}}</td>
	              	<td>{{$empresa->telefone}}</td>
					<td>{{count($empresa->unidades)}}</td>
					<td></td>
	                </tr>
	            @endforeach
                </tbody>
              </table>

              <h1>Unidades</h1>

              <table id="lista-unidades" class="table table-bordered table-hover">
                <thead>
                <tr>
                	<td>ID</td>
                	<td>Nome Fantasia</td>
                	<td>Empresa</td>
                	
                </tr>
                </thead>
                <tbody>
                	@foreach($unidades as $unidade)
				<tr>
					<td>{{$unidade->id}}</td>
					<td>{{$unidade->nomeFantasia}}</td>
					<td>{{$unidade->empresa->nomeFantasia}}</td>
				</tr>
				@endforeach
                </tbody>
              </table>

			<h1>Usuarios</h1>
              <table id="lista-usuarios" class="table table-bordered table-hover">
                <thead>
                <tr>
                	<td>ID</td>
                	<td>Nome</td>
                	<td>email</td>
                	<td>Empresas</td>
                	<td>Unidades</td>
                	<td></td>
                </tr>
                </thead>
                <tbody>
                	@foreach($users as $usuario)
				<tr>
					<td>{{$usuario->id}}</td>
					<td>{{$usuario->name}}</td>
					<td>{{$usuario->email}}</td>
					<td></td>
					<td></td>
				</tr>
				@endforeach
                </tbody>
              </table>


@stop




@section('js')
<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
    <script>
		$(function () {
		    $('#lista-empresas').DataTable({
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
     <script>
		$(function () {
		    $('#lista-unidades').DataTable({
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