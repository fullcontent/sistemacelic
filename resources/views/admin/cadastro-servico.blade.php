@extends('adminlte::page')

@section('title', 'Cadastrar serviço')

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
		<h1 style="margin: 0; font-weight: 700; color: #333;">Cadastrar Serviço</h1>
	</div>
</div>
@stop

@section('content')
<div class="form-container">
	{!! Form::open(['route'=>'servicos.store','id'=>'cadastroServico','enctype'=>'multipart/form-data']) !!}

	@include('admin.partials.form-servico')

	<div style="border-top: 1px solid #f4f4f4; padding-top: 20px; margin-top: 20px; display: flex; gap: 10px;">
		<a href="{{route('servicos.index')}}" class="btn btn-default btn-pill">Voltar</a>
		<button type="submit" class="btn btn-info btn-pill"><i class="fa fa-save" style="margin-right: 5px;"></i> Salvar</button>
	</div>
    
	{!! Form::close() !!}
</div>
@endsection



@section('js')

<script>
	
	$(document).ready(function() {

		$("#solicitante").select2({
            placeholder: 'Quem é o solicitante?',
            allowClear: true,
        });

        $("#solicitante").val('').trigger('change');




  	$("#protocolo_emissao").datepicker();
  	$("#licenca_emissao").datepicker();
  	$("#licenca_validade").datepicker();
  	$("#laudo_emissao").datepicker();

	$("#dataFinal").datepicker();
	$("#dataLimiteCiclo").datepicker();

  	$("#os").val("{!! $os !!}");  	
				
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

		var tipo = document.getElementById('tipo');

		var listaServicos = "<select name='nome' class='form-control' id='nome'>";
			
				listaServicos += "<option>" + "AVCB" + "</option>";
				listaServicos += "<option>" + "Alvará Sanitário" + "</option>";
				listaServicos += "<option>" + "Alvará de Funcionamento" + "</option>";
				listaServicos += "<option>" + "Alvará de Publicidade" + "</option>";
				listaServicos += "<option>" + "Alvará da Polícia Civil" + "</option>";
				listaServicos += "<option>" + "AMLURB" + "</option>";
				listaServicos += "<option>" + "CREFITO" + "</option>";
				listaServicos += "<option>" + "Licença Ambiental" + "</option>";
				listaServicos += "<option>" + "Licença de Elevador" + "</option>";
				listaServicos += "</select>";

		if(tipo.value == 'licencaOperacao')
		{
			$('#nome').replaceWith(listaServicos);
		}







		document.getElementById('tipo').onchange = function()
		{

			if(document.getElementById('tipo').value == 'licencaOperacao'){

				$('#nome').replaceWith(listaServicos);
			
			}
			if(document.getElementById('tipo').value != 'licencaOperacao'){

				$('#nome').replaceWith('<input type="text" name="nome" id="nome" class="form-control">');

			}

			
		};



			
		 
});





</script>

@stop