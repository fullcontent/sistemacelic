@extends('adminlte::page')



@section('content')
		
	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Editar Ordem de Compra</h3>
	</div>

	
	
	{!! Form::model($ordemCompra,['route'=>['ordemCompra.update', $ordemCompra->id],'method'=>'PUT']) !!}

	@include('admin.ordemCompra.form-ordemCompra')

				<div class="box-footer">
                <button type="submit" class="btn btn-default">Voltar</button>
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button>
				
              	</div>
    	
    
	{!! Form::close() !!}

@endsection