@extends('adminlte::page')



@section('content')
	
	<div class="box">
				<div class="box-header">
					<h3>Listagem de serviços</h3>
				</div>
				<table id="lista-servicos" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>OS</th>
                  <th>Tipo</th>
                  <th>Serviço</th>
                  <th>Cod. Unid.</th>
                  <th>Empresa/Unidade</th>
                  <th>Situação</th>
                  <th>Responsável</th>
                  <th></th>
                </thead>
                <tbody>
				@foreach($servicos as $servico)
                	
                	<tr>
	              	<td>{{$servico->os}}</td>
	              	<td>{{$servico->tipo}}</td>
	              	<td><a href="{{route('servicos.show', $servico->id)}}">{{$servico->nome}}</a></td>

	              	@php
	              		if($servico->unidade_id){
	              			$empresa = $servico->unidade->nomeFantasia;
	              			$route = route('unidades.show',$servico->unidade->id);
	              		}
	              		elseif($servico->empresa_id){
	              			$empresa = $servico->empresa->nomeFantasia;
	              			$route = route('empresas.show',$servico->empresa->id);
	              		}
	              	@endphp
	              	<td>{{$servico->unidade->codigo ?? ''}}</td>
	              	<td><a href="{{$route}}">{{$empresa}}</a></td>
	              	<td>{{$servico->situacao}}</td>
	              	<td>{{$servico->responsavel->name}}</td>

					<td><a href="{{route('servicos.show', $servico->id)}}"><i class="glyphicon glyphicon-list-alt
"></i></a>
						<a href="{{route('servico.delete', $servico->id)}}" class="confirmation danger"> <i class="glyphicon glyphicon-trash
"></i></a></td>
	                </tr>
	            @endforeach
                </tbody>
              </table>   
			</div>
	 		

@endsection



@section('js')
<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script>
		$(function () {
		    $('#lista-servicos').DataTable({
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
        		return confirm('Você deseja excluir o serviço?');
    			});
		     
		    });

    </script>
  @stop