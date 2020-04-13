@extends('adminlte::page')


@section('content')
			
				<div class="box">
					<div class="box-header">
					
						<a class="btn btn-app" href="{{route('empresa.cadastro')}}">
	                		<i class="fa fa-plus"></i> Cadastrar
	         			</a>

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
	              	
	              	<td><a href="{{route('empresa.unidades', $empresa->id)}}">{{$empresa->nomeFantasia}}</a></td>
	              	<td>{{$empresa->cnpj}}</td>
	              	<td>{{$empresa->cidade}}/{{$empresa->uf}}</td>
	              	<td>{{$empresa->telefone}}</td>
					
					<td>
						<div class="btn-group">
                  <button type="button" class="btn btn-default btn-flat">Ações</button>
                  <button type="button" class="btn btn-default btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">

                  	<li><a href="{{route('empresa.unidades',$empresa->id)}}">Ver Unidades</a></li>
                  	<li><a href="#">Taxas</a></li>
                  	<li class="divider"></li>
                    <li><a href="{{route('empresas.show', $empresa->id)}}">Detalhes</a></li>
                    <li><a href="{{route('empresas.edit', $empresa->id)}}">Editar</a></li>
                    <li><a href="{{route('empresas.delete', $empresa->id)}}" class="confirmation">Excluir</a></li>
                                    
                  </ul>
                </div>


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
		     $('.confirmation').on('click', function () {
        		return confirm('Você deseja excluir a empresa?\nTodos os dados relacionados a ela serão excluidos.');
    			});
  });


    </script>
  @stop