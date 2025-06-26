@extends('adminlte::page')



@section('content')



	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Cadastrar serviço</h3>
	</div>

	

	{!! Form::open(['route'=>'servicos.store','id'=>'cadastroServico','enctype'=>'multipart/form-data']) !!}


	@include('admin.partials.form-servico')

				<div class="box-footer">
                <a href="{{route('servicos.index')}}" class="btn btn-default">Voltar</a>
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button>
              	</div>
    
	{!! Form::close() !!}

@endsection


@section('js')

<script>
	
	$(document).ready(function() {

		$("#solicitante").select2({
            placeholder: 'Quem é o solicitante?',
            allowClear: true,
        });

        $("#solicitante").val('').trigger('change');

		
		
		$("#corresponsavel").select2({
            placeholder: 'Algum co-responsável?',
            allowClear: true,
        });

        $("#corresponsavel").val('').trigger('change');


		$("#responsavel").select2({
            placeholder: 'Quem é o responsável?',
            allowClear: true,
        });

		$("#responsavel").val('').trigger('change');

		
		$("#cadastroServico").on("submit", function(){

			var responsavel_id = $("#responsavel").val();
			var corresponsavel_id = $("#corresponsavel").val();


			if(responsavel_id == corresponsavel_id)
			{	
				alert("Co-Responsável não pode ser igual ao responsável!");
				$( "#corresponsavel" ).focus();
				return false;
			}
			else
			{
				return true;
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

  	$("#os").val("{!! $os !!}");  	
				

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