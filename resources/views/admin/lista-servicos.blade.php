@extends('adminlte::page')



@section('content')
	
	<div class="box">
				<div class="box-header">
					<h2>Listando todos os serviços</h2>
				</div>
				<table id="lista-servicos" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>OS</th>
                  <th>Tipo</th>
                  <th>Nome</th>
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
	              	<td>{{$servico->nome}}</td>

	              	@php
	              		if($servico->unidade_id){
	              			$empresa = $servico->unidade->nomeFantasia;
	              		}
	              		else{
	              			$empresa = $servico->empresa->nomeFantasia	;
	              		}
	              	@endphp
	              	<td>{{$empresa}}</td>
	              	<td>{{$servico->situacao}}</td>
	              	<td>{{$servico->responsavel->name}}</td>

					<td><a href="{{route('servicos.show', $servico->id)}}">Detalhes</a></td>
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