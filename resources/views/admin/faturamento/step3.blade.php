@extends('adminlte::page')



@section('content')


<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Resumo do faturamento: </h3>
	</div>


{!! Form::open(['route'=>'faturamento.step4','id'=>'cadastroFaturamento']) !!}

<div class="box-body">

	<div class="col-md-12">
		
		<div class="col-md-6">

			{!! Form::label('descricao', 'Descrição do Faturamento') !!}
			{!! Form::text('descricao', null, ['class'=>'form-control']) !!}	

		</div>

		<div class="col-md-6">

			{!! Form::label('obs', 'Observações') !!}
			{!! Form::text('obs', null, ['class'=>'form-control']) !!}	

		</div>
		


	</div>

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
				<th>Valor em Aberto</th>
				<th>Valor Faturar</th>
				
				
			</thead>
			<tbody>

							@foreach($servicosFaturar as $value => $s)
							<tr>
								{!! Form::hidden('faturamento[]', $value) !!}
								<td></td>	
								<td>{{$s->unidade->codigo}}</td>
								<td>{{$s->unidade->nomeFantasia}}</td>
								<td>{{$s->unidade->cidade}}</td>
								<td>{{$s->unidade->cnpj}}</td>
								<td>{{$s->nome}}</td>
								<td>R$ {{$s->financeiro['valorTotal']}}</td>
								<td>R$ {{$s->financeiro['valorAberto']}}</td>
								<td>{{Form::text('faturamento['.$value.'][valorFaturar]', $s->financeiro['valorAberto'])}}</td>
								
								{!! Form::hidden('faturamento['.$value.'][servico_id]', $s->id) !!}
								{!! Form::hidden('faturamento['.$value.'][valorTotal]', $s->financeiro['valorTotal']) !!}


							</tr>
							@endforeach
						
							
			</tbody>
			<tfoot>
				<tr>
				<td colspan="8" class="lead"><b>Total: </b> R$ {{$total}}</td>
				</tr>
			</tfoot>
		</table>

	</div>

</div>

<div class="box-footer">
                <a href="#" class="btn btn-default">Voltar</a>
                <button type="submit" class="btn btn-danger">GERAR FATURAMENTO</button>
              	</div>
    
{!! Form::close() !!}





@endsection


