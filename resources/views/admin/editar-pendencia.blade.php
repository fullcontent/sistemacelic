@extends('adminlte::page')



@section('content')

	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Editar pendência</h3>
	</div>

	

{!! Form::model($pendencia,['route'=>['pendencia.update', $pendencia->id],'method'=>'put','enctype'=>'multipart/form-data']) !!}

	@include('admin.partials.form-pendencia')
	
	

      			<div class="box-footer">
      			<a href="{{route('servicos.show',$pendencia->servico_id)}}" class="btn btn-default">Voltar</a>
                
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> SALVAR</button>
              	</div>
    	
    
	
	{!! Form::close() !!}

@endsection

@section('js')

<script>
	
	$(document).ready(function() {

  	$("#emissao").datepicker();
  	$("#vencimento").datepicker();
  	 	
  	$("#valor").mask('000.000.000.000.000,00', {reverse: true});
	  	
 
});


</script>

@stop