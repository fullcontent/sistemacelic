@extends('adminlte::page')

@section('content_header')
<h1>Faturamento</h1>
@stop



@section('content')

	<div class="box box-primary">

		<div class="box-header with-border">
			<h3 class="box-title">Gerar relatório de faturamento</h3>
		</div>


		{!! Form::open(['route' => 'faturamento.step2', 'id' => 'cadastroFaturamento']) !!}

		<div class="box-body">
			<div class="col-md-6">
				{!! Form::label('empresa_id', 'Selecione a empresa:', array('class' => 'control-label')) !!}

				{{ Form::select('empresa_id[]', $empresas, null, ['multiple' => 'multiple', 'class' => 'form-control', 'id' => 'empresas']) }}
				<a href="#" id="selectAll">Selecionar Todas</a> |
				<a href="#" id="selectNone">Limpar seleção</a>

			</div>

			<div class="col-md-4">

				{!! Form::label('periodo', 'Selecione o periodo:', array('class' => 'control-label')) !!}
				{{Form::text('periodo', null, ['class' => 'form-control', 'id' => 'periodo'])}}

			</div>

			<div class="col-md-2">
				{!! Form::label('proposta', 'Propostas:', array('class' => 'control-label')) !!}
				{{ Form::select('propostas[]', $propostas, null, ['multiple' => 'multiple', 'class' => 'form-control', 'id' => 'propostas']) }}
			</div>

			<div class="col-md-12">
				<hr>
				<h4>Filtros Adicionais</h4>
			</div>

			<div class="col-md-6">
				{!! Form::label('situacoes', 'Situações do Serviço:', array('class' => 'control-label')) !!}
				{{ Form::select('situacoes[]', [
		'andamento' => 'Andamento',
		'arquivado' => 'Arquivado',
		'finalizado' => 'Finalizado',
		'nrenovado' => 'Nrenovado',
		'standby' => 'Standby',
		'cancelado' => 'Cancelado'
	], ['finalizado'], ['multiple' => 'multiple', 'class' => 'form-control', 'id' => 'situacoes']) }}
			</div>

			<div class="col-md-6">
				{!! Form::label('', 'Status de Faturamento:', array('class' => 'control-label')) !!}
				<div class="checkbox">
					<label>
						{!! Form::checkbox('faturamento_100', 1, false) !!} Listar serviços 100% faturados
					</label>
				</div>
				<div class="checkbox">
					<label>
						{!! Form::checkbox('faturamento_parcial', 1, true) !!} Listar serviços com faturamento parcial
					</label>
				</div>
				<div class="checkbox">
					<label>
						{!! Form::checkbox('faturamento_integral', 1, true) !!} Listar serviços com valor em aberto integral
					</label>
				</div>
			</div>

		</div>

		<div class="box-footer">
			<a href="javascript: history.go(-1)" class="btn btn-default">Voltar</a>
			<button type="submit" class="btn btn-info">Próximo Passo</button>
		</div>

		{!! Form::close() !!}





@endsection


	@section('js')

		<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
		<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


		<script>

			$(document).ready(function () {

				$("#empresas").select2({
					placeholder: 'Selecione a empresa',
					allowClear: true,
					multiple: true,
				});

				$("#situacoes").select2({
					placeholder: 'Selecione as situações',
					allowClear: true,
					multiple: true,
				});

				$("#empresas").val('').trigger('change');




			});



			$('#periodo').daterangepicker();

			$("#selectAll").click(function () {

				$("#empresas option").each(function () {
					$(this).prop('selected', true);
				});

			});

			$("#selectNone").click(function () {

				$("#empresas option").each(function () {
					$(this).prop('selected', false);
				});

			});



			$('#empresas').change(function () {


				var id = $(this).val();
				console.log(id);


				$('#propostas').find('option').remove();

				$.ajax({
					url: '{{ url('admin/faturamento/getPropostas') }}/' + id,
					type: 'get',
					dataType: 'json',
					success: function (response) {
						var len = 0;
						if (response.data != null) {
							len = response.data.length;
						}

						response.data.sort(function (a, b) { return a - b });

						if (len > 0) {
							for (var i = 0; i < len; i++) {
								var id = response.data[i].val;
								var proposta = response.data[i].proposta;

								var option = "<option value='" + response.data[i] + "'>" + response.data[i] + "</option>";

								$("#propostas").append(option);
							}
						}
					}
				})
			});

		</script>
	@endsection