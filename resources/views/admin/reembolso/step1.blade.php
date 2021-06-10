@extends('adminlte::page')

@section('content_header')
    <h1>Reembolso</h1>
@stop



@section('content')

<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Gerar Reembolso</h3>
	</div>


{!! Form::open(['route'=>'reembolso.step2','id'=>'cadastroReembolso']) !!}

<div class="box-body">
	<div class="col-md-6">
		{!! Form::label('empresa_id', 'Selecione a empresa:', array('class'=>'control-label')) !!}
		{{ Form::select('empresa_id[]', $empresas, null,['multiple'=>'multiple','class'=>'form-control','id'=>'empresas']) }}
		<a href="#" id="selectAll">Selecionar Todas</a> | 
		<a href="#" id="selectNone">Limpar seleção</a>
	
	</div>

	<div class="col-md-6">

		{!! Form::label('periodo', 'Selecione o periodo:', array('class'=>'control-label')) !!}
		{{Form::text('periodo', null, ['class'=>'form-control','id'=>'periodo'])}}

	</div>
	
</div>

<div class="box-footer">
                <a href="{{route('dashboard')}}" class="btn btn-default">Voltar</a>
                <button type="submit" class="btn btn-info">Próximo Passo</button>
              	</div>
    
{!! Form::close() !!}





@endsection


@section('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>

$('#periodo').daterangepicker();

	$("#selectAll").click(function(){ 
		
		$("#empresas option").each(function(){
			$(this).prop('selected', true);
		});
		
	});

	$("#selectNone").click(function(){ 
		
		$("#empresas option").each(function(){
			$(this).prop('selected', false);
		});
		
	});

	



</script>
@endsection