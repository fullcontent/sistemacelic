@extends('adminlte::page')



@section('content_header')
    <h1>Seja bem vindo, {{$user = Auth::user()->name}}</h1>
@stop



@section('content')
@if(Session::has('errors'))
@foreach($errors->all() as $error)

<div class="alert alert-danger alert-dismissible">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
  <h4><i class="icon fa fa-ban"></i> Aviso!</h4>
  {!! $error !!}
</div>
@endforeach
@endif

    
    
<div class="row">       
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>{{count($finalizados)}}</h3>

              <p>Serviços Finalizados</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{route('servico.finalizado')}}" class="small-box-footer">Visualizar <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
       
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>{{count($vencer)}}</h3>

              <p>Serviços a vencer</p>
            </div>
            <div class="icon">
              
              <i class="ion ion-alert"></i>
            </div>
            <a href="{{route('servico.vencer')}}" class="small-box-footer">Visualizar <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>{{count($andamento)}}</h3>

              <p>Serviços em andamento</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{route('servico.andamento')}}" class="small-box-footer">Visualizar <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        
      </div>


@if(count($pendencias->where('status','pendente')->where('vencimento',date('Y-m-d'))) > 0)

@include('admin.components.widget-pendencias-dia')

@endif


@if(count($pendencias->where('status','pendente')->where('vencimento','<',date('Y-m-d'))) > 0)

@include('admin.components.widget-pendencias-atrasadas')

@endif


@if(count($pendencias->where('status','pendente')) > 0)

@include('admin.components.widget-pendencias-usuario')

@endif






@stop

@section('js')
<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script>
    $(function () {
        $('#lista-pendencias').DataTable({
          "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
           "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }
         
        });
  });
  $(function () {
        $('#lista-pendencias-atrasadas').DataTable({
          "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
           "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }
         
        });
  });
  $(function () {
        $('#lista-pendencias-dia').DataTable({
          "paging": true,
          "lengthChange": false,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
           "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }
         
        });
  });
    </script>

    
  @stop