@extends('adminlte::page')
@section('content_header')
    <h1>Listagem de unidades</h1>
@stop


@section('content')
			

			<div class="box">
				<div class="box-header">
					<a class="btn btn-app" href="{{route('unidade.cadastro')}}">
	                		<i class="fa fa-plus"></i> Cadastrar
	         			</a>
				<div class="pull-right">
					

					<div class="btn-group-vertical" style="padding-left:20px;">
						<p class="text-left "><b>CB: </b><small>AVCB</small></p>
						<p class="text-left "><b>AS: </b><small>Alvará Sanitário</small></p>
						<p class="text-left "><b>LE: </b><small>Licença de Elevador</small></p>


					</div>
					<div class="btn-group-vertical" style="padding-left:20px;">

					<p class="text-left"><b>AF: </b><small>Alvará de Funcionamento</small></p>
					<p class="text-left "><b>AP: </b><small>Alvará de Publicidade</small></p>
					<p class="text-left "><b>AL: </b><small>AMLURB</small></p>
					
					
                      
                  </div>
                  <div class="btn-group-vertical" style="padding-left:20px;">
                  	<p class="text-left "><b>PC: </b><small>Alvará da Polícia Civil</small></p>
					<p class="text-left "><b>LA: </b><small>Licença Ambiental</small></p>
					<p class="text-left "><b>CR: </b><small>CREFITO</small></p>
					
                     
                    </div>
				</div>

				</div>
	         	


				<div class="box-body">

				

				<table id="lista-unidades" class="table table-bordered table-hover">
                <thead>
                <tr>
                 
                  <th>Cod.</th>
                  <th>Nome Fantasia</th>
                  <th>CNPJ</th>
				  <th>Endereço</th>
				  <th>Bairro</th>
                  <th>Cidade/UF</th>
                  <th>Licenças</th>
                  
                  
                  <th></th>
                </thead>
                <tbody>
				@foreach($unidades as $unidade)
                	<tr>

	              	<td>{{$unidade->codigo}}</td>
	              	
	              	<td><a href="{{route('unidades.show', $unidade->id)}}">{{$unidade->nomeFantasia}}</a></td>
	              	<td>{{$unidade->cnpj}}</td>
					  <td>{{$unidade->endereco}}</td>
					  <td>{{$unidade->bairro}}</td>
	              	<td>{{$unidade->cidade}}/{{$unidade->uf}}</td>
	              	<td>
	              	@php

	              	$lic = $unidade->servicos->where('tipo','licencaOperacao')->sortByDesc('created_at')->unique('nome');
					
					  

	              	foreach($lic as $l)
	              	{	

	              		if($l->licenca_validade >= date('Y-m-d'))
		              		{
		              			$label = "btn btn-success btn-xs";
		              		}
		              	elseif($l->licenca_validade < date('Y-m-d'))
		              	{

		              		$label = "btn btn-danger btn-xs";
		              	}



	              		
	              		switch($l->nome){
	              			case 'Alvará de Publicidade':
	              			$name = "AP";

	              			break;
	              			
	              			case 'Alvará Sanitário':
	              			$name = "AS";
	              			break;

	              			case 'Alvará da Polícia Civil':
	              			$name = "PC";
	              			break;

	              			case 'AVCB':
	              			$name = "CB";
	              			break;

	              			case 'Alvará de Funcionamento':
	              			$name = "AF";
	              			break;

	              			case 'Licença Ambiental':
	              			$name = "LA";
	              			break;

							case 'Licença de Elevador':
	              			$name = "LE";
	              			break;
							
							case 'AMLURB':
	              			$name = "AL";
	              			break;
							
							case 'CREFITO':
	              			$name = "CR";
	              			break;



	              		}

	              		if(empty($name))
	              		{
	              			$name = "n/a";
	              		}

	              		
	              		echo "<a href='/admin/servicos/".$l->id."' type=button class='".$label."'>".$name."</a>";
	              	}

	              	@endphp

	              	</td>
	              	
					<td>
						<div class="btn-group">
                  <button type="button" class="btn btn-default btn-flat">Ações</button>
                  <button type="button" class="btn btn-default btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">

                  	<li><a href="#">Taxas</a></li>
                  	<li class="divider"></li>
                    <li><a href="{{route('unidades.show', $unidade->id)}}">Detalhes</a></li>
                    <li><a href="{{route('unidades.edit', $unidade->id)}}">Editar</a></li>
                    <li><a href="{{route('unidade.delete', $unidade->id)}}" class="confirmation">Excluir</a></li>
                                    
				  </ul>
				  
                </div>
</td>
	                </tr>
	            @endforeach
                </tbody>
			  </table> 
			  
			</div>
		</div>
	 		
@stop

@section('js')
<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script>
		$(function () {
		    $('#lista-unidades').DataTable({
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
        		return confirm('Você deseja excluir a unidade?\nTodos os dados relacionados a ela serão excluidos.');
    			});
  });
    </script>
  @stop