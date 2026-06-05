@extends('adminlte::page')

@section('title', 'Arquivos Digitais')

@section('css')
    <!-- Select2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Modern Select2 Styling */
        .select2-container .select2-selection--single {
            height: 40px !important;
            border-radius: 4px;
            border: 1px solid #d2d6de;
            padding-left: 8px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            color: #444;
            font-size: 14px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }
        .select2-dropdown {
            border: 1px solid #3c8dbc !important;
            border-radius: 0 0 4px 4px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Layout & Tabs Optimization */
        .nav-tabs-custom {
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-radius: 6px;
            overflow: hidden;
            border-top: 3px solid #3c8dbc;
        }
        .nav-tabs-custom > .nav-tabs > li.active {
            border-top-color: transparent;
        }
        .nav-tabs-custom > .nav-tabs > li.active > a {
            font-weight: bold;
            border-left-color: #eee;
            border-right-color: #eee;
        }
        
        /* Table and Row Hover Effects */
        .table-hover tbody tr {
            transition: background-color 0.2s ease;
        }
        .table-hover tbody tr:hover {
            background-color: #f7fafc !important;
        }

        /* Action Buttons styling */
        .btn-download-file {
            transition: all 0.2s ease;
            font-weight: 500;
        }
        .btn-download-file:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Label Badges */
        .label-badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 3px;
            text-transform: uppercase;
        }
    </style>
@stop

@section('content_header')
    <h1><i class="fa fa-folder-open text-primary"></i> Arquivos Digitais <small>Central de downloads de documentos e licenças</small></h1>
@stop

