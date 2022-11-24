@extends('adminlte::page')



@section('content')

<div class="box box-primary">

	<div class="row">
		<div class="col-md-6">
			<div class="box-header with-border">
				<h3 class="box-title">Resutado da busca: </h3>
			</div>
			<div class="box-body">
				<p><b>Período: </b>{{$periodo[0] ?? ''}} a {{$periodo[1] ?? ''}}</p>
			<p><b>Empresa(s): </b>
				<ul>
					@foreach($empresas as $e)
					<li>{{ $e['nomeFantasia'] }}</li>
					@php $empresa_id = $e['id'] @endphp
					@endforeach
					
				</ul></p>
		
		@if(isset($propostas))
			@if($propostas)
			<p><b>Proposta(s): </b>
			<ul>
				@foreach($propostas as $p)
				<li>{{$p}}</li>
				@endforeach
			</ul>
			</p>
			@endif
		@endif
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
		
		<table class="table table-hover" id="lista-servicos">
			<thead>
				<th></th>
				<th>Finalizado</th>
				<th>Cód.</th>
				<th>Proposta</th>
				<th>Loja</th>
				<th>Cidade</th>
				<th>CNPJ</th>
				<th>Serviço</th>
				<th>Solicitante</th>
				<th>Departamento</th>
				<th>Total</th>
				
				<th>Em Aberto</th>
				<th>Faturado em</th>
				
				
			</thead>
			<tbody>
							@if(isset($servicosFaturar->servicoFinalizado))
							@foreach($servicosFaturar->sortBy('servicoFinalizado.finalizado') as $value => $s)
							<tr>
								<td>{{ Form::checkbox('servicos[]', $s->id,null,['class'=>'checkbox'])}}</td>
								<td>{{ \Carbon\Carbon::parse($s->servicoFinalizado->finalizado)->format('d/m/Y')}}</td>	
								<td>{{$s->unidade->codigo}}</td>
								<td>{{$s->unidade->nomeFantasia}}</td>
								<td>{{$s->unidade->cidade}}</td>
								<td>@php echo App\Http\Controllers\FaturamentoController::formatCnpjCpf($s->unidade->cnpj); @endphp</td>
								<td>{{$s->nome}}</td>
								<td>
									@if($s->solicitanteServico)
                     
                        {{$s->solicitanteServico->nome}}

                    @else
                   
                    {{$s->solicitante}}

                    @endif</td>
								<td>R$ {{number_format($s->financeiro['valorTotal'],2,'.',',')}}</td>
								
								<td>R$ {{number_format($s->financeiro['valorAberto'],2,'.',',')}}</td>
								

							</tr>
							@endforeach
							
							@else
							@foreach($servicosFaturar as $value => $s)
							<tr>
								<td>{{ Form::checkbox('servicos[]', $s->id,null,['class'=>'checkbox'])}}</td>
								
								@if(isset($s->servicoFinalizado->finalizado))
								<td>{{ \Carbon\Carbon::parse($s->servicoFinalizado->finalizado)->format('d/m/Y')}}</td>
								@else	
								<td>Andamento</td>
								@endif
								
								
								<td>{{$s->unidade->codigo}}</td>
								<td> 
									@if($s->proposta_id)
									{{$s->proposta_id}}
									@else
									{{$s->proposta}}
									@endif
								</td>
								<td>{{$s->unidade->nomeFantasia}}</td>
								<td>{{$s->unidade->cidade}}</td>
								<td>@php echo App\Http\Controllers\FaturamentoController::formatCnpjCpf($s->unidade->cnpj); @endphp</td>
								<td>{{$s->nome}}</td>
								<td>@if($s->solicitanteServico)
                     
									{{$s->solicitanteServico->nome}}
			
								@else
							   
								{{$s->solicitante}}
			
								@endif</td>

								<td>{{$s->departamento ?? ' '}}</td>
								<td>R$ {{number_format($s->financeiro['valorTotal'],2,'.',',')}}</td>
								
								<td>R$ {{number_format($s->financeiro['valorAberto'],2,'.',',')}}</td>
								<td>
								@if($s->faturado)
								
								{{\Carbon\Carbon::parse($s->faturado->created_at)->format('d/m/Y')}}
								@endif
								</td>

							</tr>
							@endforeach

							@endif
						
							
			</tbody>
		</table>

	</div>

</div>

<div class="box-footer">
<a href="javascript: history.go(-1)" class="btn btn-default">Voltar</a>	
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


$('#lista-servicos').DataTable({
		      "paging": false,
		      "lengthChange": false,
		      "searching": true,
		      "ordering": true,
		      "info": false,
		      "autoWidth": false,
		       "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }           
  });
		    
</script>

@endsection