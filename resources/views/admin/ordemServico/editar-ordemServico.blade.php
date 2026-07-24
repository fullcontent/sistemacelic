@extends('adminlte::page')

@section('css')
<style>
	.dashboard-card {
		border-radius: 8px;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
		padding: 20px;
		margin-bottom: 25px;
		background-color: #fff;
		border-top: 3px solid #3c8dbc;
	}
	.dashboard-card-title {
		font-size: 1.2em;
		font-weight: 600;
		color: #2c3e50;
		margin-bottom: 20px;
		padding-bottom: 10px;
		border-bottom: 1px solid #f4f4f4;
	}
	.form-control {
		border-radius: 6px;
	}
	.btn {
		border-radius: 50px;
	}
	.page-header-title {
		font-weight: 700;
		color: #333;
		margin-bottom: 20px;
	}
</style>
@stop



@section('content')
		
<div class="row">
    <div class="col-md-12">
        <h2 class="page-header-title">Editar Ordem de Serviço #{{ $ordemServico->id }}</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">

	
	
	{!! Form::model($ordemServico,['route'=>['ordemServico.update', $ordemServico->id],'method'=>'PUT', 'enctype'=>'multipart/form-data']) !!}

	@include('admin.ordemServico.form-ordemServico')

				<div style="text-align: right; margin-top: 10px; margin-bottom: 30px;">
                <a href="{{route('ordemServico.index')}}" class="btn btn-default" style="padding: 8px 25px;"><i class="fa fa-arrow-left"></i> Voltar</a>
                <button type="submit" class="btn btn-primary" style="padding: 8px 25px;"><i class="fa fa-save"></i> Salvar Alterações</button>
				
              	</div>
    	
    
	{!! Form::close() !!}
    </div>
</div>

@endsection