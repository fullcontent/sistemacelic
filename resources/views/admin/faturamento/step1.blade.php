@extends('adminlte::page')

@section('content_header')
    <h1>Faturamento</h1>
@stop



@section('content')

<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Gerar relatório de faturamento</h3>
	</div>


{!! Form::open(['route'=>'faturamento.step2','id'=>'cadastroFaturamento']) !!}

<div class="box-body">
	<div class="col-md-6">
		{!! Form::label('empresa_id', 'Selecione a empresa:', array('class'=>'control-label')) !!}
		
		{{ Form::select('empresa_id[]', $empresas, null,['multiple'=>'multiple','class'=>'form-control','id'=>'empresas']) }}
		<a href="#" id="selectAll">Selecionar Todas</a> | 
		<a href="#" id="selectNone">Limpar seleção</a>
	
	</div>

	<div class="col-md-4">

		{!! Form::label('periodo', 'Selecione o periodo:', array('class'=>'control-label')) !!}
		{{Form::text('periodo', null, ['class'=>'form-control','id'=>'periodo'])}}

	</div>

	<div class="col-md-2">
	{!! Form::label('proposta', 'Propostas:', array('class'=>'control-label')) !!}
	{{ Form::select('propostas[]', $propostas, null,['multiple'=>'multiple','class'=>'form-control','id'=>'propostas']) }}
	</div>
	
</div>

<div class="box-footer">
<a href="javascript: history.go(-1)" class="btn btn-default">Voltar</a>
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

	$('#empresas').change(function () {


		var id = $(this).val();
			 console.log(id);
	 

             $('#propostas').find('option').remove();

             $.ajax({
                url:'{{ url('admin/faturamento/getPropostas') }}/'+id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.data != null) {
                        len = response.data.length;
                    }

					response.data.sort(function(a, b){return a-b});

                    if (len>0) {
                        for (var i = 0; i<len; i++) {
                             var id = response.data[i].val;
                             var proposta = response.data[i].proposta;

                             var option = "<option value='"+response.data[i]+"'>"+response.data[i]+"</option>"; 

                             $("#propostas").append(option);
                        }
                    }
                }
             })
           });

</script>
@endsection