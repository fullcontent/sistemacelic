@extends('adminlte::page')



@section('content')

	<div class="box box-primary">
	
	<div class="box-header with-border">
		<h3 class="box-title">Editar pendência</h3>
	</div>

	

{!! Form::model($pendencia,['route'=>['pendencia.update', $pendencia->id],'method'=>'put','enctype'=>'multipart/form-data']) !!}

	@include('admin.partials.form-pendencia')
	

	<div class="col-md-12">
		<h3>Arquivos</h3>
		<table class="table table-bordered">
			<thead>
			  <tr>
				<td>Arquivo</td>
				<td>Cadastrado por:</td>
				<td>Download</td>
			  </tr>
			  <!-- No data rows here -->
			</thead>
			<tbody>
				@foreach($arquivos as $a)
				<tr>
					<td>{{$a->nome}}</td>
					<td>{{$a->user->name}}</td>
					<td><a href="{{$a->arquivo}}" class="btn btn-xs btn-success">Download</a></td>
				</tr>
				@endforeach
			</tbody>
		  </table>

	  </div>
	

      			<div class="box-footer">
      			<a href="{{route('servicos.show',$pendencia->servico_id)}}" class="btn btn-default">Voltar</a>
                
                <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button>
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