@extends('adminlte::page')



@section('content')

	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Cadastrar pendência</h3>
	</div>

	

	{!! Form::open(['route'=>'pendencia.store']) !!}

	@include('admin.partials.form-pendencia')
	
	

      			<div class="box-footer">
      			            
                <button type="submit" class="btn btn-info">Cadastrar</button>
              	</div>
    	
    
	
	{!! Form::close() !!}

@endsection

