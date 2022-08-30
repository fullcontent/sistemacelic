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
			<p><b>Taxas já dentro de um relatório de reembolso: </b><button onclick="mostrarTaxas()" id="btnMostrar">Incluir</button></p>
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
		<p><b><input type="checkbox" id="checkAll" > Selecionar Todas</b></p>
		<table class="table table-hover" id="lista-servicos">
			<thead>
				
				<th></th>

				<th>Cod.</th>
				<th>Unidade</th>
				<th>Serviço</th>
				<th>Taxa</th>
				<th>Solicitante</th>
				<th width="100">Valor</th>
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
					<td>
						@if(!is_numeric($s->servico->solicitante))
						{{$s->servico->solicitante}}
						@else
						{{\App\Models\Solicitante::where('id',$s->servico->solicitante)->value('nome')}}
						@endif
					</td>
					<td>R$ {{number_format($s->valor,2,'.',',')}}</td>
					<td>{{ \Carbon\Carbon::parse($s->vencimento)->format('d/m/Y')}}</td>
					<td>{{ \Carbon\Carbon::parse($s->pagamento)->format('d/m/Y')}}</td>
										

				</tr>
				@endforeach		

				@foreach($reembolsadas as $value2 => $s2)
				<tr style="background-color: #ccc; display: none;" id="reembolsada">
					<td>{{ Form::checkbox('taxas[]', $s2->id,null,['class'=>'checkbox','id'=>'reembolsada2'])}}</td>
					
					<td>{{$s2->unidade->codigo}}</td>
					<td>{{$s2->unidade->nomeFantasia}}</td>
					<td>{{$s2->servico->nome}}</td>
					<td>{{$s2->nome}}</td>
					<td>@if(!is_numeric($s2->servico->solicitante))
						{{$s2->servico->solicitante}}
						@else
						{{\App\Models\Solicitante::find($s2->servico->solicitante)->value('nome')}}
						@endif</td>
					<td>R$ {{number_format($s2->valor,2,'.',',')}}</td>
					<td>{{ \Carbon\Carbon::parse($s2->vencimento)->format('d/m/Y')}}</td>
					<td>{{ \Carbon\Carbon::parse($s2->pagamento)->format('d/m/Y')}}</td>
										

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
				alert('Você deve selecionar pelo menos uma taxa da lista!');
				return false;
	});

	function mostrarTaxas() {
		var x = document.getElementById("reembolsada");
		if (x.style.display === "none") {
			x.style.display = "";
			$('#btnMostrar').text("Esconder");
		} else {
			x.style.display = "none";
			$('#btnMostrar').text("Mostrar");
		}
		}


		$(":checkbox").on("change", function() {
        //When the id is test1
        //And name is A
        //And it's checked
        if (this.id === "reembolsada2") {
			
            alert ("Essa taxa ja está dentro de um relatório de reembolso.");
        }
    });


	$('#checkAll').click(function () {    
     $('input:checkbox').prop('checked', this.checked);    
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
            },
  });

</script>

@endsection