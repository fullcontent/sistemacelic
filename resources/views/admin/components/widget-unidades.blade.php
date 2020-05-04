<div class="box box-info collapsed-box">
	<div class="box-header with-border">
		
		<a href="#" data-widget="collapse"><h3 class="box-title">Unidades</h3></a>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
			</button>
			<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		
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
				@foreach($dados->unidades as $unidade)
                	<tr>

	              	<td>{{$unidade->codigo}}</td>
	              	
	              	<td><a href="{{route('unidades.show', $unidade->id)}}">{{$unidade->nomeFantasia}}</a></td>
	              	<td>{{$unidade->cnpj}}</td>
	              	<td>{{$unidade->cidade}}/{{$unidade->uf}}</td>
	              	<td>
	              	@php

	              	$lic = $unidade->servicos->where('tipo','primario')->sortByDesc('created_at')->unique('nome');


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