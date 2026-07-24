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

              <p>Serviços finalizados</p>
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
              <small>Responsável</small>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{route('servico.andamento')}}" class="small-box-footer">Visualizar <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>{{count($andamentoCoResponsavel)}}</h3>

              <p>Serviços em andamento</p>
              <small>Co-Responsável</small>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{route('servico.andamentoCo')}}" class="small-box-footer">Visualizar <i class="fa fa-arrow-circle-right"></i></a>
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






@if(Auth::user()->isCoordinatorOrAdmin())
    <hr style="border-top: 2px solid #ccc; margin: 30px 0;">
    
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-filter"></i> Filtros de Pendências Gerais (Coordenador)</h3>
                </div>
                <div class="box-body">
                    <form action="{{ route('dashboard') }}" method="GET" id="filter-form">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                                <label for="responsavel_id">Responsável</label>
                                <select name="responsavel_id" id="responsavel_id" class="form-control select2" style="width: 100%;">
                                    <option value="">Todos os usuários</option>
                                    @foreach($responsaveis as $id => $name)
                                        <option value="{{ $id }}" {{ $responsavel_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control select2" style="width: 100%;">
                                    <option value="todas" {{ $status == 'todas' ? 'selected' : '' }}>Todas</option>
                                    <option value="ativas" {{ $status == 'ativas' ? 'selected' : '' }}>Ativa (Pendente)</option>
                                    <option value="atrasadas" {{ $status == 'atrasadas' ? 'selected' : '' }}>Em Atraso</option>
                                    <option value="concluidas" {{ $status == 'concluidas' ? 'selected' : '' }}>Concluída</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                                <label for="empresa_id">Empresa</label>
                                <select name="empresa_id" id="empresa_id" class="form-control select2" style="width: 100%;">
                                    <option value="">Todas as empresas</option>
                                    @foreach($empresas as $id => $nome)
                                        <option value="{{ $id }}" {{ $empresa_id == $id ? 'selected' : '' }}>{{ $nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                                <label for="unidade_id">Unidade</label>
                                <select name="unidade_id" id="unidade_id" class="form-control select2" style="width: 100%;">
                                    @if($selectedUnidade)
                                        <option value="{{ $selectedUnidade->id }}" selected>{{ $selectedUnidade->nomeFantasia }} ({{ $selectedUnidade->codigo }})</option>
                                    @else
                                        <option value="">Selecione a unidade</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                                <label for="prioridade">Prioridade</label>
                                <select name="prioridade" id="prioridade" class="form-control select2" style="width: 100%;">
                                    <option value="todas" {{ $prioridade == 'todas' ? 'selected' : '' }}>Todas</option>
                                    <option value="sim" {{ $prioridade == 'sim' ? 'selected' : '' }}>Prioritárias</option>
                                    <option value="nao" {{ $prioridade == 'nao' ? 'selected' : '' }}>Não Prioritárias</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                                <label for="data_inicio">Vencimento a partir de</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ $data_inicio }}">
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                                <label for="data_fim">Vencimento até</label>
                                <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ $data_fim }}">
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-12 form-group" style="margin-top: 25px;">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                                <a href="{{ route('dashboard') }}" class="btn btn-default"><i class="fa fa-undo"></i> Limpar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Listagem Geral de Pendências</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="tabela-dashboard-geral-pendencias" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="3%">Prioridade</th>
                                    <th>Empresa</th>
                                    <th>Unidade</th>
                                    <th>Serviço</th>
                                    <th>Pendência</th>
                                    <th>Responsável</th>
                                    <th>Vencimento</th>
                                    <th>Status</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dashboardPendencias as $p)
                                <tr>
                                    <td class="prioridade text-center">
                                        @if($p->prioridade == 1)
                                            <i class="fa fa-exclamation priorize-btn" style="color:red; cursor:pointer;" data-id="{{ $p->id }}" title="Clique para remover prioridade"></i>
                                        @else
                                            <input type="checkbox" class="priorize-check" data-id="{{ $p->id }}" title="Clique para priorizar">
                                        @endif
                                    </td>
                                    <td>
                                        @if($p->servico && $p->servico->unidade && $p->servico->unidade->empresa)
                                            <a href="{{ route('empresas.show', $p->servico->unidade->empresa->id) }}">
                                                {{ $p->servico->unidade->empresa->nomeFantasia }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($p->servico && $p->servico->unidade)
                                            <a href="{{ route('servicos.show', $p->servico_id) }}">
                                                {{ $p->servico->unidade->nomeFantasia }}
                                                @if($p->servico->unidade->codigo)
                                                    ({{ $p->servico->unidade->codigo }})
                                                @endif
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('servicos.show', $p->servico_id) }}">
                                            {{ $p->servico->nome ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('servicos.show', $p->servico_id) }}">
                                            {{ $p->pendencia }}
                                        </a>
                                    </td>
                                    <td>{{ $p->responsavel->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($p->vencimento)
                                            @php
                                                $hoje = date('Y-m-d');
                                                $classe = 'label-info';
                                                if ($p->status == 'pendente') {
                                                    if ($p->vencimento < $hoje) {
                                                        $classe = 'label-danger';
                                                    } elseif ($p->vencimento == $hoje) {
                                                        $classe = 'label-warning';
                                                    } else {
                                                        $classe = 'label-success';
                                                    }
                                                }
                                            @endphp
                                            <span class="label {{ $classe }}">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y') }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($p->status == 'pendente')
                                            <span class="label label-warning">Ativa</span>
                                        @else
                                            <span class="label label-success">Concluída</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if($p->status == 'pendente')
                                                <button class="btn btn-xs btn-success toggle-done-btn" data-id="{{ $p->id }}" data-action="done" title="Marcar como concluída">
                                                    <i class="fa fa-check"></i> Concluir
                                                </button>
                                            @else
                                                <button class="btn btn-xs btn-warning toggle-done-btn" data-id="{{ $p->id }}" data-action="undone" title="Marcar como ativa">
                                                    <i class="fa fa-undo"></i> Reabrir
                                                </button>
                                            @endif
                                            <a href="{{ route('pendencia.edit', $p->id) }}" class="btn btn-xs btn-primary" title="Editar">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('pendencia.delete', $p->id) }}" class="btn btn-xs btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta pendência?')" title="Excluir">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endif

