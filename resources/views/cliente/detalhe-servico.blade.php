@extends('adminlte::page')

@section('content_header')
    <h1>Detalhes do serviço</h1>
@stop



@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> Sucesso!</h4>
        {{ session('success') }}
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-warning"></i> Atenção!</h4>
        {{ session('warning') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i> Erro!</h4>
        {{ session('error') }}
    </div>
@endif
  
  
  
<div class="row">

  
    <div class="col-md-12">
        
        @include('cliente.components.widget-detalhes')
        
        
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
                  <p><b>Tipo de serviço: </b>
                    @switch($servico->tipo)
                    @case('nRenovaveis')
                    Licenças/Projetos não renováveis
                      @break
                    @case('licencaOperacao')
                      Licença de Operação
                      @break
                    @case('controleCertidoes')
                      Certidões
                      @break
                    @case('controleTaxas')
                      Taxas
                      @break
                  @case('facilitiesRealEstate')
                    Facilities/Real Estate
                      @break
                    @default
                      
                  @endswitch</p>
                  <p><b>Nome: </b>{{$servico->nome}}</p>
                  <p><b>Situação: </b>
                    @switch($servico->situacao)
                      @case('andamento')
                          @if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))
                              <button type="button" class="btn btn-xs btn-success">Andamento</button>
                          @elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'licencaOperacao'))
                              <button type="button" class="btn btn-xs btn-danger">Andamento</button>
                          @elseif($servico->tipo == 'nRenovaveis')
                              <button type="button" class="btn btn-xs btn-warning">Andamento</button>
                          @endif
                          @break
                      @case('finalizado')
                          @if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))
                              <button type="button" class="btn btn-xs btn-success">Finalizado</button>
                          @elseif(($servico->licenca_validade < date('Y-m-d'))&& ($servico->tipo == 'licencaOperacao'))
                              <button type="button" class="btn btn-xs btn-danger">Finalizado</button>
                          @elseif($servico->tipo == 'nRenovaveis')
                              <button type="button" class="btn btn-xs btn-warning">Finalizado</button>
                          @endif
                          @break
                      @case('arquivado')
                          <button type="button" class="btn btn-xs btn-default">Arquivado</button>
                          @break
                      @case('standBy')
                          <button type="button" class="btn btn-xs btn-default">Stand By</button>
                          @break
                      @case('nRenovado')
                          <button type="button" class="btn btn-xs btn-default">Não renovado</button>
                          @break
                      @case('cancelado')
                          <button type="button" class="btn btn-xs btn-danger">Cancelado</button>
                          @break
                    @endswitch
                  </p>
                  <p><b>Responsável: </b>{{$servico->responsavel->name}}</p>
                  <p><b>Início do processo: </b>{{\Carbon\Carbon::parse($servico->created_at)->format('d/m/Y')}}</p>
                  
                  @unless ( empty($servico->protocolo_numero) )
                    
                    <p><b>Emissão Protocolo: </b>{{\Carbon\Carbon::parse($servico->protocolo_emissao)->format('d/m/Y')}}</p>
                  <p><b>Número Protocolo: </b>{{$servico->protocolo_numero}}

                    @unless (empty($servico->protocolo_anexo))
                    <a href="{{ url("public/uploads/$servico->protocolo_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver Protocolo</a>
                    @endunless
          

                  @endunless
                  <p><b>Observações: </b>{{$servico->observacoes}}</p>

                </div>
                
                <div class="col-sm-6">
                  
                 

                  <p><b>Tipo da Licença: </b>
                    @switch($servico->tipoLicenca)
                        @case('renovavel')
                            Renovável
                            @break
                        @case('n/a')
                            Não Aplicada
                            @break
                        @case('definitiva')
                            Definitiva
                            @break
                        
                            
                    @endswitch
                  </p>
                  @unless ( empty($servico->licenca_anexo) ) 

                  <p><b>Emissão da Licença: </b>{{\Carbon\Carbon::parse($servico->licenca_emissao)->format('d/m/Y')}}</p>

                  @if($servico->tipoLicenca == 'renovavel')
                    
                    <p><b>Validade da Licença </b>{{\Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</p>

                  @endif
                  
                  <p><b>Emissão Documento: </b> <a href="{{ url("public/uploads/$servico->licenca_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver Licença</a></p>
                  @endunless

                  @unless ( empty($servico->laudo_anexo) )  
                  <p><b>Emissão do Laudo: </b>{{\Carbon\Carbon::parse($servico->laudo_emissao)->format('d/m/Y')}}</p>
                  <p><b>N. do Laudo </b> {{$servico->laudo_numero }}</p>
                  <p><b>Laudo: </b> <a href="{{ url("public/uploads/$servico->laudo_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver Laudo</a></p>
                  @endunless

                </div>

              


            </div>
            <!-- /.box-body -->
           
          </div>
</div>

<div class="row">

@if(count($taxas)>0)
<div class="col-sm-6">
  @include('cliente.components.widget-taxas')
</div>
@endif

@if(count($pendencias->where('status','pendente'))>0)
<div class="col-sm-6">
  @include('cliente.components.widget-pendencias')
</div>
@endif
</div>







<div class="row">
  
<div class="col-sm-12">
      <div class="box box-black">
            <div class="box-header with-border">
              <h3 class="box-title">Histórico</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
             <ul class="timeline timeline-inverse">
                                 
                
                @foreach($servico->ultimasInteracoes as $historico)
                  <!-- timeline item -->
                  <li>
                    
                    <i class="fa fa-user bg-aqua"></i>
                    
                    <div class="timeline-item" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 4px; background: #fff;">
                      <span class="time" style="padding: 10px 15px;">
                        <i class="fa fa-clock-o"></i> 
                        {{\Carbon\Carbon::parse($historico->edited_at ?? $historico->created_at)->timezone('America/Sao_Paulo')->format('d/m/Y H:i')}}
                        @if($historico->edited_at)
                          <span class="text-warning" style="font-size: 10px; font-weight: bold; margin-left: 2px;">(editado)</span>
                        @endif
                      </span>
                      
                      <h3 class="timeline-header" style="border-bottom: none; padding: 10px 15px; font-size: 14px; font-weight: bold;">
                        <strong>{{$historico->user->name ?? 'Robot'}}</strong>
                        <small class="text-muted" style="display: block; font-size: 11px; margin-top: 2px; font-weight: normal;">
                          {{$historico->user->privileges == 'admin' ? 'Admin' : 'Operacional'}}
                        </small>
                      </h3>

                      <div class="timeline-body" style="padding: 5px 15px 10px 15px; font-size: 13px; color: #444; border-bottom: none;">
                        {{$historico->observacoes}}
                      </div>

                      <div class="timeline-footer" style="padding: 5px 15px 10px 15px; background: transparent; border-top: none; display: flex; align-items: center; gap: 8px;">
                        @if($historico->pendencia)
                          <div style="display: inline-flex; border: 1px solid #3c8dbc; border-radius: 4px; overflow: hidden; font-size: 11px;">
                              <span style="background: #3c8dbc; color: #fff; padding: 3px 8px; font-weight: normal;"><i class="fa fa-link"></i> Vinculado à Pendência:</span>
                              <span style="background: #f4f4f4; color: #444; padding: 3px 8px; font-weight: bold;">{{$historico->pendencia->pendencia}}</span>
                          </div>
                        @endif
                      </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                @endforeach
                  
                  
                  <li>
                   <i class="fa fa-clock bg-gray"></i>
                   <div class="timeline-item">
                      <a href="{{route('cliente.interacoes.lista',$servico->id)}}" class="btn btn-flat">Visualizar todo o histórico</a>

                    </div>
                  </li>
                </ul>
            </div>

            <div class="box-footer">
                
               

                </div>

            </div>


                 
  </div>


  </div> 

</div>


@endsection


@section('js')
<script>
 $('#full').mentionsInput({
    onDataRequest:function (mode, query, callback) {
      $.getJSON('{{route('cliente.users.list')}}', function(responseData) {
        responseData = _.filter(responseData, function(item) { 
          return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 
          });
        
          callback.call(this, responseData);        
      });
    }

    

});

$('.responder').click(function () {

        
var msg = $(this).data('msg');
var userID = $(this).data('user');

$.getJSON('{{route('cliente.users.list')}}', function(responseData) {

 var user_filter = responseData.filter(element => element.id == userID);

  var userName = JSON.stringify(user_filter);
  var user = JSON.parse(userName);
  
   

  $('#full').val(user[0].name).focus();

  console.log(userName);

});



});
</script>
 



  @stop