@extends('adminlte::page')

@section('content_header')
    <h1></h1>
@stop



@section('content')


<div class="row">
    <div class="col-md-12">
        
        @include('admin.components.widget-detalhes')
        
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
                    
                  <p><b>Emissão Licença: </b>{{$servico->licenca_emissao}}</p>
                  <p><b>Emissão Validade: </b>{{$servico->licenca_emissao}}</p>
                  <p><b>Emissão Documento: </b><button type="button" class="btn btn-primary btn-xs">Ver</button></p>

                </div>

              <a href="{{route('servicos.edit', $servico->id)}}" class="btn btn-info pull-right"><span class="glyphicon glyphicon-pencil"></span> Editar</a>


            </div>
            <!-- /.box-body -->
           
          </div>
</div>

<div class="row">
    <div class="col-sm-12">
      <div class="box box-black">
            <div class="box-header with-border">
              <h3 class="box-title">Interações</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            
            <div class="box-body">
                        
                <ul class="timeline timeline-inverse">
                                 
                  
                @foreach($servico->historico as $historico)
                  <!-- timeline item -->
                  <li>
                    
                    <i class="fa fa-user bg-aqua"></i>
                    
                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock"></i> {{\Carbon\Carbon::parse($historico->created_at)->diffForHumans()}}</span>
                      <h3 class="timeline-header"><a href="#">{{$servico->responsavel->name}}</a> {{$historico->observacoes}}</h3>

                      
                    </div>
                  </li>
                  <!-- END timeline item -->
                @endforeach
                  
                  
                  <li>
                    <i class="fa fa-clock bg-gray"></i>
                  </li>
                </ul>

            </div>

                 
  </div>


  </div> 

</div>

@endsection

