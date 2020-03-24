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



<div class="col-md-12">
    
    
   

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
                  
                  @unless ( empty($servico->protocolo_numero) )
                    
                    <p><b>Emissão Protocolo: </b>{{\Carbon\Carbon::parse($servico->protocolo_emissao)->format('d/m/Y')}}</p>
                  <p><b>Número Protocolo: </b>{{$servico->protocolo_numero}}

                    @unless (empty($servico->protocolo_anexo))
                    <a href="{{ url("uploads/$servico->protocolo_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver Protocolo</a>
                    @endunless
          

                  @endunless
                  <p><b>Observações: </b>{{$servico->observacoes}}</p>

                </div>
                
                <div class="col-sm-6">
                  
                  @unless ( empty($servico->licenca_anexo) )  
                  <p><b>Emissão da Licença: </b>{{\Carbon\Carbon::parse($servico->licenca_emissao)->format('d/m/Y')}}</p>
                  <p><b>Validade da Licença </b>{{\Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</p>
                  <p><b>Emissão Documento: </b> <a href="{{ url("uploads/$servico->licenca_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver Licença</a></p>
                  @endunless

                  @unless ( empty($servico->laudo_anexo) )  
                  <p><b>Emissão do Laudo: </b>{{\Carbon\Carbon::parse($servico->laudo_emissao)->format('d/m/Y')}}</p>
                  <p><b>N. do Laudo </b> {{$servico->laudo_numero }}</p>
                  <p><b>Laudo: </b> <a href="{{ url("uploads/$servico->laudo_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver Laudo</a></p>
                  @endunless

                </div>

              <a href="{{route('servicos.edit', $servico->id)}}" class="btn btn-info pull-right"><span class="glyphicon glyphicon-pencil"></span> Editar</a>


            </div>
            <!-- /.box-body -->
           
          </div>
</div>

<div class="col-md-7">
  @include('admin.components.widget-taxas')
</div>

<div class="col-md-5">
  @include('admin.components.widget-pendencias')
</div>

<div class="row">
    <div class="col-sm-12">
      <div class="box box-black">
            <div class="box-header with-border">
              <h3 class="box-title">Últimas Interações</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            
             <ul class="timeline timeline-inverse">
                                 
                  
                @foreach($servico->historico as $historico)
                  <!-- timeline item -->
                  <li>
                    
                    <i class="fa fa-user bg-aqua"></i>
                    
                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock"></i> {{\Carbon\Carbon::parse($historico->created_at)->format('d/m/Y H:m')}}</span>
                      <h3 class="timeline-header"><a href="#">{{$historico->user->name}}</a> {{$historico->observacoes}}</h3>

                      
                    </div>
                  </li>
                  <!-- END timeline item -->
                @endforeach
                  
                  
                  <li>
                   <i class="fa fa-clock bg-gray"></i>
                   <div class="timeline-item">
                      <a href="{{route('interacoes.lista',$servico->id)}}" class="btn btn-flat">Visualizar todas as interações</a>

                    </div>
                  </li>
                </ul>

                <div class="box-footer">
                
                <div class="box-header">
                  
                  {!! Form::open(['route'=>'interacao.store']) !!}
                  <div class="input-group">
                  {!! Form::text('observacoes', null, ['class'=>'form-control','id'=>'observacoes','placeholder'=>'Digite a mensagem']) !!}
                  {!! Form::hidden('servico_id',$servico->id) !!}
                  
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-info btn-flat">Enviar</button>
                      </span>
                </div>
                {!! Form::close() !!}   

                </div>

            </div>


                 
  </div>


  </div> 

</div>

@endsection

