@extends('adminlte::page')

@php
    if (!function_exists('cleanObsText')) {
        function cleanObsText($text) {
            if (empty($text)) return '';
            $clean = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $clean = str_replace(["\xc2\xa0", '&nbsp;'], ' ', $clean);
            $clean = preg_replace('/<br\s*\/?>/i', "\n", $clean);
            $clean = preg_replace('/<\/p>/i', "\n", $clean);
            $clean = strip_tags($clean);
            $clean = str_replace(["\r\n", "\r"], "\n", $clean);
            $lines = array_map('trim', explode("\n", $clean));
            $filteredLines = [];
            $prevEmpty = false;
            foreach ($lines as $line) {
                if ($line === '') {
                    if (!$prevEmpty) {
                        $filteredLines[] = '';
                        $prevEmpty = true;
                    }
                } else {
                    $filteredLines[] = $line;
                    $prevEmpty = false;
                }
            }
            return trim(implode("\n", $filteredLines));
        }
    }

    $tipoMap = [
        'nRenovaveis' => 'Licenças/Projetos não renováveis',
        'licencaOperacao' => 'Licença de Operação',
        'controleCertidoes' => 'Certidões',
        'controleTaxas' => 'Taxas',
        'facilitiesRealEstate' => 'Facilities/Real Estate',
    ];
    $tipoLabel = isset($tipoMap[$servico->tipo]) ? $tipoMap[$servico->tipo] : $servico->tipo;

    $situacaoMap = [
        'andamento' => 'Em Andamento',
        'finalizado' => 'Finalizado',
        'arquivado' => 'Arquivado',
        'standBy' => 'Stand By',
        'nRenovado' => 'Não Renovado',
        'cancelado' => 'Cancelado',
    ];
    $situacaoLabel = isset($situacaoMap[$servico->situacao]) ? $situacaoMap[$servico->situacao] : ucfirst($servico->situacao);

    $situacaoBgMap = [
        'andamento' => '#0ea5e9',
        'finalizado' => '#10b981',
        'standBy' => '#f59e0b',
        'cancelado' => '#ef4444',
    ];
    $situacaoBg = isset($situacaoBgMap[$servico->situacao]) ? $situacaoBgMap[$servico->situacao] : '#64748b';

    $isLicencaVencida = ($servico->tipo == 'licencaOperacao' && !empty($servico->licenca_validade) && $servico->licenca_validade < date('Y-m-d'));

    $cntPendenciasAbertas = count($pendencias->where('status', 'pendente'));
    $cntTaxas = count($taxas);
    $cntDocsDisponiveis = (!empty($servico->licenca_anexo) ? 1 : 0) + (!empty($servico->laudo_anexo) ? 1 : 0) + (!empty($servico->protocolo_anexo) ? 1 : 0);
@endphp

@section('content_header')
    <!-- Dashboard Header Hero Banner -->
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 12px; padding: 25px 30px; color: #fff; margin-bottom: 25px; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <span class="label" style="background: rgba(255,255,255,0.15); color: #38bdf8; font-size: 13px; font-weight: 700; padding: 5px 12px; border-radius: 50px; text-transform: uppercase;">
                    OS {{ $servico->os }}
                </span>
                <span style="color: #94a3b8; font-size: 13px;">| {{ $tipoLabel }}</span>
            </div>
            <h1 style="font-weight: 800; font-size: 26px; margin: 0; color: #fff; line-height: 1.2;">
                {{ $servico->nome }}
            </h1>
            <div style="margin-top: 10px; font-size: 14px; color: #cbd5e1; display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <span><i class="fa fa-building-o text-info" style="margin-right: 6px;"></i> <strong>{{ $dados->nomeFantasia ?? '' }}</strong> {{ !empty($dados->codigo) ? '(Cód: ' . $dados->codigo . ')' : '' }}</span>
                <span><i class="fa fa-user-circle-o text-warning" style="margin-right: 6px;"></i> Resp. Castro: <strong>{{ $servico->responsavel->name ?? 'N/A' }}</strong></span>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <span class="label" style="background: {{ $situacaoBg }}; color: #fff; font-size: 13px; font-weight: 700; border-radius: 50px; padding: 8px 20px; text-transform: uppercase; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                {{ $situacaoLabel }}
            </span>
            @if($servico->tipo == 'licencaOperacao' && !empty($servico->licenca_validade))
                @if($isLicencaVencida)
                    <span class="label label-danger" style="font-size: 13px; font-weight: 700; border-radius: 50px; padding: 8px 18px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                        <i class="fa fa-exclamation-triangle"></i> Licença Vencida
                    </span>
                @else
                    <span class="label label-success" style="font-size: 13px; font-weight: 700; border-radius: 50px; padding: 8px 18px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                        <i class="fa fa-check-circle"></i> Licença Vigente
                    </span>
                @endif
            @endif
        </div>
    </div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible" style="border-radius: 8px;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> Sucesso!</h4>
        {{ session('success') }}
    </div>
