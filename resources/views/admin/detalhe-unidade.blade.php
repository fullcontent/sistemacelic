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
    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','licencaOperacao')))
            @include('admin.components.widget-licencasOperacao')
        @endif
    
    </div>
    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','nRenovaveis')))
            @include('admin.components.widget-naoRenovaveis')
        @endif
       
        
    </div>

    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','controleCertidoes')))
            @include('admin.components.widget-controleCertidoes')
        @endif
       
        
    </div>

    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','controleTaxas')))
            @include('admin.components.widget-controleTaxas')
        @endif
       
        
    </div>
    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','facilitiesRealEstate')))
            @include('admin.components.widget-facilities')
        @endif
       
        
    </div>


    <div class="col-md-6">
        @if(count($dados->arquivos))
            @include('admin.components.widget-arquivos')
        @endif
    </div>

     
</div>
@endsection