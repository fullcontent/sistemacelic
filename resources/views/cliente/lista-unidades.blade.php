@extends('adminlte::page')


@section('content')
			

			<div class="box">
				
				

				<table id="lista-unidades" class="table table-bordered table-hover">
                <thead>
                <tr>
                 
                  
                  <th>Nome Fantasia</th>
                  <th>CNPJ</th>
                  <th>Cidade/UF</th>
                  <th>Telefone</th>
                  
                  
                  <th></th>
                </thead>
                <tbody>
				@foreach($unidades as $unidade)
                	<tr>

	              	
	              	
	              	<td><a href="{{route('cliente.unidade.show', $unidade->id)}}">{{$unidade->nomeFantasia}}</a></td>
	              	<td>{{$unidade->cnpj}}</td>
	              	<td>{{$unidade->cidade}}/{{$unidade->uf}}</td>
	              	<td>{{$unidade->telefone}}</td>
	              	
					<td><a href="{{route('cliente.unidade.show', $unidade->id)}}" class="btn btn-flat btn-warning">Detalhes</a></td>
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