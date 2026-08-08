@extends('adminlte::page')

@section('title', 'Editar pendência')

@section('css')
<style>
	.form-container {
		background: #fff;
		border-radius: 8px;
		padding: 25px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
		border: 1px solid #ebf0f5;
		margin-bottom: 25px;
	}

	.btn-pill {
		border-radius: 50px;
		padding: 6px 20px;
		font-weight: 600;
		transition: all 0.2s;
	}

	.btn-pill:hover {
		transform: translateY(-1px);
		box-shadow: 0 2px 5px rgba(0,0,0,0.1);
	}

	/* Scope controls inside form container */
	.form-container .form-control {
		border-radius: 6px;
		border: 1px solid #d2d6de;
		box-shadow: none;
		transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
	}

	.form-container .form-control:focus {
		border-color: #3c8dbc;
		box-shadow: none;
	}

	.form-container label {
		font-weight: 600;
		color: #555;
		font-size: 0.95em;
		margin-bottom: 6px;
	}

	.form-container .box-body {
		padding: 0;
	}

	.content-header .breadcrumb { display: none !important; }
</style>
@stop

@section('content_header')
<div class="row" style="margin-bottom: 15px;">
	<div class="col-sm-12">
		<h1 style="margin: 0; font-weight: 700; color: #333;">Editar Pendência</h1>
	</div>
</div>
@stop

@section('content')
<div class="form-container">
	{!! Form::model($pendencia,['route'=>['pendencia.update', $pendencia->id],'method'=>'put','enctype'=>'multipart/form-data']) !!}

	@include('admin.partials.form-pendencia')

	<div style="border-top: 1px solid #f4f4f4; padding-top: 20px; margin-top: 20px; display: flex; gap: 10px;">
		<a href="{{route('servicos.show',$pendencia->servico_id)}}" class="btn btn-default btn-pill">Voltar</a>
		<button type="submit" class="btn btn-info btn-pill"><i class="fa fa-save" style="margin-right: 5px;"></i> Salvar</button>
	</div>

	{!! Form::close() !!}
</div>
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