@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 34px !important;
        border-radius: 4px;
        border: 1px solid #ccc;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@stop

@section('js')
<script src="http://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    @if(Auth::check() && Auth::user()->isCoordinatorOrAdmin())
        // Inicializa DataTable
        $('#tabela-dashboard-geral-pendencias').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            },
            "order": []
        });

        // Inicializa Select2 simples
        $('.select2:not(#unidade_id)').select2();

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        // Inicializa Select2 AJAX de Unidades
        $('#unidade_id').select2({
            placeholder: 'Selecione a unidade',
            allowClear: true,
            ajax: {
                url: "/api/unidades/get",
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term,
                        empresa_id: $('#empresa_id').val()
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        });

        // Se a empresa mudar, limpa e re-filtra a unidade correspondente
        $('#empresa_id').change(function() {
            $('#unidade_id').val(null).trigger('change');
        });

        // Toggle de prioridade via AJAX (Marca como Prioritária)
        $('.priorize-check').change(function() {
            if ($(this).is(':checked')) {
                var id = $(this).data('id');
                $.ajax({
                    url: '/admin/pendencia/priority/' + id,
                    method: 'GET',
                    success: function() {
                        location.reload();
                    }
                });
            }
        });

        // Toggle de prioridade via AJAX (Remove Prioritária)
        $('.priorize-btn').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: '/admin/pendencia/unPriority/' + id,
                method: 'GET',
                success: function() {
                    location.reload();
                }
            });
        });

        // Concluir/Reabrir pendência via AJAX
        $('.toggle-done-btn').click(function() {
            var id = $(this).data('id');
            var action = $(this).data('action'); // 'done' ou 'undone'
            $.ajax({
                url: '/admin/pendencia/' + action + '/' + id,
                method: 'GET',
                success: function() {
                    location.reload();
                }
            });
        });
    @endif
});
</script>
@stop