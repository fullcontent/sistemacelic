@extends('adminlte::page')


@section('content')

		<h1 class="text-center">Você não tem permissão para acessar essa página.</h1>
		<a href="{{url()->previous()}}" class="btn btn-flat btn-info">Voltar</a>


@endsection