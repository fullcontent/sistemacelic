@php
    $todosArquivos = [];
    $tiposNomes = [
        'licencaOperacao' => 'Licença de Operação',
        'nRenovaveis' => 'Licenças/Projetos não renováveis',
        'controleCertidoes' => 'Certidões',
        'controleTaxas' => 'Taxas',
        'facilitiesRealEstate' => 'Facilities/Real Estate',
        'geral' => 'Arquivos Gerais / Sem Serviço específico'
    ];
    $arquivosPorTipoServico = [];

    // 1. Arquivos da tabela 'arquivos'
    if (isset($dados->arquivos)) {
        foreach ($dados->arquivos as $arq) {
            $item = [
                'id' => $arq->id,
                'nome' => $arq->nome,
                'tipo_arquivo' => 'geral',
                'servico_id' => $arq->servico_id,
                'servico_os' => $arq->servico->os ?? null,
                'servico_nome' => $arq->servico->nome ?? null,
                'servico_tipo' => $arq->servico->tipo ?? 'geral',
                'emissao' => $arq->created_at,
                'validade' => null,
                'tipo_licenca' => null,
                'download_url' => route('cliente.arquivo.download', $arq->id),
            ];
            $todosArquivos[] = $item;
            $tipoKey = $arq->servico ? $arq->servico->tipo : 'geral';
            $arquivosPorTipoServico[$tipoKey][] = $item;
        }
    }

    // 2. Anexos diretos dos serviços vinculados
    if (isset($dados->servicos)) {
        foreach ($dados->servicos as $servico) {
            if ($servico->licenca_anexo) {
                $item = [
                    'id' => $servico->id,
                    'nome' => 'Licença: ' . $servico->nome,
                    'tipo_arquivo' => 'licenca',
                    'servico_id' => $servico->id,
                    'servico_os' => $servico->os,
                    'servico_nome' => $servico->nome,
                    'servico_tipo' => $servico->tipo,
                    'emissao' => $servico->created_at,
                    'validade' => $servico->licenca_validade,
                    'tipo_licenca' => ($servico->tipo == 'licencaOperacao') ? 'renovavel' : 'definitiva',
                    'download_url' => route('cliente.servico.downloadFile', ['tipo' => 'licenca', 'servico_id' => $servico->id]),
                ];
                $todosArquivos[] = $item;
                $arquivosPorTipoServico[$servico->tipo][] = $item;
            }
            if ($servico->laudo_anexo) {
                $item = [
                    'id' => $servico->id,
                    'nome' => 'Laudo: ' . $servico->nome,
                    'tipo_arquivo' => 'laudo',
                    'servico_id' => $servico->id,
                    'servico_os' => $servico->os,
                    'servico_nome' => $servico->nome,
                    'servico_tipo' => $servico->tipo,
                    'emissao' => $servico->created_at,
                    'validade' => null,
                    'tipo_licenca' => null,
                    'download_url' => route('cliente.servico.downloadFile', ['tipo' => 'laudo', 'servico_id' => $servico->id]),
                ];
                $todosArquivos[] = $item;
                $arquivosPorTipoServico[$servico->tipo][] = $item;
            }
            if ($servico->protocolo_anexo) {
                $item = [
                    'id' => $servico->id,
                    'nome' => 'Protocolo: ' . $servico->nome,
                    'tipo_arquivo' => 'protocolo',
                    'servico_id' => $servico->id,
                    'servico_os' => $servico->os,
                    'servico_nome' => $servico->nome,
                    'servico_tipo' => $servico->tipo,
                    'emissao' => $servico->created_at,
                    'validade' => null,
                    'tipo_licenca' => null,
                    'download_url' => route('cliente.servico.downloadFile', ['tipo' => 'protocolo', 'servico_id' => $servico->id]),
                ];
                $todosArquivos[] = $item;
                $arquivosPorTipoServico[$servico->tipo][] = $item;
            }
        }
    }

    $licencas = array_filter($todosArquivos, function($arq) {
        return $arq['tipo_arquivo'] === 'licenca';
    });
@endphp

