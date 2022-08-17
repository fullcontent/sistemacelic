@extends('adminlte::page')
@section('content_header')
    <h1>Relatórios</h1>
@stop


@section('content')
<div class="col-md-3">
    <div class="box box-default">
        <div class="box-header with-border">

            <h3 class="box-title">Completos</h3>
            <p>Download em formato .csv</p>
        </div>

        <div class="box-body">


            <a href="{{route('relatorio.completo')}}" target="_blank"
                class="btn btn-block btn-default btn-lg">Serviços</a>

            <a href="{{route('relatorio.taxas')}}" target="_blank" class="btn btn-block btn-default btn-lg">Taxas</a>

            <a href="{{route('relatorio.pendencias')}}" target="_blank"
                class="btn btn-block btn-default btn-lg">Pendências</a>


        </div>

    </div>

</div>



@endsection