@endif

<!-- Metric KPI Cards Row -->
<div class="row" style="margin-bottom: 25px;">
    <!-- KPI 1: Status & Categoria -->
    <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
        <div style="background: #fff; border-radius: 10px; padding: 20px; border: 1px solid #ebf0f5; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <i class="fa fa-tachometer"></i>
            </div>
            <div>
                <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Situação Atual</span>
                <h4 style="margin: 2px 0 0 0; font-weight: 800; color: #1e293b; font-size: 18px;">{{ $situacaoLabel }}</h4>
            </div>
        </div>
    </div>

    <!-- KPI 2: Validade da Licença -->
    <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
        <div style="background: #fff; border-radius: 10px; padding: 20px; border: 1px solid #ebf0f5; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; border-radius: 10px; background: {{ $isLicencaVencida ? '#fef2f2' : '#f0fdf4' }}; color: {{ $isLicencaVencida ? '#ef4444' : '#10b981' }}; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <i class="fa fa-calendar-check-o"></i>
            </div>
            <div>
                <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Validade Licença</span>
                <h4 style="margin: 2px 0 0 0; font-weight: 800; color: #1e293b; font-size: 16px;">
                    @if(!empty($servico->licenca_validade) && $servico->tipoLicenca == 'renovavel')
                        {{ \Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y') }}
                    @elseif($servico->tipoLicenca == 'definitiva')
                        Definitiva
                    @else
                        Indefinida
                    @endif
                </h4>
            </div>
        </div>
    </div>

    <!-- KPI 3: Pendências Ativas -->
    <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
        <div style="background: #fff; border-radius: 10px; padding: 20px; border: 1px solid #ebf0f5; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; border-radius: 10px; background: {{ $cntPendenciasAbertas > 0 ? '#fffbebf1' : '#f8fafc' }}; color: {{ $cntPendenciasAbertas > 0 ? '#f59e0b' : '#64748b' }}; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <i class="fa fa-exclamation-circle"></i>
            </div>
            <div>
                <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Pendências Ativas</span>
                <h4 style="margin: 2px 0 0 0; font-weight: 800; color: #1e293b; font-size: 18px;">
                    {{ $cntPendenciasAbertas }} {{ $cntPendenciasAbertas == 1 ? 'item' : 'itens' }}
                </h4>
            </div>
        </div>
    </div>

    <!-- KPI 4: Documentos Disponíveis -->
    <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
        <div style="background: #fff; border-radius: 10px; padding: 20px; border: 1px solid #ebf0f5; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; border-radius: 10px; background: #f3e8ff; color: #a855f7; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <i class="fa fa-folder-open"></i>
            </div>
            <div>
                <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Central Documentos</span>
                <h4 style="margin: 2px 0 0 0; font-weight: 800; color: #1e293b; font-size: 18px;">
                    {{ $cntDocsDisponiveis }} {{ $cntDocsDisponiveis == 1 ? 'arquivo' : 'arquivos' }}
                </h4>
            </div>
        </div>
    </div>
</div>

<!-- Empresa / Unidade Card Component -->
<div class="row">
    <div class="col-md-12">
        @include('cliente.components.widget-detalhes')
    </div>
</div>

<!-- Central de Documentos & Downloads (Hub de Documentos) -->
<div class="box box-primary" style="border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 25px;">
    <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 20px 25px;">
        <h3 class="box-title" style="font-weight: 700; color: #1e293b; font-size: 17px; margin: 0;">
            <i class="fa fa-cloud-download text-info" style="margin-right: 8px;"></i> Central de Documentos Emitidos
        </h3>
    </div>
    <div class="box-body" style="padding: 25px;">
        <div class="row" style="display: flex; flex-wrap: wrap; row-gap: 20px;">
            <!-- Document 1: Licença de Operação -->
            <div class="col-md-4 col-sm-6">
                <div style="background: #f8fafc; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0; height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-weight: 700; color: #334155; font-size: 15px;"><i class="fa fa-certificate text-warning" style="margin-right: 6px;"></i> Licença</span>
                            @if(!empty($servico->licenca_anexo))
                                <span class="label label-success" style="border-radius: 4px;">Disponível</span>
                            @else
                                <span class="label label-default" style="border-radius: 4px;">Pendente</span>
                            @endif
                        </div>
                        <div style="font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 15px;">
                            <div><strong>Emissão:</strong> {{ !empty($servico->licenca_emissao) ? \Carbon\Carbon::parse($servico->licenca_emissao)->format('d/m/Y') : '-' }}</div>
                            <div><strong>Validade:</strong> {{ !empty($servico->licenca_validade) ? \Carbon\Carbon::parse($servico->licenca_validade)->format('d/m/Y') : 'Definitiva / N/A' }}</div>
                        </div>
                    </div>
                    @if(!empty($servico->licenca_anexo))
                        <a href="{{ route('cliente.servico.downloadFile', ['tipo' => 'licenca', 'servico_id' => $servico->id]) }}" class="btn btn-pill" style="background: #f59e0b; color: #fff; border-radius: 50px; font-weight: 700; width: 100%; text-align: center; padding: 8px; text-decoration: none; display: block; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.25);">
                            <i class="fa fa-download" style="margin-right: 6px;"></i> Baixar Licença
                        </a>
                    @else
                        <button class="btn btn-default disabled btn-pill" style="width: 100%; border-radius: 50px; padding: 8px;" disabled>Aguardando Emissão</button>
                    @endif
                </div>
            </div>

            <!-- Document 2: Laudo Técnico -->
            <div class="col-md-4 col-sm-6">
                <div style="background: #f8fafc; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0; height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-weight: 700; color: #334155; font-size: 15px;"><i class="fa fa-file-text-o text-primary" style="margin-right: 6px;"></i> Laudo Técnico</span>
                            @if(!empty($servico->laudo_anexo))
                                <span class="label label-success" style="border-radius: 4px;">Disponível</span>
                            @else
                                <span class="label label-default" style="border-radius: 4px;">Pendente</span>
                            @endif
                        </div>
                        <div style="font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 15px;">
                            <div><strong>Nº do Laudo:</strong> {{ $servico->laudo_numero ?? '-' }}</div>
                            <div><strong>Emissão:</strong> {{ !empty($servico->laudo_emissao) ? \Carbon\Carbon::parse($servico->laudo_emissao)->format('d/m/Y') : '-' }}</div>
                        </div>
                    </div>
                    @if(!empty($servico->laudo_anexo))
                        <a href="{{ route('cliente.servico.downloadFile', ['tipo' => 'laudo', 'servico_id' => $servico->id]) }}" class="btn btn-pill" style="background: #2563eb; color: #fff; border-radius: 50px; font-weight: 700; width: 100%; text-align: center; padding: 8px; text-decoration: none; display: block; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);">
                            <i class="fa fa-download" style="margin-right: 6px;"></i> Baixar Laudo
                        </a>
                    @else
                        <button class="btn btn-default disabled btn-pill" style="width: 100%; border-radius: 50px; padding: 8px;" disabled>Aguardando Emissão</button>
                    @endif
                </div>
            </div>

            <!-- Document 3: Protocolo de Entrada -->
            <div class="col-md-4 col-sm-12">
                <div style="background: #f8fafc; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0; height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-weight: 700; color: #334155; font-size: 15px;"><i class="fa fa-file-pdf-o text-info" style="margin-right: 6px;"></i> Protocolo</span>
                            @if(!empty($servico->protocolo_anexo))
                                <span class="label label-success" style="border-radius: 4px;">Disponível</span>
                            @else
                                <span class="label label-default" style="border-radius: 4px;">Pendente</span>
                            @endif
                        </div>
                        <div style="font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 15px;">
                            <div><strong>Nº Protocolo:</strong> {{ $servico->protocolo_numero ?? '-' }}</div>
                            <div><strong>Emissão:</strong> {{ !empty($servico->protocolo_emissao) ? \Carbon\Carbon::parse($servico->protocolo_emissao)->format('d/m/Y') : '-' }}</div>
                        </div>
                    </div>
                    @if(!empty($servico->protocolo_anexo))
                        <a href="{{ route('cliente.servico.downloadFile', ['tipo' => 'protocolo', 'servico_id' => $servico->id]) }}" class="btn btn-pill" style="background: #0ea5e9; color: #fff; border-radius: 50px; font-weight: 700; width: 100%; text-align: center; padding: 8px; text-decoration: none; display: block; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.25);">
                            <i class="fa fa-download" style="margin-right: 6px;"></i> Baixar Protocolo
                        </a>
                    @else
                        <button class="btn btn-default disabled btn-pill" style="width: 100%; border-radius: 50px; padding: 8px;" disabled>Aguardando Entrada</button>
                    @endif
                </div>
            </div>
        </div>

        @if(!empty($servico->observacoes))
            @php
                $servObsLimpa = cleanObsText($servico->observacoes);
            @endphp
            <div style="margin-top: 20px;">
                <div style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                    <i class="fa fa-comment-o text-primary" style="margin-right: 6px;"></i> Observações do Serviço
                </div>
                <div style="background: #f8fafc; border-radius: 6px; padding: 14px 18px; border: 1px solid #e2e8f0; font-size: 13px; color: #334155; line-height: 1.6; white-space: pre-wrap;">{!! nl2br(e($servObsLimpa)) !!}</div>
            </div>
        @endif
    </div>
</div>

<!-- Widgets de Pendências e Taxas Ativas -->
<div class="row">
    @if($cntTaxas > 0)
        <div class="col-md-6">
            @include('cliente.components.widget-taxas')
        </div>
    @endif

    @if($cntPendenciasAbertas > 0)
        <div class="col-md-{{ $cntTaxas > 0 ? '6' : '12' }}">
            @include('cliente.components.widget-pendencias')
        </div>
    @endif
</div>

<!-- Histórico de Atualizações & Interações com Avatares -->
<div class="row" style="margin-top: 10px;">
    <div class="col-md-12">
        <div class="box box-default" style="border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 25px;">
            <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 20px 25px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                <h3 class="box-title" style="font-weight: 700; color: #1e293b; font-size: 17px; margin: 0;">
                    <i class="fa fa-comments-o text-primary" style="margin-right: 8px;"></i> Histórico de Interações & Atividades
                </h3>
                <a href="{{ route('cliente.interacoes.lista', $servico->id) }}" class="btn btn-pill btn-default btn-xs" style="border-radius: 50px; padding: 6px 18px; font-weight: 600;">
                    Ver Histórico Completo
                </a>
            </div>
            <div class="box-body" style="padding: 25px;">
                @if(isset($servico->ultimasInteracoes) && count($servico->ultimasInteracoes) > 0)
                    <div class="activity-timeline" style="display: flex; flex-direction: column; gap: 20px;">
                        @foreach($servico->ultimasInteracoes as $historico)
                            @php
                                $histObsLimpa = cleanObsText($historico->observacoes);
                                $userObj = $historico->user;
                                $userName = $userObj->name ?? 'Sistema Castro';
                                $isClienteUser = $userObj && $userObj->privileges == 'cliente';
                                
                                // Resolvendo a foto de Avatar do Usuário
                                if ($userObj && !empty($userObj->avatar_url)) {
                                    $userAvatarUrl = $userObj->avatar_url;
                                } else {
                                    $bgHex = $isClienteUser ? '0ea5e9' : '10b981';
                                    $userAvatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=' . $bgHex . '&color=fff&size=128&bold=true';
                                }
                            @endphp
                            <div style="display: flex; gap: 15px; align-items: flex-start;">
                                <!-- Avatar Image -->
                                <img src="{{ $userAvatarUrl }}" alt="{{ $userName }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid {{ $isClienteUser ? '#0ea5e9' : '#10b981' }}; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                                
                                <!-- Timeline Card Content -->
                                <div style="flex: 1; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; overflow: hidden;">
                                    <div style="padding: 12px 18px; border-bottom: 1px solid #f1f5f9; background: #fff; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <strong style="color: #1e293b; font-size: 14px; font-weight: 700;">{{ $userName }}</strong>
                                            <span class="label" style="background: {{ $isClienteUser ? '#e0f2fe' : '#d1fae5' }}; color: {{ $isClienteUser ? '#0369a1' : '#047857' }}; font-size: 11px; font-weight: 600; border-radius: 4px; padding: 2px 8px;">
                                                {{ $isClienteUser ? 'Cliente' : 'Equipe Castro' }}
                                            </span>
                                        </div>
                                        <small style="color: #64748b; font-size: 12px; font-weight: 500;">
                                            <i class="fa fa-clock-o" style="margin-right: 4px;"></i>
                                            {{ \Carbon\Carbon::parse($historico->edited_at ?? $historico->created_at)->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                                            @if($historico->edited_at)
                                                <span class="text-warning" style="font-size: 11px; font-weight: 700; margin-left: 4px;">(editado)</span>
                                            @endif
                                        </small>
                                    </div>

                                    <div style="padding: 14px 18px; font-size: 13px; color: #334155; line-height: 1.6; white-space: pre-wrap;">{!! nl2br(e($histObsLimpa)) !!}</div>

                                    @if($historico->pendencia)
                                        <div style="padding: 8px 18px; background: #fff; border-top: 1px solid #f1f5f9;">
                                            <span class="label label-info" style="border-radius: 4px; padding: 4px 8px; font-weight: 500;">
                                                <i class="fa fa-link"></i> Pendência Vinculada: {{ $historico->pendencia->pendencia }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted" style="padding: 25px;">
                        <i class="fa fa-comments-o" style="font-size: 28px; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                        Nenhuma atualização ou interação registrada para este serviço ainda.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<style>
    .content-header .breadcrumb { display: none !important; }
    .btn-pill { transition: all 0.2s ease; }
    .btn-pill:hover { transform: translateY(-1px); }
</style>
@endsection