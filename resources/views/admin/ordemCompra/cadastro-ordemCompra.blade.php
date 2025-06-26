@extends('adminlte::page')



@section('content')

	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Nova ordem de compra</h3>
	</div>

	

	{!! Form::open(['route'=>'ordemCompra.store','enctype'=>'multipart/form-data']) !!}

	@include('admin.ordemCompra.form-ordemCompra')
	
      			<div class="box-footer">
      			<a href="{{route('ordemCompra.index')}}" class="btn btn-default">Voltar</a>
                
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button>
              	</div>
    	
    
	
	{!! Form::close() !!}

@endsection