@extends('adminlte::page')
@section('content')
@if(Session::has('errors'))
@foreach($errors->all() as $error)

<div class="alert alert-danger alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	<h4><i class="icon fa fa-ban"></i> Aviso!</h4>
	{!! $error !!}
</div>
@endforeach
@endif

<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Editar usuário</h3>
	</div>
	
	@if(auth()->user()->privileges == 'admin')
	
	{!! Form::model($usuario,['route'=>['usuario.update', $usuario->id]]) !!}

	@elseif(auth()->user()->privileges == 'cliente')
	{!! Form::model($usuario,['route'=>['cliente.usuario.update']]) !!}
	@endif
	
	

	@include('admin.partials.form-usuario')
	<div class="box-footer">
		<a href="{{route('usuarios.index')}}" class="btn btn-default">Voltar</a>
		<button type="submit" class="btn btn-info">Editar</button>
	</div>
	
	
	{!! Form::close() !!}
	@endsection

@section('js')

<script>
	$(document).ready(function() {

  	$("#empresas_user_access").select2();
  	$("#unidades_user_access").select2();
  	document.getElementById("password").classList.add("form-control");

	
	var user_access = {!! json_encode($user_access->toArray()) !!};

	
	$.each(user_access, function (index, value) {

       	if(value.empresa) {

       	var newOption = new Option(value.empresa.nomeFantasia, value.empresa_id, false, true);
		$('#empresas_user_access').append(newOption).trigger('change');
       	}
       	if(value.unidade){
       	var newOption = new Option(value.unidade.nomeFantasia, value.unidade_id, false, true);
		$('#unidades_user_access').append(newOption).trigger('change');
       	}

   		
    });

	
	
	

	  	
 
});
</script>

@stop