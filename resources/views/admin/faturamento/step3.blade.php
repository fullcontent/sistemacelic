@extends('adminlte::page')



@section('content')


	<div class="box box-primary">

		<div class="box-header with-border">
			<h3 class="box-title">Resumo do faturamento: </h3>
		</div>



		{!! Form::open(['route' => 'faturamento.step4', 'id' => 'cadastroFaturamento']) !!}

		{!! Form::hidden('empresa_id', $empresa_id) !!}

		<div class="box-body">

			<div class="col-md-12">

				<div class="col-md-6">

					{!! Form::label('descricao', 'Descrição do Faturamento') !!}
					{!! Form::text('descricao', $descricao, ['class' => 'form-control']) !!}

				</div>

				<div class="col-md-5">

					{!! Form::label('obs', 'Observações') !!}
					{!! Form::text('obs', null, ['class' => 'form-control']) !!}

				</div>

				<div class="col-md-1">
					{!! Form::label('link', 'Link para documentos') !!}
					{!! Form::checkbox('link', null, ['class' => 'form-control']) !!}
				</div>

				<div class="col-md-3">

					{!! Form::label('dadosCastro', 'CNPJ Castro') !!}
					{!! Form::select('dadosCastro', $dadosCastro, ['id' => 'dadosCastro', 'class' => 'form-control']) !!}

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
						<th>NF</th>
						<th>Valor Total</th>
						<th>Valor em Aberto</th>
						<th>%</th>
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
								<td>
									<p>{{$s->nome}}</p> <a href="{{route('servicos.show', $s->id)}}"
										class="btn btn-xs btn-success no-print">{{$s->os}}</a>
								</td>
								<td>{!! Form::text('faturamento[' . $value . '][nf]', $s->nf, null, ['class' => 'form-control', 'id' => 'nf']) !!}
								</td>
								<td>R$ {{number_format($s->financeiro['valorTotal'], 2, '.', ',')}}</td>
								<td>R$ {{number_format($s->financeiro['valorAberto'], 2, '.', ',')}}</td>
								<td>
									<div class="input-group">
										<input type="number" class="form-control input-porcento" step="0.01" min="0" max="100"
											value="{{ number_format(($s->financeiro['valorAberto'] / ($s->financeiro['valorTotal'] > 0 ? $s->financeiro['valorTotal'] : 1)) * 100, 2) }}">
										<span class="input-group-addon">%</span>
									</div>
								</td>
								<td>
									<input class="form-control input-valor" type="number" required="true"
										name="faturamento[{{$value}}][valorFaturar]" min="0"
										max="{{$s->financeiro['valorAberto']}}" step=".01"
										value="{{$s->financeiro['valorAberto']}}" data-total="{{$s->financeiro['valorTotal']}}"
										data-aberto="{{$s->financeiro['valorAberto']}}" onFocusOut="checar(this)">
								</td>

								{!! Form::hidden('faturamento[' . $value . '][servico_id]', $s->id) !!}
								{!! Form::hidden('faturamento[' . $value . '][valorTotal]', $s->financeiro['valorTotal']) !!}


							</tr>
						@endforeach


					</tbody>
					<tfoot>
						<tr>
							<td colspan="9" class="lead"><b>Total: </b> <span id="total-faturamento">R$
									{{number_format($total, 2, '.', ',')}}</span></td>
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

		function checar(object) {


			var value = parseInt(object.value);
			var max = parseInt(object.max);

			if (value > max) {
				window.alert('Valor a faturar não pode ser maior que o valor em aberto!');
				console.log("Não pode faturar");
				object.value = max;

				console.log(max);
				console.log(value);
			}
			if (value < max) {
				console.log("Pode faturar");
			}

		}

		$(document).on('input', '.input-porcento', function () {
			var $row = $(this).closest('tr');
			var percentage = parseFloat($(this).val());
			var $valorInput = $row.find('.input-valor');
			var totalValue = parseFloat($valorInput.data('total'));
			var maxVal = parseFloat($valorInput.data('aberto'));

			if (!isNaN(percentage) && !isNaN(totalValue)) {
				var newValue = (percentage / 100) * totalValue;
				if (newValue > maxVal) {
					newValue = maxVal;
					// Optionally update percentage back to show it's capped
					$(this).val(((newValue / totalValue) * 100).toFixed(2));
				}
				$valorInput.val(newValue.toFixed(2));
			}
			updateFooterTotal();
		});

		$(document).on('input', '.input-valor', function () {
			var $row = $(this).closest('tr');
			var value = parseFloat($(this).val());
			var totalValue = parseFloat($(this).data('total'));
			var $porcentoInput = $row.find('.input-porcento');

			if (!isNaN(value) && !isNaN(totalValue) && totalValue > 0) {
				var percentage = (value / totalValue) * 100;
				$porcentoInput.val(percentage.toFixed(2));
			}
			updateFooterTotal();
		});

		function updateFooterTotal() {
			var total = 0;
			$('.input-valor').each(function () {
				var val = parseFloat($(this).val());
				if (!isNaN(val)) {
					total += val;
				}
			});
			$('#total-faturamento').text('R$ ' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
		}


		$('#link').prop('checked', false);


	</script>



	@stop