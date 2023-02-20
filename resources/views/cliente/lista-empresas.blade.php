@extends('adminlte::page')

@section('content_header')
    <h1>Listagem de empresas</h1>
@stop

@section('content')
			
				<div class="box">
					<div class="box-header">
					
						

					</div>
				
				<table id="lista-empresas" class="table table-bordered table-hover">

                <thead>

                <tr>
                
                  <th>Nome Fantasia</th>
                  <th>CNPJ</th>
                  <th>Cidade/UF</th>
                  <th>Telefone</th>
                  
                  <th></th>
                </thead>

                <tbody>
				@foreach($empresas as $empresa)
                	<tr>
	              	
	              	<td><a href="{{route('cliente.empresa.unidades', $empresa->id)}}">{{$empresa->nomeFantasia}}</a></td>
	              	<td>{{$empresa->cnpj}}</td>
	              	<td>{{$empresa->cidade}}/{{$empresa->uf}}</td>
	              	<td>{{$empresa->telefone}}</td>
					
					<td><a href="{{route('cliente.empresa.unidades',$empresa->id)}}" class="btn btn-flat btn-info">Unidades</a>
						<a href="{{route('cliente.empresa.show',$empresa->id)}}" class="btn btn-flat btn-warning">Detalhes</a>
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
  });


    </script>
  @stop