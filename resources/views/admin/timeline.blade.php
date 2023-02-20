@extends('adminlte::page');


@section('content')



<div class="containner" style="padding: 0 10%">

  
    <div class="row etapa">
        <div class="col-md-4 text-right">
            <h1>Inicio</h1>
        </div>
        <div class="col-md-8">
            
           
            <div class="col-md-12">
                <div class="timeline-item">
                    <div class="timeline-header no-border">
                        <h3 class="box-title">{{$servico->nome}}</h3>
                        </div>
                    <div class="box-body">
                        
                        <ul class="nav nav-pills nav-stacked">
                            <li><h4><a href="javascript:void()"><i class="fa fa-user"></i> {{$servico->responsavel->name ?? ''}}</a></h4></li>
                            <li><h4><a href="javascript:void()"><i class="glyphicon glyphicon-calendar"></i> {{\Carbon\Carbon::parse($servico->created_at)->format('d/m/Y')}}</a></h4></li>
                            
                            <li></li>
                        </ul>
                        <a href="{{route('servicos.show',$servico->id)}}" class="pull-right btn btn-primary btn-xs">Ver Serviço</a>

                    </div>
    
                </div>
            </div>
           
            
        </div>
    </div>

@foreach($servico->pendencias->sortBy('etapa')->groupBy('etapa') as $etapa => $pendencia)
    <div class="row etapa">
        <div class="col-md-4 text-right"><h2>Etapa @if($etapa){{$etapa}} @endif</h2></div>
        <div class="col-md-8">
            
            @foreach($pendencia as $p)
            <div class="col-md-6 item">
                <div class="timeline-item">
                    <div class="timeline-header no-border">
                        <h3 class="box-title">{{$p->pendencia}}</h3>
                        <small>tes</small>
                        </div>
                    <div class="box-body">
                        
                        <ul class="nav nav-pills nav-stacked">

                            @if($p->responsavel_tipo == 'usuario')

                            <li><a href="javascript:void()"><i class="fa fa-copyright"></i> Castro</a></li>

                            @elseif($p->responsavel_tipo == 'op')

                            <li><a href="javascript:void()"><i class="fa fa-building"></i> Órgão Público</a></li>

                            @elseif($p->responsavel_tipo == 'cliente')

                            <li><a href="javascript:void()"><i class="fa fa-user"></i> Cliente</a></li>

                            @elseif($p->responsavel_tipo == 'vinculada')

                            <li><a href="javascript:void()"><i class="fa fa-link"></i> Vinculada</a></li>

                            @endif



                            <li><a href="javascript:void()"><i class="fa fa-user"></i> {{$p->responsavel->name}}</a></li>
                            <li><a href="javascript:void()"><i class="glyphicon glyphicon-calendar"></i>
                                    {{\Carbon\Carbon::parse($p->created_at)->format('d/m/Y')}}</a></li>
                            
                            <li>
                            @if($p->status == "concluido")
                            <a href="javascript:void()"><i class="fa fa-check-square"></i> <span class="label label-success">{{$p->status}}</span></a>
                            @elseif($p->status == "pendente")
                            <a href="javascript:void()"><i class="fa fa-exclamation"></i> <span class="label label-warning">{{$p->status}}</span></a>
                            @endif
                            </li>
                            
                            <li>


                                <a href="javascript:void()">
                                    @switch($p->vencimento)

                                    @case($p->vencimento > date('Y-m-d'))
                                    <i class="fa fa-calendar-check"></i> <span id="dataPendencia"
                                        class="label label-success">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>


                                    @break

                                    @case($p->vencimento < date('Y-m-d')) <i class="fa fa-calendar-times"></i> <span
                                            id="dataPendencia"
                                            class="label label-danger">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>
                                        @break

                                        @case($p->vencimento == date('Y-m-d'))
                                        <i class="fa fa-calendar-times"></i> <span id="dataPendencia"
                                            class="label label-warning">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y')}}</span>
                                        @break

                                </a> </li>



                            @endswitch
                            






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


@section('js')

<script>
    
</script>


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

        
        display: flex;
        align-items: center;
        height: 100%;
    }

    .item {
        -webkit-box-shadow: 0 2px 2px 0 rgba(0,0,0,0.2),0 6px 10px 0 rgba(0,0,0,0.2);
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.2),0 6px 10px 0 rgba(0,0,0,0.2);
        padding: 10px;
        height: 400px;
        margin-top: 20px;
        
    }

    .box-title{
        padding: 10px;
    }

</style>


@endsection