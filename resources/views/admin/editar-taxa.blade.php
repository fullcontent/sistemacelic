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

    if(!comprovante) { // returns true if the string is not empty
		$("#pagamento").prop('disabled',true).val(null);

    } 
	
		$( "#comprovante" ).change(function() {
  		$("#pagamento").prop('disabled',false);
  		$("#situacao").val('pago');
		  $("#pagamento").attr("required", "true");
		  var myDate = new Date();
			var prettyDate =(myDate.getDate()+1) + '/' + myDate.getMonth() + '/' + myDate.getFullYear();

		$("#pagamento").val(prettyDate).datepicker("setDate", myDate);
	});
	

    
	  	
 	
});


</script>

@stop