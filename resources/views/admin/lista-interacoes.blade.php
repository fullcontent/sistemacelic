@extends('adminlte::page')





@section('content')

<a href="{{route('timeline',$servico->id)}}" class='btn btn-app btn-xs'><i class="glyphicon glyphicon-time"> </i>Timeline</a>

	<div class="box">
					<div class="box-header">
					<div class="pull-right">
           <h3><a href="{{route('servicos.show',$servico->id)}}" class="btn btn-default"><i class="fa fa-chevron-left"></i> Voltar</a></h3>
            
            </div>
						<h4>Interações da O.S. {{$servico->os}}</h4>
            
					</div>

           
		<ul class="timeline timeline-inverse">
                                 
                  
                @foreach($interacoes as $historico)
                  <!-- timeline item -->
                  <li>
                    
                    @php

                    	if($historico->user->privileges == 'cliente')
                    	{
                    		$label = "fa fa-user bg-red";
                    	}
                    	elseif($historico->user->privileges == 'admin')
                    	{
                    		$label = "fa fa-copyright bg-aqua";
                    	}
                      else
                      {
                        $label = "fa fa-weixin bg-default";
                      }


                    @endphp
                    <i class="{{$label}}"></i>
                    
                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock"></i> {{\Carbon\Carbon::parse($historico->created_at)->format('d/m/Y H:m')}}</span>
                      <h3 class="timeline-header"><a href="#">{{$historico->user->name}}</a></h3>

                      <div class="timeline-body">
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
                        @endif                	  </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                @endforeach
                                 
                  
                </ul>

                <div class="box-footer">
               
                <a href="{{route('servicos.show',$servico->id)}}" class="btn btn-default"><i class="fa fa-chevron-left"></i> Voltar</a>
                

                </div>

            </div>



  <div class="box box-default collapsed-box">
      <div class="box-header">

          <h3 class="box-title"><a href="" data-widget="collapse">Interações do Sistema {{$servico->os}}</a></h3>
          <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
              </button>
          </div>
      </div>
      <div class="box-body">



          <ul class="timeline timeline-inverse">


              @foreach($interacoesSistema as $historico)
              <!-- timeline item -->
              <li>

                  @php

                  if($historico->user->privileges == 'cliente')
                  {
                  $label = "fa fa-user bg-red";
                  }
                  elseif($historico->user->privileges == 'admin')
                  {
                  $label = "fa fa-copyright bg-aqua";
                  }
                  else
                  {
                  $label = "fa fa-weixin bg-default";
                  }


                  @endphp
                  <i class="{{$label}}"></i>

                  <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock"></i>
                          {{\Carbon\Carbon::parse($historico->created_at)->format('d/m/Y H:m')}}</span>
                      <h3 class="timeline-header"><a href="#">{{$historico->user->name}}</a></h3>

                      <div class="timeline-body">
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
                          @endif </div>
                  </div>
              </li>
              <!-- END timeline item -->
              @endforeach


          </ul>
      </div>

  </div>

</div>

@stop