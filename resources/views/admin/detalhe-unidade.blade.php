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

        @else
        <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7),'tipoServico'=>'licencaOperacao'])}}" class="btn btn-sm btn-info btn-flat pull-left"><span class="glyphicon glyphicon-plus-sign"></span> Nova Licença de Operação</a>
        @endif
    
    </div>
    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','nRenovaveis')))
            @include('admin.components.widget-naoRenovaveis')
            @else
        <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7),'tipoServico'=>'nRenovavel'])}}" class="btn btn-sm btn-info btn-flat pull-left"><span class="glyphicon glyphicon-plus-sign"></span> Novo Projeto/Licença Não Renovável</a>
        @endif
       
        
    </div>

    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','controleCertidoes')))
            @include('admin.components.widget-controleCertidoes')
            @else
        <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7),'tipoServico'=>'controleCertidoes'])}}" class="btn btn-sm btn-info btn-flat pull-left"><span class="glyphicon glyphicon-plus-sign"></span> Nova Certidão</a>
        @endif
       
        
    </div>

    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','controleTaxas')))
            @include('admin.components.widget-controleTaxas')
            @else
        <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7),'tipoServico'=>'controleTaxa'])}}" class="btn btn-sm btn-info btn-flat pull-left"><span class="glyphicon glyphicon-plus-sign"></span> Nova Taxa</a>
        @endif
       
        
    </div>
    <div class="col-md-6">
        
        @if(count($servicos->where('tipo','facilitiesRealEstate')))
            @include('admin.components.widget-facilities')

            @else
            <a href="{{route('servicos.create', ['id'=>$dados->id,'t'=>substr($route, 0,7),'tipoServico'=>'facilitiesRealEstate'])}}" class="btn btn-sm btn-info btn-flat pull-left"><span class="glyphicon glyphicon-plus-sign"></span> Nova Facilities/Real Estate</a>
        @endif
       
        
    </div>


    <div class="col-md-6">
        
            @include('admin.components.widget-arquivos')
        
    </div>

     
</div>
@endsection