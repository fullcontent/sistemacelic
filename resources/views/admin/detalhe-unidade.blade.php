@extends('adminlte::page')
@section('content_header')
<h1>Detalhes da unidade</h1>
@stop
@section('content')
@if (session()->has('success'))

<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-check"></i> Sucesso!</h4>
    {{ session('success') }}
</div>
@endif
<div class="row">
    <div class="col-md-12">
        
        @include('admin.components.widget-detalhes')
        
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        
        @include('admin.components.widget-servicos')
        @include('admin.components.widget-servicos-secundarios')
        
    </div>
    <div class="col-md-7">
        
        @include('admin.components.widget-taxas')
        
    </div>

     <div class="col-md-7">
        
        @include('admin.components.widget-arquivos')
        
    </div>
</div>
@endsection