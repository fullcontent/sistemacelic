@extends('adminlte::page')

@section('content_header')
    <h1 style="font-weight: 700; color: #1e293b; font-size: 24px; margin-bottom: 5px;">
        <i class="fa fa-tasks text-primary" style="margin-right: 8px;"></i> Detalhes da Pendência #{{ $pendencia->id }}
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

<div class="row">
    <!-- Esquerda: Dados da Pendência + Formulário de Resposta -->
    <div class="col-md-7">
        <!-- Cartão de Informações da Pendência -->
        <div class="box box-primary" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 25px;">
            <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 15px 20px;">
                <h3 class="box-title" style="font-weight: 700; color: #1e293b; font-size: 16px; margin: 0;">
                    Etapa {{ $pendencia->etapa }}: {{ $pendencia->pendencia }}
                </h3>
            </div>
            <div class="box-body" style="padding: 20px;">
                <div class="row" style="row-gap: 12px;">
                    <div class="col-md-6">
                        <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Ordem de Serviço:</strong>
                        <div style="font-size: 14px; font-weight: 600; color: #1e293b; margin-top: 2px;">
                            @if($pendencia->servico)
                                <a href="{{ route('cliente.servico.show', $pendencia->servico->id) }}" style="color: #2563eb;">
                                    {{ $pendencia->servico->os ? $pendencia->servico->os . ' - ' : '' }}{{ $pendencia->servico->nome }}
                                </a>
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Status:</strong>
                        <div style="margin-top: 2px;">
                            @if(!empty($pendencia->respondida_em))
                                <span class="label label-info" style="border-radius: 4px; padding: 4px 8px; font-weight: 500;">
                                    Resposta enviada - aguardando Castro
                                </span>
                            @else
                                <span class="label label-warning" style="border-radius: 4px; padding: 4px 8px; font-weight: 500;">
                                    {{ ucfirst($pendencia->status) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6" style="margin-top: 10px;">
                        <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Responsabilidade:</strong>
                        <div style="font-size: 14px; font-weight: 500; color: #334155; margin-top: 2px;">
                            {{ $pendencia->responsavel_tipo == 'usuario' ? 'Castro Engenharia' : ($pendencia->responsavel_tipo == 'cliente' ? 'Cliente' : 'Órgão Público') }}
                        </div>
                    </div>

                    <div class="col-md-6" style="margin-top: 10px;">
                        <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Data Limite (Vencimento):</strong>
                        <div style="font-size: 14px; font-weight: 600; color: #334155; margin-top: 2px;">
                            @if($pendencia->vencimento)
                                @php
                                    $venc = \Carbon\Carbon::parse($pendencia->vencimento);
                                    $badgeClass = $venc->isPast() ? 'label-danger' : 'label-success';
                                @endphp
                                <span class="label {{ $badgeClass }}" style="border-radius: 4px; padding: 4px 8px;">
                                    {{ $venc->format('d/m/Y') }}
                                </span>
                            @else
                                Indefinida
                            @endif
                        </div>
                    </div>

                    @if($pendencia->responsavelCliente)
                        <div class="col-md-12" style="margin-top: 10px;">
                            <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Responsável Cliente Atribuído:</strong>
                            <div style="font-size: 14px; font-weight: 600; color: #0284c7; margin-top: 2px;">
                                <i class="fa fa-user" style="margin-right: 4px;"></i> {{ $pendencia->responsavelCliente->name }}
                            </div>
                        </div>
                    @endif

                    @if(!empty($pendencia->observacoes))
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
                            $obsLimpa = cleanObsText($pendencia->observacoes);
                        @endphp
                        <div class="col-md-12" style="margin-top: 15px;">
                            <strong style="color: #64748b; font-size: 12px; text-transform: uppercase;">Instruções / Observações da Castro:</strong>
                            <div style="background: #f8fafc; border-radius: 6px; padding: 12px 15px; border: 1px solid #e2e8f0; margin-top: 4px; font-size: 13px; color: #334155; line-height: 1.6; white-space: pre-wrap;">{!! nl2br(e($obsLimpa)) !!}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form de Resposta do Cliente -->
        <div class="box box-info" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 25px;">
            <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 15px 20px;">
                <h3 class="box-title" style="font-weight: 700; color: #2c3e50; font-size: 16px; margin: 0;">
                    <i class="fa fa-reply text-info" style="margin-right: 8px;"></i> Responder Pendência
                </h3>
            </div>
            {!! Form::open(['route' => ['cliente.pendencia.responder', $pendencia->id], 'enctype' => 'multipart/form-data']) !!}
            <div class="box-body" style="padding: 20px;">
                <div class="form-group">
                    {!! Form::label('observacoes', 'Sua Resposta / Observações', ['class' => 'control-label', 'style' => 'font-weight: 600; color: #334155;']) !!}
                    {!! Form::textarea('observacoes', null, ['class' => 'form-control plain-textarea-field', 'id' => 'resposta_observacoes_page', 'rows' => 5, 'required', 'spellcheck' => 'true', 'autocomplete' => 'off', 'placeholder' => 'Escreva aqui sua resposta ou esclarecimento em texto simples...', 'style' => 'border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px 15px; font-size: 14px; line-height: 1.5; resize: vertical; background: #ffffff !important; font-family: inherit;']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('arquivo', 'Anexar Comprovantes / Documentos', ['class' => 'control-label', 'style' => 'font-weight: 600; color: #334155;']) !!}
                    <input type="file" name="arquivo[]" class="form-control" multiple style="border-radius: 6px; padding: 6px;">
                    <small class="text-muted">Você pode selecionar múltiplos arquivos (PDF, Imagens, Zip, etc).</small>
                </div>
            </div>
            <div class="box-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 15px 20px; text-align: right;">
                <button type="submit" class="btn btn-pill" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #fff; border-radius: 50px; padding: 8px 25px; font-weight: 700; border: none; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3);">
                    <i class="fa fa-paper-plane" style="margin-right: 6px;"></i> Enviar Resposta
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <!-- Direita: Histórico de Respostas + Arquivos da Pendência -->
    <div class="col-md-5">
        <!-- Histórico de Respostas -->
        <div class="box box-default" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 25px;">
            <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 15px 20px;">
                <h3 class="box-title" style="font-weight: 700; color: #334155; font-size: 16px; margin: 0;">
                    <i class="fa fa-history text-muted" style="margin-right: 8px;"></i> Histórico de Mensagens
                </h3>
            </div>
            <div class="box-body" style="padding: 20px; max-height: 400px; overflow-y: auto;">
                @forelse($historicos as $hist)
                    @php
                        $histObsLimpa = trim(strip_tags(html_entity_decode(str_replace(['</p>', '<br>', '<br/>', '<br />'], "\n", $hist->observacoes))));
                    @endphp
                    <div style="background: #f8fafc; border-radius: 8px; padding: 12px 15px; margin-bottom: 12px; border-left: 4px solid {{ $hist->user && $hist->user->privileges == 'cliente' ? '#0ea5e9' : '#10b981' }};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <strong style="color: #1e293b; font-size: 13px;">
                                <i class="fa {{ $hist->user && $hist->user->privileges == 'cliente' ? 'fa-user text-info' : 'fa-user-md text-success' }}" style="margin-right: 4px;"></i>
                                {{ $hist->user->name ?? 'Sistema' }}
                            </strong>
                            <small class="text-muted" style="font-size: 11px;">
                                {{ \Carbon\Carbon::parse($hist->created_at)->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        <div style="font-size: 13px; color: #334155; line-height: 1.5; white-space: pre-wrap;">{!! nl2br(e($histObsLimpa)) !!}</div>
                    </div>
                @empty
                    <div class="text-center text-muted" style="padding: 15px;">Nenhuma resposta ou interação registrada ainda.</div>
                @endforelse
            </div>
        </div>

        <!-- Arquivos Anexados à Pendência -->
        <div class="box box-default" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 25px;">
            <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 15px 20px;">
                <h3 class="box-title" style="font-weight: 700; color: #334155; font-size: 16px; margin: 0;">
                    <i class="fa fa-paperclip text-muted" style="margin-right: 8px;"></i> Arquivos Vinculados
                </h3>
            </div>
            <div class="box-body" style="padding: 15px;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" style="width: 100%; margin-bottom: 0;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="font-weight: 600; color: #475569; padding: 10px;">Arquivo</th>
                                <th style="font-weight: 600; color: #475569; padding: 10px;">Enviado por</th>
                                <th style="font-weight: 600; color: #475569; padding: 10px; text-align: center;">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($arquivos as $a)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 10px; color: #1e293b; font-weight: 500; font-size: 13px;">{{ $a->nome }}</td>
                                    <td style="padding: 10px; color: #64748b; font-size: 12px;">{{ $a->user->name ?? 'Sistema' }}</td>
                                    <td style="padding: 10px; text-align: center;">
                                        <a href="{{ route('cliente.arquivo.download', $a->id) }}" class="btn btn-pill btn-xs" style="background: #2563eb; color: #fff; border-radius: 50px; padding: 4px 12px; font-weight: 600; text-decoration: none;">
                                            Baixar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted" style="padding: 15px;">Nenhum arquivo anexo nesta pendência.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
