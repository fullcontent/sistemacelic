@extends('adminlte::page')

@section('content_header')
<h1>Editar unidade {{$unidade->nomeFantasia}}</h1>
@stop


@section('content')
@if(Session::has('errors'))
@foreach($errors->all() as $error)

<div class="alert alert-danger alert-dismissible">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	<h4><i class="icon fa fa-ban"></i> Aviso!</h4>
	{!! $error !!}
</div>
@endforeach
@endif

<div class="box box-primary">
	
	
	
	{!! Form::model($unidade,['route'=>['unidade.editar', $unidade->id]]) !!}
	@include('admin.partials.form-unidade')
	<div class="box-footer">
		<a href="{{route('unidades.index')}}" class="btn btn-default">Voltar</a>
		<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Salvar</button>
	</div>
	
	
	{!! Form::close() !!}
	@endsection