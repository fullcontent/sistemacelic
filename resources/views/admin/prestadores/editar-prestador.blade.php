@extends('adminlte::page')



@section('content')
		
	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Editar prestador</h3>
	</div>

	
	
	{!! Form::model($prestador,['route'=>['prestador.update', $prestador->id],'method'=>'PUT']) !!}

	@include('admin.prestadores.form-prestador')

				<div class="box-footer">
                <button type="submit" class="btn btn-default">Voltar</button>
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button>
				
              	</div>
    	
    
	{!! Form::close() !!}

@endsection