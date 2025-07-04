@extends('adminlte::page')
@section('content_header')
<h1>Editar serviço {{$servico->os}}</h1>
@stop


@section('content')



	<div class="box box-primary">
	
	
	{!! Form::model($servico,['route'=>['servicos.update', $servico->id],'method'=>'put','enctype'=>'multipart/form-data','id'=>'editServico']) !!}


	@include('admin.partials.form-servico')

				<div class="box-footer">
                <a href="{{route('servicos.show',$servico->id)}}" class="btn btn-default"><i class="fa fa-chevron-left"></i> Voltar</a>
                <button type="" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button> 
              	</div>
    
	{!! Form::close() !!}

@endsection


@section('js')
<script src="http://jqueryvalidation.org/files/dist/jquery.validate.js"></script>

<script>
	
	$(document).ready(function() {


		$("#corresponsavel").select2({
            placeholder: 'Algum co-responsável?',
            allowClear: true,
        });

        $("#corresponsavel").val('{{$servico->coresponsavel_id}}').trigger('change');


		$("#responsavel").select2({
            placeholder: 'Quem é o responsável?',
            allowClear: true,
			sorter: function(data) {
        return data.sort();
    }
        });

		$("#responsavel").val('{{$servico->responsavel_id}}').trigger('change');

		
		$("#corresponsavel").on("change", function(){

			var responsavel_id = $("#responsavel").val();
			var corresponsavel_id = $("#corresponsavel").val();


			if(responsavel_id == corresponsavel_id)
			{
				alert("Co-Responsável não pode ser igual ao responsável!");
				$("#corresponsavel").val('').trigger('change');

			}

					
		})

		$("#analista1_id").select2({
            placeholder: 'Algum analista?',
            allowClear: true,
        });

        $("#analista1_id").val('{{$servico->analista1_id ?? ''}}').trigger('change');



		$("#analista2_id").select2({
            placeholder: 'Algum outro analista?',
            allowClear: true,
        });

        $("#analista2_id").val('{{$servico->analista2_id ?? ''}}').trigger('change');

  	$("#protocolo_emissao").datepicker();
  	$("#licenca_emissao").datepicker();
  	$("#licenca_validade").datepicker();
  	$("#laudo_emissao").datepicker();
	$("#dataFinal").datepicker();
	$("#dataLimiteCiclo").datepicker();

  		

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