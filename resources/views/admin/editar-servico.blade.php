@extends('adminlte::page')

@section('title', 'Editar serviço')

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
		<h1 style="margin: 0; font-weight: 700; color: #333;">Editar Serviço: {{$servico->os}}</h1>
	</div>
</div>
@stop

@section('content')
<div class="form-container">
	{!! Form::model($servico,['route'=>['servicos.update', $servico->id],'method'=>'put','enctype'=>'multipart/form-data','id'=>'editServico']) !!}

	@include('admin.partials.form-servico')

	<div style="border-top: 1px solid #f4f4f4; padding-top: 20px; margin-top: 20px; display: flex; gap: 10px;">
		<a href="{{route('servicos.show',$servico->id)}}" class="btn btn-default btn-pill"><i class="fa fa-chevron-left" style="margin-right: 5px;"></i> Voltar</a>
		<button type="submit" class="btn btn-info btn-pill"><i class="fa fa-save" style="margin-right: 5px;"></i> Salvar</button> 
	</div>
    
	{!! Form::close() !!}
</div>
@endsection



@section('js')
<script src="http://jqueryvalidation.org/files/dist/jquery.validate.js"></script>

<script>
	
	$(document).ready(function() {




  	$("#protocolo_emissao").datepicker();
  	$("#licenca_emissao").datepicker();
  	$("#licenca_validade").datepicker();
  	$("#laudo_emissao").datepicker();
	$("#dataFinal").datepicker();
	$("#dataLimiteCiclo").datepicker();

		function toggleDiasNotificacao() {
			if ($('#ativar_notificacao_renovacao').is(':checked')) {
				$('#dias_notificacao_container').show();
			} else {
				$('#dias_notificacao_container').hide();
			}
		}
		$('#ativar_notificacao_renovacao').on('change', function() {
			if ($(this).is(':checked')) {
				$('#dias_notificacao_container').slideDown();
			} else {
				$('#dias_notificacao_container').slideUp();
			}
		});
		toggleDiasNotificacao();


  		

			// var len = document.getElementById("servico_lpu").length;

			// if(len)
			// 		{
			// 		// get reference to select element
			// 		var sel = document.getElementById('servico_lpu');

			// 		// create new option element
			// 		var opt = document.createElement('option');

			// 		// create text node to add to option element (opt)
			// 		opt.appendChild( document.createTextNode('Selecione o tipo de serviço') );

			// 		// set value property of opt
			// 		opt.value = '0';

			// 		opt.selected = true; 

			// 		// add opt to end of select box (sel)
			// 		sel.appendChild(opt);
			// 		}
			
			// else
			// 		{
			// 		var sel = document.getElementById('servico_lpu');

			// 		// create new option element
			// 		var opt = document.createElement('option');

			// 		// create text node to add to option element (opt)
			// 		opt.appendChild( document.createTextNode('Essa empresa não possui LPU') );
			// 		sel.disabled = true;
			// 		opt.selected = true;
			// 		opt.value = '0';
			// 		sel.appendChild(opt);

			// 		}

		if(document.getElementById('tipoLicenca').value == 'n/a')
		{
			document.getElementById('licenca_emissao').disabled = true;
			document.getElementById('licenca_validade').disabled = true;
		}

		if(document.getElementById('tipoLicenca').value == 'definitiva')
		{
			document.getElementById('licenca_validade').disabled = true;
			document.getElementById('licenca_emissao').disabled = false;
			document.getElementById('licenca_validade').value = '31/12/2050';
		}



		document.getElementById('tipoLicenca').onchange = function()
		{

			

			switch(document.getElementById('tipoLicenca').value)
			{
				case 'definitiva':
					document.getElementById('licenca_validade').disabled = true;
					document.getElementById('licenca_emissao').disabled = false;
					document.getElementById('licenca_validade').value = '31/12/2050';

				break;

				case 'n/a':

					document.getElementById('licenca_emissao').disabled = true;
					document.getElementById('licenca_validade').disabled = true;

					document.getElementById('licenca_validade').value = '';
					document.getElementById('licenca_emissao').value = '';
					

				break;

				case 'renovavel':
					document.getElementById('licenca_validade').value = '';
					document.getElementById('licenca_emissao').value = '';
					document.getElementById('licenca_emissao').disabled = false;
					document.getElementById('licenca_validade').disabled = false;
				break;
			}
		};
		


		



// document.getElementById('servico_lpu').onchange = function() {
// var selem = document.getElementById('servico_lpu'); 
// document.getElementById('nome').value = selem.options[selem.selectedIndex].text;
// }



 
});

var protocolo = "{{$servico->protocolo_anexo}}";
var laudo = "{{$servico->laudo_anexo}}";
var licenca = "{{$servico->licenca_anexo}}";



if(protocolo){
		$("#protocolo_anexo").hide();
	}
	if(laudo){
		$("#laudo_anexo").hide();
	}

	if(licenca){
		$("#licenca_anexo").hide();
	}




$( "#removerProtocolo" ).click(function() {
		
		$("#protocolo_anexo").show();
		$("#btnProtocolo").hide();
		$("#removerProtocolo").hide();
		
		$.ajax({
            url: '{{url('admin/servico/removerProtocolo',$servico->id)}}',
            method: 'GET',
            success: function(data) {

              console.log("Protocolo Removido");
            },
            })
		$("#removerProtocolo").after("<p class=danger>Protocolo removido</p>");

		
	});

	$( "#removerLaudo" ).click(function() {
		$("#laudo_anexo").show();
		$("#btnLaudo").hide();
		$("#removerLaudo").hide();
		
		$.ajax({
            url: '{{url('admin/servico/removerLaudo',$servico->id)}}',
            method: 'GET',
            success: function(data) {

              console.log("Laudo Removido");
            },
            })
		$("#removerLaudo").after("<p class=danger>Laudo removido</p>");

		
	});

	$( "#removerLicenca" ).click(function() {
		$("#licenca_anexo").show();
		$("#btnLicenca").hide();
		$("#removerLicenca").hide();
		
		$.ajax({
            url: '{{url('admin/servico/removerLicenca',$servico->id)}}',
            method: 'GET',
            success: function(data) {

              console.log("Licenca Removido");
            },
            })
		$("#removerLicenca").after("<p class=danger>Licenca removida</p>");

		
	});


	var user_id = {{Auth::id()}};

		if(user_id > 4)
		{
		

		if(document.getElementById('situacao').value == 'finalizado')
		{
			document.getElementById('situacao').disabled = true;
				$(document).on('submit','form',function(){
					document.getElementById('situacao').disabled = false;
				});
		}
		
		}

		var validator = $("#editServico").validate({
    rules: { 
        valorTotal: {
            required:true,              
            number: true,
            notEqual: '0'
        }
    }, 
    messages: { 
        valorTotal: {
            required: "Insira um valor válido",               
            number:"Please enter numbers only",
            notEqual:"Valor total não pode ser 0"
        }
    },
    submitHandler: function() {  
        form.submit();
    }
});
jQuery.validator.addMethod("notEqual", function (value, element, param) { // Adding rules for Amount(Not equal to zero)
    return this.optional(element) || value != '0';
});


	if($("#valorTotal").val() == 0)
	{	
		


	}


	$('#valorTotal').keyup(function() {
    $('#valorAberto').val($(this).val());
});

var licenciamento = "{{$licenciamento}}";

lic = licenciamento.toLowerCase();

$('#licenciamento').val(lic);


</script>



@stop