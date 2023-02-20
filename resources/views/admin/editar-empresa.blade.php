@extends('adminlte::page')



@section('content')
		
	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Editar empresa</h3>
	</div>

	
	
	{!! Form::model($empresa,['route'=>['empresa.editar', $empresa->id]]) !!}

	@include('admin.partials.form-empresa')

				<div class="box-footer">
                <button type="submit" class="btn btn-default">Voltar</button>
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button>
              	</div>
    	
    
	{!! Form::close() !!}

@endsection