@extends('adminlte::page')



@section('content')


<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Resumo do faturamento: </h3>
	</div>



{!! Form::open(['route'=>'faturamento.step4','id'=>'cadastroFaturamento']) !!}

{!! Form::hidden('empresa_id', $empresa_id) !!}

<div class="box-body">

	<div class="col-md-12">
		
		<div class="col-md-6">

			{!! Form::label('descricao', 'Descrição do Faturamento') !!}
			{!! Form::text('descricao', $descricao, ['class'=>'form-control']) !!}	

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
								<td>@php
									echo App\Http\Controllers\FaturamentoController::formatCnpjCpf($s->unidade->cnpj);
								   @endphp
								</td>
								<td>{{$s->nome}}</td>
								<td>R$ {{number_format($s->financeiro['valorTotal'],2,'.',',')}}</td>
								<td>R$ {{number_format($s->financeiro['valorAberto'],2,'.',',')}}</td>
								<td>
								<input class="form-control" type="number" required="true" name="faturamento[{{$value}}][valorFaturar]" min="0" max="{{$s->financeiro['valorAberto']}}" step=".01" value="{{$s->financeiro['valorAberto']}}" onFocusOut="checar(this)"> 
</td>
								
								{!! Form::hidden('faturamento['.$value.'][servico_id]', $s->id) !!}
								{!! Form::hidden('faturamento['.$value.'][valorTotal]', $s->financeiro['valorTotal']) !!}


							</tr>
							@endforeach
						
							
			</tbody>
			<tfoot>
				<tr>
				<td colspan="8" class="lead"><b>Total: </b> R$ {{number_format($total,2,'.',',')}}</td>
				</tr>
			</tfoot>
		</table>

	</div>

</div>

<div class="box-footer">
                <a href="javascript: history.go(-1)" class="btn btn-default">Voltar</a>
                <button type="submit" class="btn btn-danger">GERAR FATURAMENTO</button>
              	</div>
    
{!! Form::close() !!}





@endsection

@section('js')

<script>
	function goBack() {
	  window.history.back();
	}

	function checar(object)
  {
   

	var value = parseInt(object.value);
	var max = parseInt(object.max);

	if(value > max)
	{
		window.alert('Valor a faturar não pode ser maior que o valor em aberto!');
		console.log("Não pode faturar");
		object.value = max;

		console.log(max);
		console.log(value);
	}
	if(value < max)
	{
		console.log("Pode faturar");
	}

  }
	

</script>



@stop



