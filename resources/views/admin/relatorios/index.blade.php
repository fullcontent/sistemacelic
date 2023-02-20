@extends('adminlte::page')
@section('content_header')
    <h1>Relatórios</h1>
@stop


@section('content')

<div class="col-md-3">
    <div class="box box-default">
        <div class="box-header with-border">

            <h3 class="box-title">Completos</h3>
            <p>Download em formato .csv</p>
        </div>

        <div class="box-body">


            <a href="{{route('relatorio.completo')}}" target="_blank"
                class="btn btn-block btn-default btn-lg">Serviços</a>

            <a href="{{route('relatorio.taxas')}}" target="_blank" class="btn btn-block btn-default btn-lg">Taxas</a>

            <a href="{{route('relatorio.pendencias')}}" target="_blank"
                class="btn btn-block btn-default btn-lg">Pendências</a>


        </div>

    </div>

</div>

<div class="col-md-4">
    <div class="box box-default">
        <div class="box-header with-border">

            <h3 class="box-title">Relatórios de pendências</h3>
            <p>Download em formato .csv</p>
        </div>

        <div class="box-body">
           

            {!! Form::open(['route'=>'relatorioPendenciasFilter','method'=>"post"]) !!}


            <div class="col-md-6">
                {!! Form::label('empresa_id', 'Selecione a empresa:', array('class'=>'control-label')) !!}
                
                {{ Form::select('empresa_id[]', $empresas, null,['multiple'=>'multiple','class'=>'form-control','id'=>'empresas']) }}
                <a href="#" id="selectAll">Selecionar Todas</a> | 
                <a href="#" id="selectNone">Limpar seleção</a>
            
            </div>

            <div class="col-md-6">
                <div class="form-group">
                {!! Form::label('status', 'Status', array('class'=>'control-label')) !!}
                {!! Form::select('status', array('pendente' => 'Pendente', 'concluido' => 'Concluido'), null, ['class'=>'form-control','id'=>'status']) !!}
                </div>
            </div>


          



        </div>

        <div class="box-footer">
                
            <button type="submit" class="btn btn-info" id="gerarRelatorio">Gerar</button>
</div>





{!! Form::close() !!}

    </div>

</div>


<div class="col-md-4">
    <div class="box box-default">
        <div class="box-header with-border">

            <h3 class="box-title">Relatórios de Serviços</h3>
            <p>Download em formato .csv</p>
        </div>

        <div class="box-body">
           

            {!! Form::open(['route'=>'relatorioServicosFilter','method'=>"post"]) !!}


            <div class="col-md-6">
                {!! Form::label('empresa_id', 'Selecione a empresa:', array('class'=>'control-label')) !!}
                
                {{ Form::select('empresa_id[]', $empresas, null,['multiple'=>'multiple','class'=>'form-control','id'=>'empresas2']) }}
                <a href="#" id="selectAll">Selecionar Todas</a> | 
                <a href="#" id="selectNone">Limpar seleção</a>
            
            </div>

            


          



        </div>

        <div class="box-footer">
                
            <button type="submit" class="btn btn-info" id="gerarRelatorio">Gerar</button>
</div>





{!! Form::close() !!}

    </div>

</div>


@endsection


@section('js')


<script>
    $(document).ready(function() {

$("#empresas").select2({
	placeholder: 'Selecione a empresa',
	allowClear: true,
	multiple: true,
});

$("#empresas").val('').trigger('change');

$("#empresas2").select2({
	placeholder: 'Selecione a empresa',
	allowClear: true,
	multiple: true,
});

$("#empresas2").val('').trigger('change');

$("#selectAll").click(function(){ 
		
		$("#empresas option").each(function(){
			$(this).prop('selected', true);
		});

        console.log("Todas selecionadas");


		
	});

    $("#selectAll2").click(function(){ 
		
		$("#empresas2 option").each(function(){
			$(this).prop('selected', true);
		});

        console.log("Todas selecionadas");


		
	});

	$("#selectNone").click(function(){ 
		
		$('#empresas').val(null).trigger('change');


        console.log("Limpar selecao");
		
	});

    $("#selectNone2").click(function(){ 
		
		$('#empresas2').val(null).trigger('change');


        console.log("Limpar selecao");
		
	});



$("#gerarRelatorio").on("submit",function(){


    console.log("test");

})


  

});
</script>
@endsection