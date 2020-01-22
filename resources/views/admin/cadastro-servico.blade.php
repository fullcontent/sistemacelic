@extends('adminlte::page')



@section('content')



	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Cadastrar serviço</h3>
	</div>

	

	{!! Form::open(['route'=>'servicos.store']) !!}


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
  	$("#licenca_vencimento").datepicker();

  	
  	
	  	
 
});


</script>

@stop