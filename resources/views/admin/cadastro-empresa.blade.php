@extends('adminlte::page')



@section('content')

	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Cadastrar empresa</h3>
	</div>

	

	{!! Form::open(['route'=>'empresas.store']) !!}

	@include('admin.partials.form-empresa')
	
	

      			<div class="box-footer">
      			<a href="{{route('empresas.index')}}" class="btn btn-default">Voltar</a>
                
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> SALVAR</button>
              	</div>
    	
    
	
	{!! Form::close() !!}

@endsection