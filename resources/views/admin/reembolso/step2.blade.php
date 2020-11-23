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
					@php $empresa_id = $e['id'] @endphp
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

	{!! Form::hidden('empresa_id', $empresa_id) !!}

	<div class="col-md-12">
		
		<table class="table table-hover">
			<thead>
				<th></th>
				<th>Cód.</th>
				<th>Loja</th>
				<th>Cidade</th>
				<th>CNPJ</th>
				<th>Serviço</th>
				<th>Total</th>
				
				<th>Em Aberto</th>
				
				
			</thead>
			<tbody>

							@foreach($servicosFaturar as $value => $s)
							<tr>
								<td>{{ Form::checkbox('servicos[]', $s->id,null,['class'=>'checkbox'])}}</td>	
								<td>{{$s->unidade->codigo}}</td>
								<td>{{$s->unidade->nomeFantasia}}</td>
								<td>{{$s->unidade->cidade}}</td>
								<td>@php echo App\Http\Controllers\FaturamentoController::formatCnpjCpf($s->unidade->cnpj); @endphp</td>
								<td>{{$s->nome}}</td>
								<td>R$ {{number_format($s->financeiro['valorTotal'],2,'.',',')}}</td>
								
								<td>R$ {{number_format($s->financeiro['valorAberto'],2,'.',',')}}</td>
								

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


@section('js')

<script>
	$('button[type="submit"]').on('click', function(e) {
  e.preventDefault();
  if($('.checkbox:checked').length > 0) {
      $(this).parents('form').submit();
      return;
  }
  alert('Selecione um serviço da lista!');
  return false;
});
</script>

@endsection