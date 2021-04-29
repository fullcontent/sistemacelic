@extends('adminlte::page')





@section('content')

	
	<div class="box">
					<div class="box-header">
					
						<h4>Interações da O.S. {{$servico->os}}</h4>
            <div class="pull-right">
            <a href="{{route('servicos.show',$servico->id)}}" class="btn btn-default">Voltar</a>
            </div>
            

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
                      <span class="time"><i class="fa fa-clock"></i> {{\Carbon\Carbon::parse($historico->created_at)->format('d/m/Y h:m')}}</span>
                      <h3 class="timeline-header"><a href="#">{{$historico->user->name}}</a></h3>

                      <div class="timeline-body">
                  			{{$historico->observacoes}}
                	  </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                @endforeach
                                 
                  
                </ul>

                <div class="box-footer">
               
                <a href="{{route('servicos.show',$historico->servico_id)}}" class="btn btn-default">Voltar</a>
                

                </div>

            </div>

	</div>

@stop