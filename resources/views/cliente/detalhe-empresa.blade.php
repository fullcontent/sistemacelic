@extends('adminlte::page')

@php
    $isUnidade = isset($dados->empresa_id) || isset($dados->endereco);
    $headerTitle = $isUnidade 
        ? 'Unidade: ' . (!empty($dados->codigo) ? '[' . $dados->codigo . '] ' : '') . $dados->nomeFantasia
        : 'Empresa: ' . $dados->nomeFantasia;
    $headerIcon = $isUnidade ? 'fa-building-o' : 'fa-building';

    $cntAndamento = $servicos ? $servicos->where('situacao', 'andamento')->count() : 0;
    $cntFinalizados = $servicos ? $servicos->where('situacao', 'finalizado')->count() : 0;
    $cntStandby = $servicos ? $servicos->where('situacao', 'standBy')->count() : 0;
    $cntCancelados = $servicos ? $servicos->where('situacao', 'cancelado')->count() : 0;
    $cntArquivados = $servicos ? $servicos->where('situacao', 'arquivado')->count() : 0;
@endphp

@section('content_header')
    <h1 style="font-weight: 700; color: #1e293b; font-size: 24px; margin-bottom: 5px;">
        <i class="fa {{ $headerIcon }} text-primary" style="margin-right: 8px;"></i>{{ $headerTitle }}
    </h1>
@stop

@section('content')
@if (session()->has('success'))
    <div class="alert alert-success alert-dismissible" style="border-radius: 8px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> Sucesso!</h4>
        {{ session('success') }}
    </div>
@endif

<!-- Unit/Empresa Header Details + Street View -->
<div class="row" style="margin-bottom: 20px;">
    @if(isset($dados->endereco) && !empty($dados->endereco))
        <!-- Unidade View: Details + Street View side-by-side -->
        <div class="col-md-8">
            @include('cliente.components.widget-detalhes')
        </div>
        <div class="col-md-4">
            @include('components.street-view', ['unidade' => $dados])
        </div>
    @else
        <!-- Empresa View -->
        <div class="col-md-12">
            @include('cliente.components.widget-detalhes')
        </div>
    @endif
</div>

<!-- Busca Geral (Posicionada acima dos Arquivos) -->
<div class="row" style="margin-bottom: 20px;">
    <div class="col-md-12">
        <div class="input-group input-group-lg" style="box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <span class="input-group-addon" style="background: #fff; border-color: #cbd5e1;"><i class="fa fa-search text-muted"></i></span>
            <input type="text" id="search-servicos" class="form-control" placeholder="Buscar por licenças, certificados, taxas, arquivos ou serviços..." style="border-color: #cbd5e1;">
        </div>
    </div>
</div>

<!-- Arquivo Digital Widget (Download, Tabs, DataTables, Categorias & Upload Grande) -->
<div class="row">
    <div class="col-md-12">
        @include('cliente.components.widget-arquivos')
    </div>
</div>

