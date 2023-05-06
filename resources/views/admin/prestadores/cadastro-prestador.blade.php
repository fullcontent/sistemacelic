@extends('adminlte::page')



@section('content')

	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Cadastrar prestador</h3>
	</div>

	

	{!! Form::open(['route'=>'prestador.store']) !!}

	@include('admin.prestadores.form-prestador')
	
      			<div class="box-footer">
      			<a href="{{route('prestador.index')}}" class="btn btn-default">Voltar</a>
                
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button>
              	</div>
    	
    
	
	{!! Form::close() !!}

@endsection