@extends('adminlte::page')
@section('content_header')
    <h1>Listagem de serviços</h1>
@stop


@section('content')
	
	<div class="box" style="padding: 5px;">
				<div class="box-header">
					
				</div>
				<table id="lista-servicos" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>Tipo</th>
				  <th>OS</th>
                  <th>Serviço</th>
                  <th>Cod. Unid.</th>
				  <th>Unidade</th>
				  <th>Cidade</th>
                  <th>Situação</th>
                  
                  <th></th>
                </thead>
                <tbody>
				@foreach($servicos as $servico)
                	
                	<tr>
						<td>@switch($servico->tipo)
							@case('nRenovaveis')
							Licenças/Projetos não renováveis
								@break
							@case('licencaOperacao')
								Licença de Operação
								@break
							@case('controleCertidoes')
								Certidões
								@break
							@case('controleTaxas')
								Taxas
								@break
						  @case('facilitiesRealEstate')
							  Facilities/Real Estate
								@break
							@default
								
						@endswitch</td>
					<td>{{$servico->os}}</td>
					 
	              	
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
					<td>{{$servico->unidade->cidade}}/{{$servico->unidade->uf}}</td>
	              	<td>

	              		

	              		@switch($servico->situacao)

	              			@case('andamento')

								@if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))
									
									<button type="button" class="btn btn-xs btn-success">Andamento</button>
	              					@elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'licencaOperacao'))
	              					<button type="button" class="btn btn-xs btn-danger">Andamento</button>
								@elseif($servico->tipo == 'nRenovaveis')
									<button type="button" class="btn btn-xs btn-warning">Andamento</button>

	              				@endif

								


	              				@break

	              			@case('finalizado')

	              				@if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))
									
									<button type="button" class="btn btn-xs btn-success">Finalizado</button>
	              					@elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'licencaOperacao'))
	              					<button type="button" class="btn btn-xs btn-danger">Finalizado</button>

	              				@elseif($servico->tipo == 'nRenovaveis')
									<button type="button" class="btn btn-xs btn-warning">Finalizado</button>

	              				@endif
								
	              				@break

	              			@case('arquivado')
								<button type="button" class="btn btn-xs btn-default">Arquivado</button>
	              				@break
							@case('nRenovado')
								<button type="button" class="btn btn-xs btn-gray">Não renovado</button>
	              				@break

	              		@endswitch

					@if (\Request::is('admin/servico/vencer'))  
  							<a href="{{route('servico.renovar',$servico->id)}}" class="btn btn-xs btn-primary">Renovar</a>
					@endif

					@if (\Request::is('admin/servico/vencer'))  
  							<a href="{{route('servico.desconsiderar',$servico->id)}}" class="btn btn-xs btn-info">Desconsiderar</a>
					@endif

	              	</td>
	              	

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