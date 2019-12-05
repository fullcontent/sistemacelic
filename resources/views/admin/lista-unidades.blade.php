@extends('adminlte::page')


@section('content')
			

			<div class="box">
				<div class="box-header">
					<h2>Listando todas as unidades</h2>
				</div>
				<table id="lista-unidades" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Empresa</th>
                  <th>Nome Fantasia</th>
                  <th>CNPJ</th>
                  <th>Cidade/UF</th>
                  <th>Telefone</th>
                  <th>Servicos</th>
                  <th>Taxas</th>
                  
                  <th></th>
                </thead>
                <tbody>
				@foreach($unidades as $unidade)
                	<tr>

	              	<td>{{$unidade->id}}</td>
	              	<td>{{$unidade->empresa->nomeFantasia}}</td>
	              	<td>{{$unidade->nomeFantasia}}</td>
	              	<td>{{$unidade->cnpj}}</td>
	              	<td>{{$unidade->cidade}}/{{$unidade->uf}}</td>
	              	<td>{{$unidade->telefone}}</td>
	              	<td><a href="#">{{count($unidade->servicos)}}</a></td>
	              	<td><a href="#">{{count($unidade->taxas)}}</a></td>
					<td><a href="{{route('unidades.show', $unidade->id)}}">Detalhes</a></td>
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
  @stop