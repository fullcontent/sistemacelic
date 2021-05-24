@extends('adminlte::page')

@section('content_header')
    <h1>Detalhes do serviço</h1>
@stop



@section('content')
  
  
  
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


                  <p><b>Emissão da Licença: </b>{{\Carbon\Carbon::parse($servico->licenca_emissao)->format('d/m/Y')}}</p>

                  @if($servico->tipoLicenca == 'renovavel')
                    
                    <p><b>Validade da Licença </b>{{\Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</p>

                  @endif
                  
                  <p><b>Emissão Documento: </b> <a href="{{ url("uploads/$servico->licenca_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver Licença</a></p>
                  @endunless

                  @unless ( empty($servico->laudo_anexo) )  
                  <p><b>Emissão do Laudo: </b>{{\Carbon\Carbon::parse($servico->laudo_emissao)->format('d/m/Y')}}</p>
                  <p><b>N. do Laudo </b> {{$servico->laudo_numero }}</p>
                  <p><b>Laudo: </b> <a href="{{ url("uploads/$servico->laudo_anexo") }}" class="btn btn-xs btn-warning" target="_blank">Ver Laudo</a></p>
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
  
<div class="col-sm-6">

@include('cliente.components.widget-interacoesChat')
</div>


<div class="col-sm-6">
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
            
             <ul class="timeline timeline-inverse">
                                 
                
                @foreach($servico->ultimasInteracoes as $historico)
                  <!-- timeline item -->
                  <li>
                    
                    <i class="fa fa-user bg-aqua"></i>
                    
                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock"></i> {{\Carbon\Carbon::parse($historico->created_at)->format('d/m/Y H:m')}}</span>
                      <h3 class="timeline-header"><a href="#">{{$historico->user->name ?? 'Robot'}}</a> {{$historico->observacoes}}</h3>

                      
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