@extends('adminlte::page')



@section('content')


<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Resumo do reembolso: </h3>
	</div>



{!! Form::open(['route'=>'reembolso.step4','id'=>'cadastroReembolso']) !!}

{!! Form::hidden('empresa_id', $empresa->id) !!}

<div class="box-body">

	<div class="col-md-12">
		
		<div class="col-md-4">

			{!! Form::label('empresa', 'Empresa: ') !!}
			{!! Form::text('empresa', $empresa->nomeFantasia, ['class'=>'form-control','disabled'=>'disabled']) !!}	

		</div>
		
		<div class="col-md-4">

			{!! Form::label('descricao', 'Descrição do Reembolso') !!}
			{!! Form::text('descricao', $descricao, ['class'=>'form-control']) !!}	

		</div>

		<div class="col-md-4">

			{!! Form::label('obs', 'Observações') !!}
			{!! Form::text('obs', null, ['class'=>'form-control']) !!}	

		</div>
		
		


	</div>

	<div class="col-md-12">
		
		<table class="table table-hover">
			<thead>
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

							@foreach($taxasReembolsar as $value => $s)
							<tr>
								{!! Form::hidden('taxas[]', $value) !!}
								
								<td>{{$s->unidade->codigo}}</td>
								<td>{{$s->unidade->nomeFantasia}}</td>
								<td>{{$s->servico->nome}}</td>
								<td>{{$s->nome}}</td>
								<td>{{$s->servico->solicitante}}</td>
								<td>R$ {{number_format($s->valor,2,'.',',')}}</td>
								<td>{{ \Carbon\Carbon::parse($s->vencimento)->format('d/m/Y')}}</td>
								<td>{{ \Carbon\Carbon::parse($s->pagamento)->format('d/m/Y')}}</td>

								{!! Form::hidden('taxas['.$value.'][id]', $s->id) !!}
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
                <button type="submit" class="btn btn-danger">GERAR REEMBOLSO</button>
              	</div>
    
{!! Form::close() !!}





@endsection


<script>
	function goBack() {
	  window.history.back();
	}
	</script>

