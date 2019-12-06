@extends('adminlte::page')



@section('content')
		
	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Editar empresa</h3>
	</div>

	
	
	{!! Form::model($empresa,['route'=>['empresas.update', $empresa->id]]) !!}

	@include('admin.partials.form-empresa')

				<div class="box-footer">
                <button type="submit" class="btn btn-default">Voltar</button>
                <button type="submit" class="btn btn-info">Editar</button>
              	</div>
    	
    
	{!! Form::close() !!}

@endsection