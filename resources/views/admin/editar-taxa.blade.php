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
                
                <button type="submit" class="btn btn-info" id="submitBtn"><i class="fa fa-save"></i> SALVAR</button>
              	</div>
    	
    
	
	{!! Form::close() !!}

@endsection

@section('js')

<script>
	
	$(document).ready(function() {

		

  	
		var comprovante = "{{$taxa->comprovante}}";
		var pagamento = "{{$taxa->pagamento}}";
		var boleto = "{{$taxa->boleto}}";

		$("#emissao").datepicker();
		$("#vencimento").datepicker();	
		$("#pagamento").datepicker();  	 	
		$("#valor").mask('000.000.000.000.000,00', {reverse: true});
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
	
	
	$( "#comprovante" ).change(function() {
  		$("#pagamento").prop('disabled',false);
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