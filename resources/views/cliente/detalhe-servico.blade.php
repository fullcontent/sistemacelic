@extends('adminlte::page')

@section('content_header')
    <h1></h1>
@stop



@section('content')


<div class="row">
    <div class="col-md-12">
        
        @include('cliente.components.widget-detalhes')
        
    </div>
</div>


 
<div class="col-md-8">
    
    <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Detalhes do serviço {{$servico->os}}</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                
                <div class="col-sm-6">
                  <p><b>Ordem de serviço: </b>{{$servico->os}}</p>
                  <p><b>Nome: </b>{{$servico->nome}}</p>
                  <p><b>Responsável: </b>{{$servico->responsavel->name}}</p>
                  <p><b>Início do processo: </b>{{\Carbon\Carbon::parse($servico->created_at)->format('d/m/Y')}}</p>
                  <p><b>Emissão Protocolo: </b>{{\Carbon\Carbon::parse($servico->protocolo_emissao)->format('d/m/Y')}}</p>
                  <p><b>Número Protocolo: </b>{{$servico->protocolo_numero}} <button type="button" class="btn btn-primary btn-xs">Ver</button></p>
                  

                </div>
                
                <div class="col-sm-6">
                    
                  <p><b>Emissão Licença: </b>{{\Carbon\Carbon::parse($servico->licenca_emissao)->format('d/m/Y')}}</p>
                  <p><b>Emissão Validade: </b>{{\Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</p>
                  <p><b>Visualizar Documento: </b><button type="button" class="btn btn-primary btn-xs">Ver</button></p>

                </div>

              
            </div>
            <!-- /.box-body -->
           
          </div>
</div>

@include('cliente.components.widget-interacoes')

@endsection

