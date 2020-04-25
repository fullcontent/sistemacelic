@extends('adminlte::page')



@section('content')

<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Novo Faturamento</h3>
	</div>


{!! Form::open(['route'=>'faturamento.step3','id'=>'cadastroFaturamento']) !!}

<div class="box-body">

	<div class="col-md-12">
			
			<h4>{{$empresa->razaoSocial}}</h4>
			
	</div>

	<div class="col-md-12">
		
		<table class="table table-hover">
			<thead>
				<th></th>
				<th>Solicitante</th>
				<th>Cliente</th>
				<th>Cód</th>
				<th>Unidade</th>
				<th>Cidade</th>
				<th>CNPJ</th>
				<th>Item LPU</th>
				<th>Serviço</th>
				<th>Valor</th>
			</thead>
			<tbody>

							@foreach($servicosFaturar as $s)
							<tr>
								<td>{{ Form::checkbox('servicos_id[]',$s->id) }}</td>
								<td>{{$s->solicitante}}</td>
								<td>{{$s->unidade->empresa->nomeFantasia}}</td>
								<td>{{$s->unidade->codigo}}</td>
								<td>{{$s->unidade->nomeFantasia}}</td>
								<td>{{$s->unidade->cidade}}</td>
								<td>{{$s->unidade->cnpj}}</td>
								<td>{{$s->servico_lpu}}</td>
								<td>{{$s->nome}}</td>
								<td>{{$s->servicoLpu['valor']}}</td>
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