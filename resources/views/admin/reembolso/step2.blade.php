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
		<h3 class="box-title">Selecione as taxas que deseja incluir no reembolso: </h3>
	</div>


{!! Form::open(['route'=>'reembolso.step3','id'=>'cadastroReembolso']) !!}

<div class="box-body">

	{!! Form::hidden('empresa_id', $empresa_id) !!}

	<div class="col-md-12">
		
		<table class="table table-hover">
			<thead>
				
				<th></th>

				<th>Cod.</th>
				<th>Unidade</th>
				<th>Serviço</th>
				<th>Taxa</th>
				<th>Solicitante</th>
				<th>Valor</th>
				<th>Vcto.</th>
				<th>Pgto.</th>
							
				
			</thead>
			<tbody>

				@foreach($taxas as $value => $s)
				<tr>
					<td>{{ Form::checkbox('taxas[]', $s->id,null,['class'=>'checkbox'])}}</td>
					
					<td>{{$s->unidade->codigo}}</td>
					<td>{{$s->unidade->nomeFantasia}}</td>
					<td>{{$s->servico->nome}}</td>
					<td>{{$s->nome}}</td>
					<td>{{$s->servico->solicitante}}</td>
					<td>R$ {{number_format($s->valor,2,'.',',')}}</td>
					<td>{{ \Carbon\Carbon::parse($s->vencimento)->format('d/m/Y')}}</td>
					<td>{{ \Carbon\Carbon::parse($s->pagamento)->format('d/m/Y')}}</td>
										

				</tr>
				@endforeach		
						
							
			</tbody>
		</table>

	</div>

</div>

<div class="box-footer">
                <a href="{{route('reembolso.create')}}" class="btn btn-default">Voltar</a>
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
  alert('Selecione uma taxa da lista!');
  return false;
});
</script>

@endsection