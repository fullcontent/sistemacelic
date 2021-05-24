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
		$("#pagamento").prop('disabled',true).val(null);

  	
		var comprovante = "{{$taxa->comprovante}}";
		var pagamento = "{{$taxa->pagamento}}";
		var boleto = "{{$taxa->boleto}}";
		
		var btnComprovante = $("#comprovante").val();
		var btnBoleto = $("#boleto").val();

	
	
	if(boleto){
		$("#boleto").hide();
	}
	
	  
	
	if(comprovante) {
		$("#pagamento").prop('disabled',true).val(pagamento);
		$("#situacao").prop('disabled',true).val('{{$taxa->situacao}}');
		$("#comprovante").hide();
    }
	
	
	$( "#comprovante" ).change(function() {
  		$("#pagamento").prop('disabled',false);
  		$("#situacao").val('pago');
		$("#pagamento").attr("required", "true");
		var myDate = new Date();
		var prettyDate =(myDate.getDate()+1) + '/' + myDate.getMonth() + '/' + myDate.getFullYear();

		$("#pagamento").val(prettyDate).datepicker("setDate", myDate);
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
	  	
 	
});


</script>

@stop