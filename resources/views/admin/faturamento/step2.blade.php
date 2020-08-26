@extends('adminlte::page')



@section('content')

<div class="box box-primary">

	<div class="row">
		<div class="col-md-6">
			<div class="box-header with-border">
				<h3 class="box-title">Resutado da busca: </h3>
			</div>
			<div class="box-body">
				<p><b>Período: </b>{{$periodo[0]}} a {{$periodo[1]}}</p>
			<p><b>Empresa(s): </b>
				<ul>
					@foreach($empresas as $e)
					<li>{{ $e['nomeFantasia'] }}</li>
					@endforeach
					
				</ul></p>
			</div>
			
		</div>
	</div>

</div>



<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Selecione os serviços que deseja incluir no faturamento: </h3>
	</div>


{!! Form::open(['route'=>'faturamento.step3','id'=>'cadastroFaturamento']) !!}

<div class="box-body">

	

	<div class="col-md-12">
		
		<table class="table table-hover">
			<thead>
				<th></th>
				<th>Cód.</th>
				<th>Loja</th>
				<th>Cidade</th>
				<th>CNPJ</th>
				<th>Serviço</th>
				<th>Valor Total</th>
				<th>Valor Faturado</th>
				<th>Valor em Aberto</th>
				
				
			</thead>
			<tbody>

							@foreach($servicosFaturar as $value => $s)
							<tr>
								<td>{{ Form::checkbox('servicos[]', $s->id)}}</td>	
								<td>{{$s->unidade->codigo}}</td>
								<td>{{$s->unidade->nomeFantasia}}</td>
								<td>{{$s->unidade->cidade}}</td>
								<td>{{$s->unidade->cnpj}}</td>
								<td>{{$s->nome}}</td>
								<td>R$ {{$s->financeiro['valorTotal']}}</td>
								<td>R$ {{$s->financeiro['valorFaturado']}}</td>
								<td>R$ {{$s->financeiro['valorAberto']}}</td>
								

							</tr>
							@endforeach
						
							
			</tbody>
		</table>

	</div>

</div>

<div class="box-footer">
                <a href="#" class="btn btn-default">Voltar</a>
                <button type="submit" class="btn btn-info">Próximo Passo</button>
              	</div>
    
{!! Form::close() !!}





@endsection


