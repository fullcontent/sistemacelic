@extends('adminlte::page')

@section('title', 'Cadastrar taxa')

@section('css')
<style>
	.form-container {
		background: #fff;
		border-radius: 8px;
		padding: 25px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
		border: 1px solid #ebf0f5;
		margin-bottom: 25px;
	}

	.btn-pill {
		border-radius: 50px;
		padding: 6px 20px;
		font-weight: 600;
		transition: all 0.2s;
	}

	.btn-pill:hover {
		transform: translateY(-1px);
		box-shadow: 0 2px 5px rgba(0,0,0,0.1);
	}

	/* Scope controls inside form container */
	.form-container .form-control {
		border-radius: 6px;
		border: 1px solid #d2d6de;
		box-shadow: none;
		transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
	}

	.form-container .form-control:focus {
		border-color: #3c8dbc;
		box-shadow: none;
	}

	.form-container label {
		font-weight: 600;
		color: #555;
		font-size: 0.95em;
		margin-bottom: 6px;
	}

	.form-container .box-body {
		padding: 0;
	}

	.content-header .breadcrumb { display: none !important; }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-12">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Cadastrar Taxa</h1>
	</div>
</div>
@stop

@section('content')
<div class="form-container">
	{!! Form::open(['route'=>'taxas.store','enctype'=>'multipart/form-data','id'=>'cadastroTaxa']) !!}

	@include('admin.partials.form-taxa')
	
	<div style="border-top: 1px solid #f4f4f4; padding-top: 20px; margin-top: 20px; display: flex; gap: 10px;">
		<button type="button" onclick="window.history.back();" class="btn btn-default btn-pill">Voltar</button>
		<button type="submit" class="btn btn-info btn-pill"><i class="fa fa-save" style="margin-right: 5px;"></i> Salvar</button>
	</div>

	{!! Form::close() !!}
</div>
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
	  $("#pagamento").prop('required',true).val(null);
	});


	$( "#responsavelPgto" ).change(function() {
		
		var responsavel = $("#responsavelPgto").val();

		if(responsavel == "cliente"){

			$("#reembolso").val("nao");
			$("#reembolso").prop('disabled',true);
		}

		if(responsavel == "castro"){

			
			$("#reembolso").prop('disabled',false);
			$("#reembolso").val("sim");
			}
	
	 });
	 
	 
	 $("#cadastroTaxa").on("submit",function(){

		$("#reembolso").prop('disabled',false);
	 })
	  	
 
});


</script>

@stop