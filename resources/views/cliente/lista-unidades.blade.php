@extends('adminlte::page')

@section('content_header')
    <h1>Listagem de unidades</h1>
@stop

@section('content')
			

			<div class="box">
				<div class="box-header">
					
				<div class="pull-right">
					

					<div class="btn-group-vertical" style="padding-left:20px;">
						<p class="text-left "><b>CB: </b><small>AVCB</small></p>
						<p class="text-left "><b>AS: </b><small>Alvará Sanitário</small></p>


					</div>
					<div class="btn-group-vertical" style="padding-left:20px;">

					<p class="text-left"><b>AF: </b><small>Alvará de Funcionamento</small></p>
					<p class="text-left "><b>AP: </b><small>Alvará de Publicidade</small></p>
					
					
                      
                  </div>
                  <div class="btn-group-vertical" style="padding-left:20px;">
                  	<p class="text-left "><b>PC: </b><small>Alvará da Polícia Civil</small></p>
					<p class="text-left "><b>LA: </b><small>Licença Ambiental</small></p>
					
                     
                    </div>
				</div>

				</div>
	         	


				

				<table id="lista-unidades" class="table table-bordered table-hover">
                <thead>
                <tr>
                 
                  <th>Cod.</th>
                  <th>Nome Fantasia</th>
                  <th>CNPJ</th>
                  <th>Cidade/UF</th>
                  <th>Licenças</th>
                  
                  
                  <th></th>
                </thead>
                <tbody>
				@foreach($unidades as $unidade)
                	<tr>

	              	<td>{{$unidade->codigo}}</td>
	              	
	              	<td><a href="{{route('cliente.unidade.show', $unidade->id)}}">{{$unidade->nomeFantasia}}</a></td>
	              	<td>{{$unidade->cnpj}}</td>
	              	<td>{{$unidade->cidade}}/{{$unidade->uf}}</td>
	              	<td>
	              	@php

	              	$lic = $unidade->servicos->where('tipo','licencaOperacao')->sortByDesc('created_at')->unique('nome');




	              	foreach($lic as $l)
	              	{	

	              		if($l->licenca_validade > date('Y-m-d'))
		              		{
		              			$label = "btn btn-success btn-xs";
		              		}
		              	else{

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

	              		}

	              		
	              		echo "<a href='/cliente/servico/".$l->id."' type=button class='".$label."'>".$name."</a>";
	              	}

	              	@endphp

	              	</td>
	              	
					<td>
						<a href="{{route('cliente.unidade.show', $unidade->id)}}" class="btn btn-flat btn-warning">Detalhes</a>
						
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
  });
    </script>
  @stop