<!-- Tabs for Services by Situation (Com Contadores) -->
<div class="nav-tabs-custom" style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; margin-bottom: 25px;">
    <ul class="nav nav-tabs" style="border-bottom: 2px solid #f1f5f9; padding-left: 10px; background: #f8fafc;">
        <li class="active">
            <a href="#tab_andamento" data-toggle="tab" style="font-weight: 600;">
                <i class="fa fa-play text-info"></i> Em Andamento <span class="badge bg-blue" style="margin-left: 5px;">{{ $cntAndamento }}</span>
            </a>
        </li>
        <li>
            <a href="#tab_finalizados" data-toggle="tab" style="font-weight: 600;">
                <i class="fa fa-check text-success"></i> Finalizados <span class="badge bg-green" style="margin-left: 5px;">{{ $cntFinalizados }}</span>
            </a>
        </li>
        <li>
            <a href="#tab_standby" data-toggle="tab" style="font-weight: 600;">
                <i class="fa fa-pause text-warning"></i> Stand-by <span class="badge bg-yellow" style="margin-left: 5px;">{{ $cntStandby }}</span>
            </a>
        </li>
        <li>
            <a href="#tab_cancelados" data-toggle="tab" style="font-weight: 600;">
                <i class="fa fa-ban text-danger"></i> Cancelados <span class="badge bg-red" style="margin-left: 5px;">{{ $cntCancelados }}</span>
            </a>
        </li>
    </ul>
    <div class="tab-content" style="padding: 20px;">
        <!-- Tab 1: Em Andamento -->
        <div class="tab-pane active" id="tab_andamento">
            <div class="row" style="display: flex; flex-wrap: wrap;">
                <div class="col-md-6" style="display: flex; flex-direction: column;">
                    @include('cliente.components.widget-licencasOperacao', ['situacao' => 'andamento'])
                </div>
                <div class="col-md-6" style="display: flex; flex-direction: column;">
                    @include('cliente.components.widget-nRenovaveis', ['situacao' => 'andamento'])
                </div>
            </div>
        </div>

        <!-- Tab 2: Finalizados -->
        <div class="tab-pane" id="tab_finalizados">
            <div class="row" style="display: flex; flex-wrap: wrap;">
                <div class="col-md-6" style="display: flex; flex-direction: column;">
                    @include('cliente.components.widget-licencasOperacao', ['situacao' => 'finalizado'])
                </div>
                <div class="col-md-6" style="display: flex; flex-direction: column;">
                    @include('cliente.components.widget-nRenovaveis', ['situacao' => 'finalizado'])
                </div>
            </div>
        </div>

        <!-- Tab 3: Stand-by -->
        <div class="tab-pane" id="tab_standby">
            <div class="row" style="display: flex; flex-wrap: wrap;">
                <div class="col-md-6" style="display: flex; flex-direction: column;">
                    @include('cliente.components.widget-licencasOperacao', ['situacao' => 'standBy'])
                </div>
                <div class="col-md-6" style="display: flex; flex-direction: column;">
                    @include('cliente.components.widget-nRenovaveis', ['situacao' => 'standBy'])
                </div>
            </div>
        </div>

        <!-- Tab 4: Cancelados -->
        <div class="tab-pane" id="tab_cancelados">
            <div class="row" style="display: flex; flex-wrap: wrap;">
                <div class="col-md-6" style="display: flex; flex-direction: column;">
                    @include('cliente.components.widget-licencasOperacao', ['situacao' => 'cancelado'])
                </div>
                <div class="col-md-6" style="display: flex; flex-direction: column;">
                    @include('cliente.components.widget-nRenovaveis', ['situacao' => 'cancelado'])
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Other widgets (Certidões, Taxas, Facilities) -->
<div class="row">
    @if(count($servicos->where('tipo','controleCertidoes')->where('situacao','<>','arquivado')))
    <div class="col-md-6">
        @include('cliente.components.widget-controleCertidoes')
    </div>
    @endif

    @if(count($servicos->where('tipo','controleTaxas')->where('situacao','<>','arquivado')))
    <div class="col-md-6">
        @include('cliente.components.widget-controleTaxas')
    </div>
    @endif

    @if(count($servicos->where('tipo','facilitiesRealEstate')->where('situacao','<>','arquivado')))
    <div class="col-md-6">
        @include('cliente.components.widget-facilitiesRealEstate')
    </div>
    @endif
</div>

<!-- Serviços Arquivados (Com Paginação Ativa) -->
@if($cntArquivados > 0)
<div class="row">
    <div class="col-md-12" style="margin-top: 10px; margin-bottom: 20px;">
        @include('cliente.components.widget-arquivados')
    </div>
</div>
@endif

@endsection

@section('css')
<style>
    .content-header .breadcrumb { display: none !important; }
    .btn-pill { transition: all 0.2s ease; }
    .btn-pill:hover { transform: translateY(-1px); }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Inicializar DataTables para os arquivos da unidade
    if ($.fn.DataTable) {
        $('.data-table-arquivos').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }
        });

        $('#tabela-widget-licencas').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "order": [[ 3, "asc" ]],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }
        });

        // Paginação ativa para Serviços Arquivados
        $('#servicosArquivadosTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
        });
    }

    // Busca geral live
    $('#search-servicos').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        
        $('table tbody tr').each(function() {
            var $row = $(this);
            var text = $row.text().toLowerCase();
            if (text.indexOf(value) > -1) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    });
});
</script>
@endsection
