@extends('adminlte::page')


@section('content')
			

			<div class="box">
				<div class="box-header">
					<h2>Listando todas as empresas</h2>
				</div>
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
                  <th>Taxas</th>
                  <th></th>
                </thead>
                <tbody>
				@foreach($empresas as $empresa)
                	<tr>
	              	<td>{{$empresa->id}}</td>
	              	<td>{{$empresa->nomeFantasia}}</td>
	              	<td>{{$empresa->cnpj}}</td>
	              	<td>{{$empresa->cidade}}/{{$empresa->uf}}</td>
	              	<td>{{$empresa->telefone}}</td>
					<td><a href="{{route('empresa.unidades', $empresa->id)}}">{{count($empresa->unidades)}}</a></td>
					<td><a href="{{route('empresa.unidades', $empresa->id)}}">{{count($empresa->servicos)}}</a></td>
					<td><a href="{{route('empresa.unidades', $empresa->id)}}">{{count($empresa->taxas)}}</a></td>
					<td><a href="{{route('empresas.show', $empresa->id)}}">Detalhes</a></td>
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
  @stop