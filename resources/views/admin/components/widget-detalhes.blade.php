<div class="box box-gray">
	<div class="box-header with-border">
		
		<a href="{{route('unidades.show',$dados->id)}}"><h3 class="box-title text-bold">{{$dados->codigo}} | {{$dados->nomeFantasia}}</h3></a>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
			</button>
			<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		
		<div class="col-md-4">
			
			<p><b>Nome da Unidade: </b>{{$dados->nomeFantasia}}</p>
			<p><b>Razão Social:</b> {{$dados->razaoSocial}}</p>
			<p><b>CNPJ:</b> {{$dados->cnpj}}</p>
			<p><b>Status: </b>{{$dados->status}}</p>
			<p><b>Data de Inauguração: </b>@if($dados->dataInauguracao){{\Carbon\Carbon::parse($dados->dataInauguracao)->format('d/m/Y')}}@endif</p>
			<p><b>Ins. Estadual:</b> {{$dados->inscricaoEst}}</p>
			<p><b>Insc. Municipal:</b> {{$dados->inscricaoMun}}</p>
		</div>
		<div class="col-md-4">
			<p><b>Código:</b> {{$dados->codigo}}</p>
			<p><b>Inscrição Imobiliária:</b> {{$dados->inscricaoImo}}</p>
			<p><b>RIP:</b> {{$dados->rip}}</p>
			<p><b>Matricula RI:</b> {{$dados->matriculaRI}}</p>
			<p><b>Área da Loja:</b> {{$dados->area}} m2</p>
			<p><b>Área do Terreno:</b> {{$dados->areaTerreno}} m2</p>
			<p><b>Imóvel:</b> {{$dados->tipoImovel}}</p>
		</div>
		<div class="col-md-4">
			
			<p><b>Endereço:</b> {{$dados->endereco}}</p>
			<p><b>Número: </b>{{ $dados->numero}}</p>
			<p><b>Complemento: </b>{{ $dados->complemento }}</p>
			<p><b>Cidade/UF:</b> {{$dados->cidade}}/{{$dados->uf}}</p>
			<p><b>CEP:</b> {{$dados->cep}}</p>
			<p><b>Bairro:</b> {{$dados->bairro}}</p>
		</div>

		@if(Route::is('unidades.show'))
		<div class="pull-right">
		<a href="{{route('unidades.edit', $dados->id)}}" class="btn btn-info pull-right"><span class="glyphicon glyphicon-pencil"></span> Editar</a>
		</div>
		@endif
	</div>
</div>