@extends('adminlte::page')



@section('content')

	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Cadastrar taxa</h3>
	</div>

	

	{!! Form::open(['route'=>'taxas.store']) !!}

	@include('admin.partials.form-taxa')
	
	

      			<div class="box-footer">
      			<a href="#" class="btn btn-default">Voltar</a>
                
                <button type="submit" class="btn btn-info">Cadastrar</button>
              	</div>
    	
    
	
	{!! Form::close() !!}

@endsection