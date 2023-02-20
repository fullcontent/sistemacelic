@extends('adminlte::page');


@section('content')



<div class="containner" style="padding: 0 10%">

  
    <div class="row etapa">
        <div class="col-md-4 text-right"><h2>Inicio</h2></div>
        <div class="col-md-8">
            
           
            <div class="col-md-12">
                <div class="timeline-item">
                    <div class="timeline-header no-border">
                        <h3 class="box-title">{{$servico->nome}}</h3>
                        </div>
                    <div class="box-body">
                        
                        <ul class="nav nav-pills nav-stacked">
                            <li><h4><a href="#"><i class="fa fa-user"></i> {{$servico->responsavel->name}}</a></h4></li>
                            <li><h4><a href="#"><i class="glyphicon glyphicon-calendar"></i> {{\Carbon\Carbon::parse($servico->created_at)->format('d/m/Y')}}</a></h4></li>
                            
                            <li></li>
                        </ul>
                        <a href="{{route('servicos.show',$servico->id)}}" class="pull-right btn btn-primary btn-xs">Ver Serviço</a>

                    </div>
    
                </div>
            </div>
           
            
        </div>
    </div>

@foreach($servico->pendencias->groupBy('etapa') as $etapa => $pendencia)
    <div class="row etapa">
        <div class="col-md-4 text-right"><h2>Etapa @if($etapa){{$etapa}} @endif</h2></div>
        <div class="col-md-8">
            
            @foreach($pendencia as $p)
            <div class="col-md-6 item">
                <div class="timeline-item">
                    <div class="timeline-header no-border">
                        <h3 class="box-title">{{$p->pendencia}}</h3>
                        </div>
                    <div class="box-body">
                        
                        <ul class="nav nav-pills nav-stacked">

                            @if($p->responsavel_tipo == 'usuario')

                            <li><a href="#"><i class="fa fa-copyright"></i>  Castro</a></li>

                            @elseif($p->responsavel_tipo == 'op')

                            <li><a href="#"><i class="fa fa-building"></i>  Órgão Público</a></li>

                            @endif
                            
                            
                            
                            <li><a href="#"><i class="fa fa-user"></i> {{$p->responsavel->name}}</a></li>
                            <li><a href="#"><i class="glyphicon glyphicon-calendar"></i> {{\Carbon\Carbon::parse($p->created_at)->format('d/m/Y')}}</a></li>
                            
                            @switch($p->vencimento)
                        
                            @case($p->vencimento > date('Y-m-d'))
                                <li><span id="dataPendencia" class="label label-success">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>
                                    </li>
                                
                            @break
    
                            @case($p->vencimento < date('Y-m-d'))
                                 <span id="dataPendencia"  class="label label-danger">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>
                            @break
    
                            @case($p->vencimento == date('Y-m-d'))
                                <span id="dataPendencia"  class="label label-warning">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>
                            @break
    
    
    
    
    
                      @endswitch
                            @if($p->status)
                            <li><a href="#"><i class="fa fa-status"></i> {{$p->status}}</a></li>
                            @elseif($p->status)
                            
                            @endif



                            
                            <li><a href="#"><i class="fa fa-status"></i> {{$p->observacoes}}</a></li>

                        </ul>

                        <a href="/pendencia/{{$p->id}}/previousEtapa" class='btn btn-app btn-xs'><i class="fa fa-arrow-circle-up"> </i>Retroceder etapa</a>
                        <a href="/pendencia/{{$p->id}}/nextEtapa" class='btn btn-app btn-xs'><i class="fa fa-arrow-circle-down"> </i>Avançar etapa</a> 


                    </div>
    
                </div>
            </div>
           @endforeach
            
        </div>
    </div>
@endforeach




</div>



@endsection


@section('css')

<style>
    .etapa{
        background-color: #fff;
        padding: 20px;
        border-bottom: 1px solid #f4f4f4;
        border-top: 3px solid #d2d6de;
        margin-bottom: 20px;
        box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
        border-radius: 3px;

    }
    .etapa h2 {

        margin: 25% auto;
    }

    .item {
        -webkit-box-shadow: 0 2px 2px 0 rgba(0,0,0,0.2),0 6px 10px 0 rgba(0,0,0,0.2);
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.2),0 6px 10px 0 rgba(0,0,0,0.2);
        padding: 10px;
        
    }

    .box-title{
        padding: 10px;
    }

</style>


@endsection