@section('content')

    <!-- Filtro por Unidade -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" style="border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <div class="box-body" style="padding: 20px;">
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="select-unidade" style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px; display: block;">
                                    <i class="fa fa-building text-primary"></i> Selecione a Unidade (Filial):
                                </label>
                                <select id="select-unidade" class="form-control select2" style="width: 100%;">
                                    @if(count($unidades) > 1 && !$selectedUnit)
                                        <option value="" selected disabled>-- Selecione uma unidade para ver os arquivos --</option>
                                    @endif
                                    @foreach($unidades as $u)
                                        <option value="{{ $u->id }}" {{ $selectedUnit && $selectedUnit->id == $u->id ? 'selected' : '' }}>
                                            [{{ $u->codigo ?? 'S/C' }}] - {{ $u->nomeFantasia ?? $u->razaoSocial }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12" style="margin-top: 25px;">
                            @if($selectedUnit)
                                <div class="text-muted" style="font-size: 13px;">
                                    <i class="fa fa-info-circle text-info"></i> Encontrados <strong>{{ count($todosArquivos) }}</strong> arquivos para esta unidade.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($selectedUnit)
        <div class="row">
            <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_geral" data-toggle="tab"><i class="fa fa-list"></i> Todos os Arquivos</a></li>
                        <li><a href="#tab_tipo" data-toggle="tab"><i class="fa fa-tags"></i> Por Tipo de Serviço</a></li>
                        <li><a href="#tab_licencas" data-toggle="tab"><i class="fa fa-certificate"></i> Licenças & Validades</a></li>
                    </ul>
                    <div class="tab-content" style="padding: 20px;">
                        
                        <!-- TAB 1: TODOS OS ARQUIVOS -->
                        <div class="tab-pane active" id="tab_geral">
                            <p class="text-muted" style="margin-bottom: 20px;">Listagem completa de todos os documentos anexos e arquivos gerais disponíveis para a unidade **[{{ $selectedUnit->codigo }}] {{ $selectedUnit->nomeFantasia }}**.</p>
                            
                            @if(count($todosArquivos) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover data-table-arquivos" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Nome do Arquivo</th>
                                                <th>Categoria</th>
                                                <th>Serviço / O.S.</th>
                                                <th>Data Emissão</th>
                                                <th>Validade</th>
                                                <th style="width: 110px; text-align: center;">Download</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($todosArquivos as $arq)
                                                <tr>
                                                    <td>
                                                        <i class="fa {{ $arq['tipo_arquivo'] == 'licenca' ? 'fa-certificate text-yellow' : 'fa-file text-muted' }}" style="margin-right: 6px;"></i> 
                                                        <strong>{{ $arq['nome'] }}</strong>
                                                    </td>
                                                    <td>
                                                        @switch($arq['tipo_arquivo'])
                                                            @case('licenca')
                                                                <span class="label label-warning">Licença</span>
                                                                @break
                                                            @case('laudo')
                                                                <span class="label label-primary">Laudo</span>
                                                                @break
                                                            @case('protocolo')
                                                                <span class="label label-info">Protocolo</span>
                                                                @break
                                                            @default
                                                                <span class="label label-default">Geral</span>
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        @if($arq['servico_id'])
                                                            <a href="{{ route('cliente.servico.show', $arq['servico_id']) }}" target="_blank">
                                                                <strong>{{ $arq['servico_os'] }}</strong> - {{ $arq['servico_nome'] }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $arq['emissao'] ? \Carbon\Carbon::parse($arq['emissao'])->format('d/m/Y') : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        @if($arq['tipo_arquivo'] == 'licenca' && $arq['tipo_licenca'] == 'renovavel' && $arq['validade'])
                                                            {{ \Carbon\Carbon::parse($arq['validade'])->format('d/m/Y') }}
                                                            @if(\Carbon\Carbon::parse($arq['validade'])->isPast())
                                                                <span class="label label-danger pull-right" style="margin-left: 5px;">Vencido</span>
                                                            @else
                                                                <span class="label label-success pull-right" style="margin-left: 5px;">Vigente</span>
                                                            @endif
                                                        @elseif($arq['tipo_arquivo'] == 'licenca' && $arq['tipo_licenca'] == 'definitiva')
                                                            <span class="label label-success">Definitiva</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <a href="{{ $arq['download_url'] }}" class="btn btn-success btn-xs btn-download-file" title="Download">
                                                            <i class="fa fa-download"></i> Baixar
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info" style="margin-bottom: 0;">
                                    <i class="icon fa fa-info"></i> Nenhum arquivo digital disponível para esta unidade.
                                </div>
                            @endif
                        </div>
                        <!-- /TAB 1 -->

                        <!-- TAB 2: POR TIPO DE SERVIÇO -->
                        <div class="tab-pane" id="tab_tipo">
                            <p class="text-muted" style="margin-bottom: 20px;">Documentos agrupados pelas categorias e áreas de serviços vinculados.</p>
                            
                            <div class="box-group" id="accordion-tipos">
                                @php $isFirstTipo = true; @endphp
                                @foreach($tiposNomes as $tipoKey => $tipoNome)
                                    @php 
                                        $arqsDoTipo = isset($arquivosPorTipoServico[$tipoKey]) ? $arquivosPorTipoServico[$tipoKey] : [];
                                    @endphp
                                    <div class="panel box box-default" style="margin-bottom: 10px; border-radius: 4px; box-shadow: none; border: 1px solid #f4f4f4;">
                                        <div class="box-header with-border" style="background-color: #fafafa;">
                                            <h4 class="box-title" style="width: 100%;">
                                                <a data-toggle="collapse" data-parent="#accordion-tipos" href="#collapse-tipo-{{$tipoKey}}" style="display: block; width: 100%; text-decoration: none; padding: 10px 15px;">
                                                    <i class="fa fa-tags text-orange" style="margin-right: 6px;"></i> 
                                                    <strong>{{ $tipoNome }}</strong>
                                                    <span class="badge bg-orange pull-right">{{ count($arqsDoTipo) }}</span>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse-tipo-{{$tipoKey}}" class="panel-collapse collapse {{ $isFirstTipo && count($arqsDoTipo) > 0 ? 'in' : '' }}">
                                            <div class="box-body" style="padding: 15px;">
                                                @if(count($arqsDoTipo) > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped table-hover data-table-arquivos" style="width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Nome do Arquivo</th>
                                                                    <th>Categoria</th>
                                                                    <th>Serviço / O.S.</th>
                                                                    <th>Data Emissão</th>
                                                                    <th>Validade</th>
                                                                    <th style="width: 110px; text-align: center;">Download</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($arqsDoTipo as $arq)
                                                                    <tr>
                                                                        <td>
                                                                            <i class="fa {{ $arq['tipo_arquivo'] == 'licenca' ? 'fa-certificate text-yellow' : 'fa-file text-muted' }}" style="margin-right: 6px;"></i> 
                                                                            <strong>{{ $arq['nome'] }}</strong>
                                                                        </td>
                                                                        <td>
                                                                            @switch($arq['tipo_arquivo'])
                                                                                @case('licenca')
                                                                                    <span class="label label-warning">Licença</span>
                                                                                    @break
                                                                                @case('laudo')
                                                                                    <span class="label label-primary">Laudo</span>
                                                                                    @break
                                                                                @case('protocolo')
                                                                                    <span class="label label-info">Protocolo</span>
                                                                                    @break
                                                                                @default
                                                                                    <span class="label label-default">Geral</span>
                                                                            @endswitch
                                                                        </td>
                                                                        <td>
                                                                            @if($arq['servico_id'])
                                                                                <a href="{{ route('cliente.servico.show', $arq['servico_id']) }}" target="_blank">
                                                                                    <strong>{{ $arq['servico_os'] }}</strong> - {{ $arq['servico_nome'] }}
                                                                                </a>
                                                                            @else
                                                                                <span class="text-muted">N/A</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            {{ $arq['emissao'] ? \Carbon\Carbon::parse($arq['emissao'])->format('d/m/Y') : 'N/A' }}
                                                                        </td>
                                                                        <td>
                                                                            @if($arq['tipo_arquivo'] == 'licenca' && $arq['tipo_licenca'] == 'renovavel' && $arq['validade'])
                                                                                {{ \Carbon\Carbon::parse($arq['validade'])->format('d/m/Y') }}
                                                                                @if(\Carbon\Carbon::parse($arq['validade'])->isPast())
                                                                                    <span class="label label-danger pull-right">Vencido</span>
                                                                                @else
                                                                                    <span class="label label-success pull-right">Vigente</span>
                                                                                @endif
                                                                            @elseif($arq['tipo_arquivo'] == 'licenca' && $arq['tipo_licenca'] == 'definitiva')
                                                                                <span class="label label-success">Definitiva</span>
                                                                            @else
                                                                                <span class="text-muted">-</span>
                                                                            @endif
                                                                        </td>
                                                                        <td style="text-align: center;">
                                                                            <a href="{{ $arq['download_url'] }}" class="btn btn-success btn-xs btn-download-file" title="Download">
                                                                                <i class="fa fa-download"></i> Baixar
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="alert alert-info" style="margin-bottom: 0;">
                                                        <i class="icon fa fa-info"></i> Nenhum arquivo deste tipo de serviço disponível.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @php $isFirstTipo = false; @endphp
                                @endforeach
                            </div>
                        </div>
                        <!-- /TAB 2 -->

                        <!-- TAB 3: LICENÇAS -->
                        <div class="tab-pane" id="tab_licencas">
                            <p class="text-muted" style="margin-bottom: 20px;">Exibição detalhada de **Licenças Operacionais e Alvarás**, organizados por data de vencimento.</p>
                            
                            @if(count($licencas) > 0)
                                <div class="table-responsive">
                                    <table id="tabela-licencas-vigencia" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Nome da Licença</th>
                                                <th>Tipo da Licença</th>
                                                <th>Data Emissão</th>
                                                <th>Data Validade</th>
                                                <th>Status de Vigência</th>
                                                <th style="width: 110px; text-align: center;">Download</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($licencas as $arq)
                                                <tr>
                                                    <td>
                                                        <i class="fa fa-certificate text-yellow" style="margin-right: 6px;"></i> 
                                                        <strong>{{ $arq['nome'] }}</strong>
                                                    </td>
                                                    <td>
                                                        @switch($arq['tipo_licenca'])
                                                            @case('renovavel')
                                                                Renovável
                                                                @break
                                                            @case('definitiva')
                                                                Definitiva
                                                                @break
                                                            @default
                                                                Não Especificado
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        {{ $arq['emissao'] ? \Carbon\Carbon::parse($arq['emissao'])->format('d/m/Y') : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        {{ ($arq['tipo_licenca'] == 'renovavel' && $arq['validade']) ? \Carbon\Carbon::parse($arq['validade'])->format('d/m/Y') : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        @if($arq['tipo_licenca'] == 'renovavel' && $arq['validade'])
                                                            @if(\Carbon\Carbon::parse($arq['validade'])->isPast())
                                                                <span class="label label-danger"><i class="fa fa-exclamation-triangle"></i> Vencido</span>
                                                            @else
                                                                <span class="label label-success"><i class="fa fa-check"></i> Vigente</span>
                                                            @endif
                                                        @elseif($arq['tipo_licenca'] == 'definitiva')
                                                            <span class="label label-success"><i class="fa fa-check-circle"></i> Definitiva</span>
                                                        @else
                                                            <span class="label label-default">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <a href="{{ $arq['download_url'] }}" class="btn btn-success btn-xs btn-download-file">
                                                            <i class="fa fa-download"></i> Baixar
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info" style="margin-bottom: 0;">
                                    <i class="icon fa fa-info"></i> Nenhuma licença ou alvará operacional com anexo encontrado para esta unidade.
                                </div>
                            @endif
                        </div>
                        <!-- /TAB 3 -->

                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
            </div>
        </div>
    @else
        <!-- Unidade não selecionada -->
        <div class="row">
            <div class="col-md-12">
                <div class="callout callout-info" style="border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-left: 5px solid #00c0ef;">
                    <h4><i class="icon fa fa-info-circle"></i> Nenhuma unidade selecionada</h4>
                    <p>Por favor, selecione uma unidade no filtro acima para visualizar seus respectivos arquivos digitais e realizar downloads.</p>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('js')
    <!-- Select2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {
            // Inicializar Select2
            $('#select-unidade').select2({
                placeholder: "Selecione uma unidade...",
                allowClear: false
            }).on('change', function() {
                var unitId = $(this).val();
                if (unitId) {
                    window.location.href = '{{ route("cliente.arquivos") }}?unidade_id=' + unitId;
                }
            });

            // Inicializar DataTables para as tabelas gerais
            $('.data-table-arquivos').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [[ 0, "asc" ]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
                }
            });

            // Inicializar DataTable específico para licenças ordenado por validade (data mais próxima a vencer primeiro)
            $('#tabela-licencas-vigencia').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [[ 3, "asc" ]], // Ordena por validade
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
                }
            });

            // Forçar ajuste das colunas do DataTable quando mudar de aba
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            });
        });
    </script>
@stop
