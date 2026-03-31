@extends('adminlte::page')



@section('content')

<div class="row">
    <div class="col-md-12">
	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Nova ordem de serviço</h3>
	</div>

	

	{!! Form::open(['route'=>'ordemServico.store','enctype'=>'multipart/form-data']) !!}

	@include('admin.ordemServico.form-ordemServico')
	
      			<div class="box-footer">
      			<a href="{{route('ordemServico.index')}}" class="btn btn-default">Voltar</a>
                
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button>
              	</div>
    	
    
	
	{!! Form::close() !!}
    </div>
    </div>
</div>

@endsection