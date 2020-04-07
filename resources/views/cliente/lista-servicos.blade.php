@extends('adminlte::page')



@section('content')
	
	<div class="box">
				<div class="box-header">
					<h2>{{$title}}</h2>
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

	              		if($servico->situacao == 'andamento')
	              		{
	              			$label = "label-warning";
	              		}
	              		else{

	              			$label = "label-success";
	              		}	              
	              	@endphp
	              	<td>{{$empresa}}</td>
	              	<td>@switch($servico->situacao)

	              			@case('andamento')

								@if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'primario'))
									
									<button type="button" class="btn btn-xs btn-success">Andamento</button>
	              					@elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'primario'))
	              					<button type="button" class="btn btn-xs btn-danger">Andamento</button>
								@elseif($servico->tipo == 'secundario')
									<button type="button" class="btn btn-xs btn-warning">Andamento</button>

	              				@endif

								


	              				@break

	              			@case('finalizado')

	              				@if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'primario'))
									
									<button type="button" class="btn btn-xs btn-success">Finalizado</button>
	              					@elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'primario'))
	              					<button type="button" class="btn btn-xs btn-danger">Finalizado</button>

	              				@elseif($servico->tipo == 'secundario')
									<button type="button" class="btn btn-xs btn-warning">Finalizado</button>

	              				@endif
								
	              				@break

	              			@case('arquivado')
								<button type="button" class="btn btn-xs btn-default">Arquivado</button>
	              				@break

	              		@endswitch</td>
	              	<td>{{$servico->responsavel->name}}</td>

					<td><a href="{{route('cliente.servico.show', $servico->id)}}" class="btn btn-xs btn-flat btn-info">Detalhes</a></td>
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