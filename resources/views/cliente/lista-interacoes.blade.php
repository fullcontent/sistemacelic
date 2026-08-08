@extends('adminlte::page')

@section('content_header')
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 style="font-weight: 700; color: #1e293b; font-size: 24px; margin: 0;">
                <i class="fa fa-comments-o text-primary" style="margin-right: 8px;"></i> Histórico de Interações - O.S. {{ $servico->os }}
            </h1>
            <span style="font-size: 13px; color: #64748b; margin-top: 4px; display: block;">
                {{ $servico->nome }}
            </span>
        </div>
        <div>
            <a href="{{ route('cliente.servico.show', $servico->id) }}" class="btn btn-pill btn-default" style="border-radius: 50px; padding: 8px 22px; font-weight: 600;">
                <i class="fa fa-chevron-left" style="margin-right: 6px;"></i> Voltar ao Dashboard do Serviço
            </a>
        </div>
    </div>
@stop

@section('content')

<div class="box box-primary" style="border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 25px;">
    <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 20px 25px;">
        <h3 class="box-title" style="font-weight: 700; color: #1e293b; font-size: 17px; margin: 0;">
            <i class="fa fa-history text-info" style="margin-right: 8px;"></i> Linha do Tempo Completa ({{ count($interacoes) }} registros)
        </h3>
    </div>
    <div class="box-body" style="padding: 25px;">
        @if(count($interacoes) > 0)
            <div class="activity-timeline" style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($interacoes as $historico)
                    @php
                        $histObsLimpa = trim(strip_tags(html_entity_decode(str_replace(['</p>', '<br>', '<br/>', '<br />'], "\n", $historico->observacoes))));
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
                        <img src="{{ $userAvatarUrl }}" alt="{{ $userName }}" style="width: 46px; height: 46px; border-radius: 50%; object-fit: cover; border: 2px solid {{ $isClienteUser ? '#0ea5e9' : '#10b981' }}; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                        
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
            <div class="text-center text-muted" style="padding: 30px;">
                <i class="fa fa-comments-o" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 10px;"></i>
                Nenhuma interação registrada para esta O.S.
            </div>
        @endif
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