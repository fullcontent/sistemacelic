@extends('adminlte::page')



@section('content')

	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Cadastrar empresa</h3>
	</div>

	

	{!! Form::open(['route'=>'empresas.store']) !!}

	@include('admin.partials.form-empresa')
	
	

      			<div class="box-footer">
                <button type="submit" class="btn btn-default">Voltar</button>
                <button type="submit" class="btn btn-info">Cadastrar</button>
              	</div>
    	
    
	
	{!! Form::close() !!}

@endsection