<div class="box box-info" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 25px;">
    <!-- Box Header with Title and Large Upload Button -->
    <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 20px 25px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 class="box-title" style="font-weight: 700; color: #2c3e50; font-size: 18px; margin: 0;">
                <i class="fa fa-folder-open text-info" style="margin-right: 8px;"></i> Arquivos Digitais
            </h3>
            <span style="font-size: 13px; color: #64748b; display: block; margin-top: 4px;">
                Central de downloads e envio de documentos da unidade (Total: {{ count($todosArquivos) }} arquivos)
            </span>
        </div>
        
        <!-- Botão de Upload Grande e Visível -->
        <div>
            <button type="button" class="btn btn-primary btn-lg" style="border-radius: 50px; font-weight: 700; padding: 12px 28px; font-size: 15px; box-shadow: 0 4px 14px rgba(14, 165, 233, 0.35); background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border: none; transition: all 0.2s ease; cursor: pointer;" data-toggle="modal" data-target="#modal-upload-arquivo">
                <i class="fa fa-cloud-upload" style="margin-right: 8px; font-size: 18px;"></i> ENVIAR NOVO ARQUIVO
            </button>
        </div>
    </div>

    <!-- Box Body: Navigation Tabs like arquivos-digitais -->
    <div class="box-body" style="padding: 20px;">
        <div class="nav-tabs-custom" style="box-shadow: none; border-radius: 6px; border: 1px solid #f1f5f9;">
            <ul class="nav nav-tabs" style="border-bottom: 2px solid #e2e8f0; background: #f8fafc; padding-left: 10px;">
                <li class="active"><a href="#widget_tab_geral" data-toggle="tab" style="font-weight: 600;"><i class="fa fa-list text-info"></i> Todos os Arquivos ({{ count($todosArquivos) }})</a></li>
                <li><a href="#widget_tab_tipo" data-toggle="tab" style="font-weight: 600;"><i class="fa fa-tags text-warning"></i> Por Tipo de Serviço</a></li>
                <li><a href="#widget_tab_licencas" data-toggle="tab" style="font-weight: 600;"><i class="fa fa-certificate text-success"></i> Licenças & Validades ({{ count($licencas) }})</a></li>
            </ul>
            <div class="tab-content" style="padding: 20px; background: #fff;">
                
                <!-- TAB 1: TODOS OS ARQUIVOS -->
                <div class="tab-pane active" id="widget_tab_geral">
                    @if(count($todosArquivos) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover data-table-arquivos" style="width: 100%;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                        <th style="font-weight: 600; color: #475569;">Nome do Arquivo</th>
                                        <th style="font-weight: 600; color: #475569;">Categoria</th>
                                        <th style="font-weight: 600; color: #475569;">Serviço / O.S.</th>
                                        <th style="font-weight: 600; color: #475569;">Data Emissão</th>
                                        <th style="font-weight: 600; color: #475569;">Validade</th>
                                        <th style="width: 110px; font-weight: 600; color: #475569; text-align: center;">Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todosArquivos as $arq)
                                        <tr>
                                            <td style="font-weight: 600; color: #1e293b;">
                                                <i class="fa {{ $arq['tipo_arquivo'] == 'licenca' ? 'fa-certificate text-warning' : 'fa-file-text-o text-muted' }}" style="margin-right: 6px;"></i> 
                                                {{ $arq['nome'] }}
                                            </td>
                                            <td>
                                                @switch($arq['tipo_arquivo'])
                                                    @case('licenca')
                                                        <span class="label label-warning" style="border-radius: 4px;">Licença</span>
                                                        @break
                                                    @case('laudo')
                                                        <span class="label label-primary" style="border-radius: 4px;">Laudo</span>
                                                        @break
                                                    @case('protocolo')
                                                        <span class="label label-info" style="border-radius: 4px;">Protocolo</span>
                                                        @break
                                                    @default
                                                        <span class="label label-default" style="border-radius: 4px;">Geral</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($arq['servico_id'])
                                                    <a href="{{ route('cliente.servico.show', $arq['servico_id']) }}" style="color: #2563eb; font-weight: 500;">
                                                        <strong>{{ $arq['servico_os'] }}</strong> {{ $arq['servico_nome'] ? '- ' . $arq['servico_nome'] : '' }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td style="color: #64748b;">
                                                {{ $arq['emissao'] ? \Carbon\Carbon::parse($arq['emissao'])->format('d/m/Y') : 'N/A' }}
                                            </td>
                                            <td>
                                                @if($arq['tipo_arquivo'] == 'licenca' && $arq['tipo_licenca'] == 'renovavel' && $arq['validade'])
                                                    {{ \Carbon\Carbon::parse($arq['validade'])->format('d/m/Y') }}
                                                    @if(\Carbon\Carbon::parse($arq['validade'])->isPast())
                                                        <span class="label label-danger pull-right" style="border-radius: 4px; margin-left: 5px;">Vencido</span>
                                                    @else
                                                        <span class="label label-success pull-right" style="border-radius: 4px; margin-left: 5px;">Vigente</span>
                                                    @endif
                                                @elseif($arq['tipo_arquivo'] == 'licenca' && $arq['tipo_licenca'] == 'definitiva')
                                                    <span class="label label-success" style="border-radius: 4px;">Definitiva</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                <a href="{{ $arq['download_url'] }}" class="btn btn-pill" style="background: #2563eb; color: #fff; border-radius: 50px; padding: 5px 14px; font-weight: 600; font-size: 12px; text-decoration: none; display: inline-block;">
                                                    <i class="fa fa-download" style="margin-right: 4px;"></i> Baixar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted" style="padding: 25px;">
                            <i class="fa fa-folder-open-o" style="font-size: 24px; display: block; margin-bottom: 8px; color: #cbd5e1;"></i>
                            Nenhum arquivo digital disponível.
                        </div>
                    @endif
                </div>

                <!-- TAB 2: POR TIPO DE SERVIÇO -->
                <div class="tab-pane" id="widget_tab_tipo">
                    <div class="box-group" id="accordion-widget-tipos">
                        @php $isFirstTipo = true; @endphp
                        @foreach($tiposNomes as $tipoKey => $tipoNome)
                            @php 
                                $arqsDoTipo = isset($arquivosPorTipoServico[$tipoKey]) ? $arquivosPorTipoServico[$tipoKey] : [];
                            @endphp
                            <div class="panel box box-default" style="margin-bottom: 10px; border-radius: 6px; border: 1px solid #e2e8f0; box-shadow: none;">
                                <div class="box-header with-border" style="background: #f8fafc; padding: 12px 15px;">
                                    <h4 class="box-title" style="width: 100%; margin: 0;">
                                        <a data-toggle="collapse" data-parent="#accordion-widget-tipos" href="#collapse-widget-tipo-{{$tipoKey}}" style="display: flex; justify-content: space-between; align-items: center; width: 100%; text-decoration: none; color: #334155; font-weight: 600; font-size: 14px;">
                                            <span><i class="fa fa-tags text-warning" style="margin-right: 8px;"></i> {{ $tipoNome }}</span>
                                            <span class="badge bg-orange">{{ count($arqsDoTipo) }}</span>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse-widget-tipo-{{$tipoKey}}" class="panel-collapse collapse {{ $isFirstTipo && count($arqsDoTipo) > 0 ? 'in' : '' }}">
                                    <div class="box-body" style="padding: 15px;">
                                        @if(count($arqsDoTipo) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-hover data-table-arquivos" style="width: 100%;">
                                                    <thead>
                                                        <tr style="background: #f8fafc;">
                                                            <th>Nome do Arquivo</th>
                                                            <th>Categoria</th>
                                                            <th>Serviço / O.S.</th>
                                                            <th>Data Emissão</th>
                                                            <th style="width: 110px; text-align: center;">Download</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($arqsDoTipo as $arq)
                                                            <tr>
                                                                <td style="font-weight: 600; color: #1e293b;">{{ $arq['nome'] }}</td>
                                                                <td><span class="label label-default" style="border-radius: 4px;">{{ ucfirst($arq['tipo_arquivo']) }}</span></td>
                                                                <td>{{ $arq['servico_os'] ?? 'N/A' }}</td>
                                                                <td>{{ $arq['emissao'] ? \Carbon\Carbon::parse($arq['emissao'])->format('d/m/Y') : '-' }}</td>
                                                                <td style="text-align: center;">
                                                                    <a href="{{ $arq['download_url'] }}" class="btn btn-pill" style="background: #2563eb; color: #fff; border-radius: 50px; padding: 4px 12px; font-weight: 600; font-size: 12px; text-decoration: none;">
                                                                        <i class="fa fa-download"></i> Baixar
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <span class="text-muted">Nenhum arquivo nesta categoria.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @php if(count($arqsDoTipo) > 0) $isFirstTipo = false; @endphp
                        @endforeach
                    </div>
                </div>

                <!-- TAB 3: LICENÇAS & VALIDADES -->
                <div class="tab-pane" id="widget_tab_licencas">
                    @if(count($licencas) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="tabela-widget-licencas" style="width: 100%;">
                                <thead>
                                    <tr style="background: #f8fafc;">
                                        <th>Licença</th>
                                        <th>Serviço / O.S.</th>
                                        <th>Data Emissão</th>
                                        <th>Data Validade</th>
                                        <th>Situação</th>
                                        <th style="width: 110px; text-align: center;">Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licencas as $arq)
                                        <tr>
                                            <td style="font-weight: 600; color: #1e293b;">{{ $arq['nome'] }}</td>
                                            <td>{{ $arq['servico_os'] ?? 'N/A' }}</td>
                                            <td>{{ $arq['emissao'] ? \Carbon\Carbon::parse($arq['emissao'])->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $arq['validade'] ? \Carbon\Carbon::parse($arq['validade'])->format('d/m/Y') : 'Indefinida' }}</td>
                                            <td>
                                                @if($arq['validade'])
                                                    @if(\Carbon\Carbon::parse($arq['validade'])->isPast())
                                                        <span class="label label-danger" style="border-radius: 4px;">Vencido</span>
                                                    @else
                                                        <span class="label label-success" style="border-radius: 4px;">Vigente</span>
                                                    @endif
                                                @else
                                                    <span class="label label-info" style="border-radius: 4px;">Definitiva</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                <a href="{{ $arq['download_url'] }}" class="btn btn-pill" style="background: #2563eb; color: #fff; border-radius: 50px; padding: 4px 12px; font-weight: 600; font-size: 12px; text-decoration: none;">
                                                    <i class="fa fa-download"></i> Baixar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted" style="padding: 25px;">
                            Nenhuma licença com validade registrada para esta unidade.
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Upload de Arquivo -->
<div class="modal fade" id="modal-upload-arquivo" tabindex="-1" role="dialog" aria-labelledby="modalUploadLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #fff; padding: 18px 25px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.9;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalUploadLabel" style="font-weight: 700; font-size: 18px;"><i class="fa fa-cloud-upload" style="margin-right: 8px;"></i> Enviar Novo Arquivo Digital</h4>
            </div>
            {!! Form::open(['route' => 'cliente.arquivo.upload', 'enctype' => 'multipart/form-data']) !!}
            <div class="modal-body" style="padding: 25px;">
                <div class="form-group" style="margin-bottom: 20px;">
                    {!! Form::label('nome', 'Nome / Descrição do Arquivo', ['class' => 'control-label', 'style' => 'font-weight: 600; color: #334155; margin-bottom: 6px;']) !!}
                    {!! Form::text('nome', null, ['class' => 'form-control input-lg', 'required', 'placeholder' => 'Ex: Planta Baixa, Contrato Social, Habite-se...', 'style' => 'border-radius: 6px; font-size: 14px;']) !!}
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    {!! Form::label('arquivo', 'Selecione o Arquivo (PDF, Imagem, Doc)', ['class' => 'control-label', 'style' => 'font-weight: 600; color: #334155; margin-bottom: 6px;']) !!}
                    {!! Form::file('arquivo', ['class' => 'form-control input-lg', 'required', 'style' => 'border-radius: 6px; padding: 8px;']) !!}
                </div>

                @if(isset($dados) && isset($dados->id))
                    @if(request()->routeIs('cliente.empresa.show'))
                        {!! Form::hidden('empresa_id', $dados->id) !!}
                    @else
                        {!! Form::hidden('unidade_id', $dados->id) !!}
                    @endif
                @endif
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 15px 25px;">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="border-radius: 50px; font-weight: 600; padding: 8px 20px;">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border: none; padding: 8px 25px; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3);">
                    <i class="fa fa-upload" style="margin-right: 6px;"></i> Cadastrar Arquivo
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>