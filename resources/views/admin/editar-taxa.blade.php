@extends('adminlte::page')

@section('content_header')
    <h1>{{$taxa->nome}}</h1>
@stop



@section('content')

	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Cadastrar taxa</h3>
	</div>

	

{!! Form::model($taxa,['route'=>['taxas.update', $taxa->id],'method'=>'put','enctype'=>'multipart/form-data']) !!}

	@include('admin.partials.form-taxa')
	
	

      			<div class="box-footer">
      			<a href="{{route('servicos.show', $taxa->servico_id)}}" class="btn btn-default">Voltar</a>
                
                <button type="submit" class="btn btn-info">Editar</button>
              	</div>
    	
    
	
	{!! Form::close() !!}

@endsection

@section('js')

<script>
	
	$(document).ready(function() {

  	$("#emissao").datepicker();


  	$("#vencimento").datepicker();
  	$("#pagamento").datepicker();

  	 	
  	$("#valor").mask('000.000.000.000.000,00', {reverse: true});

  	var comprovante = $("#comprovante").val();

    if(comprovante) { // returns true if the string is not empty
        alert(comprovante + " existe");
    } else { // no file was selected
        $("#pagamento").prop('disabled',true);
    }

    $( "#comprovante" ).change(function() {
  		$("#pagamento").prop('disabled',false);
  		$("#situacao").val('pago');
	});
	  	
 	
});


</script>

@stop