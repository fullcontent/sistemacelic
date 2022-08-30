@extends('adminlte::page')

@section('content_header')
    <h1>{{$servico->os}}</h1>
@stop
  


@section('content')
  
  
  
<div class="row">

  
    <div class="col-md-12">
        
        @include('admin.components.widget-detalhes')
        
        
    </div>
</div>


@if($servico->servicoPrincipal)

<div class="col-md-12">
@include('admin.components.widget-servicoPrincipal')
</div>


@endif


<div class="col-md-12">
  
    <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><b>Detalhes do serviço {{$servico->nome}} @if($servico->servicoPrincipal) <small class="label pull-right bg-red">S</small>@endif</b></h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                
                <div class="col-sm-6">
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
								<button type="button" class="btn btn-xs btn-gray">Stand By</button>
	              				@break

                        @case('nRenovado')
								<button type="button" class="btn btn-xs btn-default">Não renovado</button>
	              				@break

                        
	              		@endswitch</p>
                  <p><b>Ordem de serviço: </b>{{$servico->os}}</p>
                  <p><b>Proposta: </b>@if($servico->proposta_id)
					      <a href="{{route('proposta.edit',$servico->proposta_id)}}" class="btn btn-info btn-xs">{{$servico->proposta_id}}</a>@else{{$servico->proposta}}@endif</p>
                  
                  <p><b>Responsável: </b>{{$servico->responsavel->name ?? ''}}</p>
                  
                  @if($servico->coresponsavel)
                  <p><b>Co-Responsável: </b>{{$servico->coresponsavel->name ?? ''}}</p>
                  @endif

                  @if($servico->solicitanteServico)
                      <p><b>Solicitante (novo): </b>
                        {{$servico->solicitanteServico->nome}}

                    @else
                    <p><b>Solicitante: </b>
                    {{$servico->solicitante}}

                    @endif
                 
                    
                    
                  </p>

                  <p><b>Início do processo: </b>{{\Carbon\Carbon::parse($servico->created_at)->format('d/m/Y')}}</p>

                  @if(empty($servico->protocolo_numero))
                  <p><b>Protocolo:</b> <button type="button" class="btn btn-default" data-toggle="modal"
                    data-target="#anexar-protocolo">
                    Anexar
                </button></p>
                @endif
                  
                  @unless ( empty($servico->protocolo_numero) )
                    
                  <p><b>Emissão Protocolo: </b>{{\Carbon\Carbon::parse($servico->protocolo_emissao)->format('d/m/Y')}}</p>
                  <p><b>Número Protocolo: </b>{{$servico->protocolo_numero}}

                    @unless (empty($servico->protocolo_anexo))
                    <a href="{{ route('servico.downloadFile', ['servico_id'=> $servico->id,'tipo'=>'protocolo']) }}" class="btn btn-xs btn-warning" target="_blank"><i class="fa fa-file"></i> Ver Protocolo</a>
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
                            Não aplicada
                            @break
                        @case('definitiva')
                            Definitiva
                            @break
                        
                            
                    @endswitch
                  </p>

                  @unless ( empty($servico->licenca_anexo) ) 
                  @if($servico->tipoLicenca != 'n/a')
                  <p><b>Emissão da Licença: </b>{{\Carbon\Carbon::parse($servico->licenca_emissao)->format('d/m/Y')}}</p>
                  @endif

                  @if($servico->tipoLicenca == 'renovavel')
                    
                    <p><b>Validade da Licença </b>{{\Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y')}}</p>

                  @endif
                  
                  <p><b>Emissão Documento: </b> <a href="{{ route('servico.downloadFile', ['servico_id'=> $servico->id,'tipo'=>'licenca']) }}" class="btn btn-xs btn-warning" target="_blank">Ver Licença</a></p>
                  @endunless

                  
                  @if(empty($servico->laudo_numero))
                  <p><b>Laudo:</b> <button type="button" class="btn btn-default" data-toggle="modal"
                          data-target="#anexar-laudo">
                          Anexar
                      </button></p>
                  @endif
                  @unless ( empty($servico->laudo_anexo) )  
                  <p><b>Emissão do Laudo: </b>{{\Carbon\Carbon::parse($servico->laudo_emissao)->format('d/m/Y')}}</p>
                  <p><b>N. do Laudo </b> {{$servico->laudo_numero }}</p>
                  <p><b>Laudo: </b> <a href="{{ route('servico.downloadFile', ['servico_id'=> $servico->id,'tipo'=>'laudo']) }}" class="btn btn-xs btn-warning" target="_blank"><i class="fa fa-file"></i> Ver Laudo</a></p>
                  @endunless
                  <p><b>Escopo: </b>{{$servico->escopo}}</p>
                  

                </div>

              <a href="{{route('servicos.edit', $servico->id)}}" class="btn btn-info pull-right"><span class="glyphicon glyphicon-pencil"></span> Editar</a>
              
              @if(!$servico->servicoPrincipal)
              <a href="{{route('servicos.create', ['id'=>$servico->unidade_id,'t'=>substr($route, 0,7),'tipoServico'=>'nRenovaveis','servicoPrincipal'=>$servico->id])}}" class="btn btn-warning pull-right"><span class="glyphicon glyphicon-plus"></span> SubServiço</a>
              @endif
              
              @if(count($servico->subServicos))

                

              {!! Form::open(['route'=>'faturamento.faturarServicoSub','id'=>'cadastroFaturamento', 'target'=>'_blank']) !!}

                {!! Form::hidden('servicos[]',$servico->id) !!}

                
                {!! Form::hidden('empresa_id',$servico->unidade->empresa_id) !!}
                
                <button type="submit" class="btn btn-danger pull-right"><i class="fa fa-barcode"></i> Faturar</button>

                {!! Form::close() !!}

                @elseif($servico->servicoPrincipal)

                {!! Form::open(['route'=>'faturamento.faturarServicoSub','id'=>'cadastroFaturamento', 'target'=>'_blank']) !!}
  
                  {!! Form::hidden('servicos[]',$servico->id) !!}
  
                  
                  {!! Form::hidden('empresa_id',$servico->unidade->empresa_id) !!}

                  <button type="submit" class="btn btn-danger pull-right"><i class="fa fa-barcode"></i> Faturar</button>
  
                  {!! Form::close() !!}

              @else
              
              {!! Form::open(['route'=>'faturamento.step3','id'=>'cadastroFaturamento', 'target'=>'_blank']) !!}

              {!! Form::hidden('servicos[]',$servico->id) !!}
              {!! Form::hidden('empresa_id',$servico->unidade->empresa_id) !!}
              
              @if(!$servico->faturamento)
                <button type="submit" class="btn btn-danger pull-right"><i class="fa fa-barcode"></i> Faturar</button>

              @else

              <div class="col-md-12">
                <div class="form-group">
              
                  <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-check"></i> Esse serviço já foi faturado!</h4>
                    <a class="btn btn-warning" href="{{route('faturamento.show', $servico->faturamento->id)}}" target="_blank"><i class="fa fa-file"></i> <span>Acessar relatório</span> </a>
                  </div>
                </div>
              </div>

              @endif
              {!! Form::close() !!}

              @endif
              
              

            </div>
            <!-- /.box-body -->
           
          </div>
</div>



@if(count($servico->subServicos))

<div class="col-md-12">
    @include('admin.components.widget-subServicos')
</div>

@endif




<div class="col-md-7">
  @include('admin.components.widget-taxas')
</div>


<div class="col-md-5">
  @include('admin.components.widget-pendencias')
</div>




<div class="row">
  
<div class="col-sm-6">

@include('admin.components.widget-interacoesChat')
</div>


<div class="col-sm-6">
      <div class="box box-black">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-history"></i> Histórico</h3>

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
                      <h3 class="timeline-header"><a href="#">{{$historico->user->name ?? 'Robot'}}</a>
                        @if(str_contains($historico->observacoes, 'Alterou solicitante'))
                          @php
                              $id = preg_replace('/[^0-9]/', '', $historico->observacoes);  
                              $solicitante = \App\Models\Solicitante::where('id',$id)->value('nome');
                              echo "Alterou solicitante para ".$solicitante;
                          @endphp
                        @elseif(str_contains($historico->observacoes, 'Alterou responsavel_id'))
                        @php
                            $id = preg_replace('/[^0-9]/', '', $historico->observacoes);  
                            $solicitante = \App\User::where('id',$id)->value('name');
                            echo "Alterou responsável para ".$solicitante;
                        @endphp
                        @else
                        {{$historico->observacoes}}
                        @endif
                      </h3>

                      
                    </div>
                  </li>
                  <!-- END timeline item -->
                @endforeach
                  
                  
                  <li>
                   <i class="fa fa-clock bg-gray"></i>
                   <div class="timeline-item">
                      <a href="{{route('interacoes.lista',$servico->id)}}" class="btn btn-flat">Visualizar todo o histórico</a>

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


<div class="modal fade" id="anexar-laudo" style="display: none;">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
              <h4 class="modal-title">Anexar laudo {{$servico->os}}</h4>
          </div>

          {!! Form::open(['route'=>'servico.anexarLaudo','id'=>'anexarlaudo','enctype'=>'multipart/form-data']) !!}
          <div class="modal-body">
            
            <div class="form-group">

              {!! Form::hidden('servico_id', $servico->id) !!}
		
              {!! Form::label('laudo_numero', 'N. laudo', array('class'=>'control-label')) !!}
              {!! Form::text('laudo_numero', null, ['class'=>'form-control','id'=>'laudo_numero']) !!}
                        
              
            </div>
            
            <div class="form-group">
		
              {!! Form::label('laudo_emissao', 'Emissão laudo', array('class'=>'control-label')) !!}
              {!! Form::text('laudo_emissao', null, ['class'=>'form-control','id'=>'laudo_emissao','data-date-format'=>'dd/mm/yyyy']) !!}
              
            </div>

            <div class="form-group">
		
              {!! Form::label('laudo_anexo', 'Anexo laudo.', array('class'=>'control-label')) !!}
              {!! Form::file('laudo_anexo', null, ['class'=>'form-control','id'=>'laudo_anexo']) !!}
          
             
            </div>

          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
              <button type="submit" class="btn btn-primary">Anexar</button>
          </div>
      </div>
        {!! Form::close() !!}
  </div>

</div>


<div class="modal fade" id="anexar-protocolo" style="display: none;">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
              <h4 class="modal-title">Anexar Protocolo {{$servico->os}}</h4>
          </div>

          {!! Form::open(['route'=>'servico.anexarProtocolo','id'=>'anexarProtocolo','enctype'=>'multipart/form-data']) !!}
          <div class="modal-body">
            
            <div class="form-group">

              {!! Form::hidden('servico_id', $servico->id) !!}
		
              {!! Form::label('protocolo_numero', 'N. Protocolo', array('class'=>'control-label')) !!}
              {!! Form::text('protocolo_numero', null, ['class'=>'form-control','id'=>'protocolo_numero']) !!}
                        
              
            </div>
            
            <div class="form-group">
		
              {!! Form::label('protocolo_emissao', 'Emissão Protocolo', array('class'=>'control-label')) !!}
              {!! Form::text('protocolo_emissao', null, ['class'=>'form-control','id'=>'protocolo_emissao','data-date-format'=>'dd/mm/yyyy']) !!}
              
            </div>

            <div class="form-group">
		
              {!! Form::label('protocolo_anexo', 'Anexo Protocolo.', array('class'=>'control-label')) !!}
              {!! Form::file('protocolo_anexo', null, ['class'=>'form-control','id'=>'protocolo_anexo']) !!}
          
             
            </div>

          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
              <button type="submit" class="btn btn-primary">Anexar</button>
          </div>
      </div>
        {!! Form::close() !!}
  </div>

</div>

@endsection

