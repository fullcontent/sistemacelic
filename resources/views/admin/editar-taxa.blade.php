@extends('adminlte::page')

@section('title', 'Editar taxa')

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
		<h1 style="margin: 0; font-weight: 700; color: #333;">Editar Taxa: {{$taxa->nome}}</h1>
	</div>
</div>
@stop

@section('content')
<div class="form-container">
	{!! Form::model($taxa,['route'=>['taxas.update', $taxa->id],'method'=>'put','enctype'=>'multipart/form-data']) !!}

	@include('admin.partials.form-taxa')

	<div style="border-top: 1px solid #f4f4f4; padding-top: 20px; margin-top: 20px; display: flex; gap: 10px;">
		<a href="{{route('servicos.show', $taxa->servico_id)}}" class="btn btn-default btn-pill"><i class="fa fa-chevron-left" style="margin-right: 5px;"></i> Voltar</a>
		<button type="submit" class="btn btn-info btn-pill" id="submitBtn"><i class="fa fa-save" style="margin-right: 5px;"></i> Salvar</button>
	</div>

	{!! Form::close() !!}
</div>
@endsection


@section('js')

<script>
	
	$(document).ready(function() {

		

  	
		var comprovante = "{{$taxa->comprovante}}";
		var pagamento = "{{\Carbon\Carbon::parse($taxa->pagamento)->format('d/m/Y')}}";
		var boleto = "{{$taxa->boleto}}";
		var valor = "{{number_format($taxa->valor,2)}}";
		

		$("#emissao").datepicker();
		$("#vencimento").datepicker();	
		$("#pagamento").datepicker();


		if("{{Route::is('taxas.show')}}")
		{
			$("#valor").val(valor).mask('000.000.000.000.000,00', {reverse: true});
		}

		
		$("#valor").keypress(function(){
			console.log($( this ).val());
			$("#valor").mask('000.000.000.000.000,00', {reverse: true});
		})

		
	
		
		
		$("#pagamento").prop('disabled',true).val();
		
		var btnComprovante = $("#comprovante").val();
		var btnBoleto = $("#boleto").val();

			
	
	if(boleto){
		$("#boleto").hide();
	}
	
	  
	
	if(comprovante) {
		$("#pagamento").prop('disabled',true).val(pagamento);
		$("#situacao").prop('readonly',true).val('{{$taxa->situacao}}');
		$("#comprovante").hide();
    }

	if(!comprovante){
		console.log("nao tem comprovante");
		$("#situacao option[value='pago']").remove();
		$("#pagamento").prop('disabled',true).val(null);

	}


	console.log(pagamento);
	
	
	
	$( "#comprovante" ).change(function() {
  		$("#pagamento").prop('disabled',false);

		$("#situacao").append('<option value="pago">Pago</option>'); 
  		$("#situacao").val('pago');
		$("#pagamento").attr("required", "true");
		

		$("#pagamento").prop('required',true).val(null);
	});




    
	
	
	
	$( "#removerComprovante" ).click(function() {
		alert( "Remover Comprovante" );
		
		$("#pagamento").prop('disabled',false);
		$("#situacao").prop('disabled',false);
		$("#situacao").val('aberto');
		$("#pagamento").attr("required", false);
		$("#pagamento").val(null);
		$("#comprovante").show();
		$("#btnComprovante").hide();
		$("#removerComprovante").hide();


		$.ajax({
            url: '{{url('admin/taxa/removerComprovante',$taxa->id)}}',
            method: 'GET',
            success: function(data) {

              console.log("Comprovante Removido");
            },
            })
		$("#removerComprovante").after("<p class=danger>Comprovante Removido</p>");

		$("#situacao option[value='pago']").remove();
		$("#pagamento").prop('disabled',true).val(null);

		
	});

	$( "#removerBoleto" ).click(function() {
		$("#boleto").show();
		$("#btnBoleto").hide();
		$("#removerBoleto").hide();
		$.ajax({
            url: '{{url('admin/taxa/removerBoleto',$taxa->id)}}',
            method: 'GET',
            success: function(data) {

              console.log("Boleto Removido");
            },
            })
		$("#removerBoleto").after("<p class=danger>Boleto Removido</p>");
	});


	$("#submitBtn").click(function(){

		$("#pagamento").prop('disabled',false);
		
	});

	  	
 	
});


</script>

@stop