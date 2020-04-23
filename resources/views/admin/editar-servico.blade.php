@extends('adminlte::page')



@section('content')



	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Editar serviço</h3>
	</div>

	

	{!! Form::model($servico,['route'=>['servicos.update', $servico->id],'method'=>'put','enctype'=>'multipart/form-data']) !!}


	@include('admin.partials.form-servico')

				<div class="box-footer">
                <a href="{{route('servicos.show',$servico->id)}}" class="btn btn-default">Voltar</a>
                <button type="submit" class="btn btn-info">EDITAR</button>
              	</div>
    
	{!! Form::close() !!}

@endsection


@section('js')

<script>
	
$(document).ready(function() {

  	$("#protocolo_emissao").datepicker();
  	$("#licenca_emissao").datepicker();
  	$("#licenca_vencimento").datepicker();
  	$("#laudo_emissao").datepicker();

document.getElementById('servico_lpu').onchange = function() {
var selem = document.getElementById('servico_lpu'); 
document.getElementById('nome').value = selem.options[selem.selectedIndex].text;
}


 
});
</script>

@stop