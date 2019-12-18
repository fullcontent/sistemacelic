@extends('adminlte::page')



@section('content')



	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Editar serviço</h3>
	</div>

	

	{!! Form::model($servico,['route'=>['servicos.update', $servico->id],'method'=>'put','enctype'=>'multipart/form-data']) !!}


	@include('admin.partials.form-servico')

				<div class="box-footer">
                <a href="{{route('servicos.index')}}" class="btn btn-default">Voltar</a>
                <button type="submit" class="btn btn-info">EDITAR</button>
              	</div>
    
	{!! Form::close() !!}

@endsection


@section('js')

<script>
	
	$(document).ready(function() {

  	$("#protocolo_emissao").datepicker();
  	$("#protocolo_validade").datepicker();
  	

	  	
 
});
</script>

@stop