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
                <button type="submit" class="btn btn-info">Cadastrar</button>
              	</div>
    
	{!! Form::close() !!}

@endsection


@section('js')

<script>
	
	$(document).ready(function() {

  	$("#protocolo_emissao").datepicker();
  	$("#licenca_emissao").datepicker();
  	$("#licenca_validade").datepicker();
  	$("#laudo_emissao").datepicker();

  	$("#os").val("{!! $os !!}");  	
	


			var len = document.getElementById("servico_lpu").length;

			if(len)
					{
						// get reference to select element
					var sel = document.getElementById('servico_lpu');

					// create new option element
					var opt = document.createElement('option');

					// create text node to add to option element (opt)
					opt.appendChild( document.createTextNode('Selecione o tipo de serviço') );

					// set value property of opt
					opt.value = '0';

					opt.selected = true; 

					// add opt to end of select box (sel)
					sel.appendChild(opt);
					}
			else
					{
					var sel = document.getElementById('servico_lpu');

					// create new option element
					var opt = document.createElement('option');

					// create text node to add to option element (opt)
					opt.appendChild( document.createTextNode('Essa empresa não possui LPU') );
					sel.disabled = true;
					opt.selected = true;
					opt.value = '0';
					sel.appendChild(opt);

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
		

document.getElementById('servico_lpu').onchange = function() {
var selem = document.getElementById('servico_lpu'); 
document.getElementById('nome').value = selem.options[selem.selectedIndex].text;
}



 
});


</script>

@stop