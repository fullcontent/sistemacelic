@extends('adminlte::page')



@section('content')

	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Cadastrar taxa</h3>
	</div>

	

	{!! Form::open(['route'=>'taxas.store','enctype'=>'multipart/form-data']) !!}

	@include('admin.partials.form-taxa')
	
	

      			<div class="box-footer">
      			
                
                <button type="submit" class="btn btn-info">Cadastrar</button>
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

	var myDate = new Date();
	var prettyDate =(myDate.getDate()+1) + '/' + myDate.getMonth() + '/' +
        myDate.getFullYear();

		$("#pagamento").val(prettyDate).datepicker("setDate", myDate);
	});

	
	  	
 
});


</script>

@stop