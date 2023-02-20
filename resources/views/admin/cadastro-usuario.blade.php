@extends('adminlte::page')


@section('content')
	

	<div class="box box-primary">
		
		<div class="box-header with-border">
		<h3 class="box-title">Cadastrar usuário</h3>
	</div>
	
	{!! Form::open(['route'=>'usuario.store']) !!}

	@include('admin.partials.form-usuario')

	<div class="box-footer">
                <a href="{{route('usuarios.index')}}" class="btn btn-default">Voltar</a>
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button>
              	</div>

	{!! Form::close() !!}

		
	</div>
	

@stop

@section('js')

<script>
	$(document).ready(function() {

  	$("#empresas_user_access").select2();
  	$("#unidades_user_access").select2();
  	document.getElementById("password").classList.add("form-control");

	  	
 
});
</script>

@stop