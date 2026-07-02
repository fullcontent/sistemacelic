@extends('adminlte::page')

@section('content_header')
    <h1>
        Interações da O.S. {{$servico->os}} <small>{{$servico->nome}}</small>
    </h1>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-check"></i> Sucesso!</h4>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-ban"></i> Erro!</h4>
            {{ session('error') }}
        </div>
    @endif

    <div style="margin-bottom: 15px;">
        <a href="{{route('servicos.show',$servico->id)}}" class="btn btn-default"><i class="fa fa-chevron-left"></i> Voltar para o Serviço</a>
        <a href="{{route('timeline',$servico->id)}}" class='btn btn-info' target="_blank"><i class="glyphicon glyphicon-time"></i> Ver Timeline</a>
    </div>

    <!-- Box de Interações de Usuários -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-comments"></i> Histórico de Mensagens</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            @if(count($interacoes) > 0)
                <ul class="timeline timeline-inverse">
                    @foreach($interacoes as $historico)
                        @php
                            if($historico->user->privileges == 'cliente') {
                                $label = "fa fa-user bg-red";
                            } elseif($historico->user->privileges == 'admin') {
                                $label = "fa fa-copyright bg-aqua";
                            } else {
                                $label = "fa fa-weixin bg-blue";
                            }
                        @endphp
                        <!-- timeline item -->
                        <li class="historico-item-li" id="historico-li-{{$historico->id}}">
                            <i class="{{$label}}"></i>
                            
                            <div class="timeline-item" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 4px; background: #fff; margin-bottom: 15px;">
                                <span class="time" style="padding: 10px 15px;">
                                    <i class="fa fa-clock-o"></i> 
                                    <span class="time-text">{{\Carbon\Carbon::parse($historico->edited_at ?? $historico->created_at)->timezone('America/Sao_Paulo')->format('d/m/Y H:i')}}</span>
                                    <span class="editado-indicator text-warning" style="font-size: 10px; font-weight: bold; margin-left: 2px; {{$historico->edited_at ? '' : 'display: none;'}}">(editado)</span>
                                </span>
                                
                                <h3 class="timeline-header" style="border-bottom: none; padding: 10px 15px; font-size: 14px; font-weight: bold;">
                                    <strong>{{$historico->user->name ?? 'Robot'}}</strong>
                                    <small class="text-muted" style="display: block; font-size: 11px; margin-top: 2px; font-weight: normal;">
                                        {{$historico->user->privileges == 'admin' ? 'Admin' : 'Operacional'}}
                                    </small>
                                </h3>

                                <div class="timeline-body" style="padding: 5px 15px 10px 15px; font-size: 13px; color: #444; border-bottom: none;">
                                    @if(str_contains($historico->observacoes, 'Alterou solicitante'))
                                        @php
                                            $id_sol = preg_replace('/[^0-9]/', '', $historico->observacoes);
                                            $solicitante = \App\Models\Solicitante::where('id',$id_sol)->value('nome');
                                            echo "Alterou solicitante para ".$solicitante;
                                        @endphp
                                    @elseif(str_contains($historico->observacoes, 'Alterou responsavel_id'))
                                        @php
                                            $id_resp = preg_replace('/[^0-9]/', '', $historico->observacoes);
                                            $solicitante = \App\User::where('id',$id_resp)->value('name');
                                            echo "Alterou responsável para ".$solicitante;
                                        @endphp
                                    @else
                                        {!! nl2br(e(strip_tags(str_replace(['<br>', '<br />', '<br/>', '</p>'], "\n", $historico->observacoes)))) !!}
                                    @endif
                                </div>

                                <div class="timeline-footer" style="padding: 5px 15px 10px 15px; background: transparent; border-top: none; display: flex; align-items: center; gap: 8px;">
                                    <span class="pendencia-badge-container">
                                        @if($historico->pendencia)
                                            <div class="pendencia-badge" style="display: inline-flex; border: 1px solid #3c8dbc; border-radius: 4px; overflow: hidden; font-size: 11px; vertical-align: middle;">
                                                <span style="background: #3c8dbc; color: #fff; padding: 3px 8px; font-weight: normal;"><i class="fa fa-link"></i> Vinculado à Pendência:</span>
                                                <span class="pendencia-nome-span" style="background: #f4f4f4; color: #444; padding: 3px 8px; font-weight: bold;">{{$historico->pendencia->pendencia}}</span>
                                            </div>
                                        @endif
                                    </span>

                                    @if($historico->visibilidade === 'interno')
                                        <span class="label label-default" style="padding: 4px 8px; font-size: 11px; background: #b5bbc8; color: #fff; font-weight: normal; border-radius: 4px; vertical-align: middle;"><i class="fa fa-lock"></i> Interno</span>
                                    @endif

                                    @if(strtolower(auth()->user()->privileges) == 'admin' || auth()->user()->id == 1 || auth()->id() == 1 || (strtolower(auth()->user()->privileges) == 'user' && $historico->user_id == auth()->id()))
                                        <a href="{{ route('interacao.edit', $historico->id) }}" class="text-muted" style="margin-left: auto; font-size: 14px; display: inline-block; vertical-align: middle; cursor: pointer;" title="Editar"><span class="glyphicon glyphicon-pencil"></span></a>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center text-muted" style="padding: 20px;">
                    <i class="fa fa-info-circle" style="font-size: 24px; margin-bottom: 10px;"></i>
                    <p>Nenhuma interação de usuário registrada para esta O.S.</p>
                </div>
            @endif
        </div>
        <!-- /.box-body -->
    </div>

    <!-- Box de Interações do Sistema (Logs de alteração de campos) -->
    <div class="box box-default collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title"><a href="" data-widget="collapse"><i class="fa fa-cog"></i> Histórico de Alterações do Sistema</a></h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="box-body">
            @if(count($interacoesSistema) > 0)
                <ul class="timeline timeline-inverse">
                    @foreach($interacoesSistema as $historico)
                        @php
                            if($historico->user->privileges == 'cliente') {
                                $label = "fa fa-user bg-red";
                            } elseif($historico->user->privileges == 'admin') {
                                $label = "fa fa-copyright bg-aqua";
                            } else {
                                $label = "fa fa-weixin bg-default";
                            }
                        @endphp
                        <li class="historico-item-li" id="historico-li-{{$historico->id}}">
                            <i class="{{$label}}"></i>

                            <div class="timeline-item" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 4px; background: #fff; margin-bottom: 15px;">
                                <span class="time" style="padding: 10px 15px;">
                                    <i class="fa fa-clock-o"></i> 
                                    <span class="time-text">{{\Carbon\Carbon::parse($historico->edited_at ?? $historico->created_at)->timezone('America/Sao_Paulo')->format('d/m/Y H:i')}}</span>
                                    <span class="editado-indicator text-warning" style="font-size: 10px; font-weight: bold; margin-left: 2px; {{$historico->edited_at ? '' : 'display: none;'}}">(editado)</span>
                                </span>
                                
                                <h3 class="timeline-header" style="border-bottom: none; padding: 10px 15px; font-size: 14px; font-weight: bold;">
                                    <strong>{{$historico->user->name ?? 'Sistema'}}</strong>
                                    <small class="text-muted" style="display: block; font-size: 11px; margin-top: 2px; font-weight: normal;">
                                        {{$historico->user->privileges == 'admin' ? 'Admin' : 'Operacional'}}
                                    </small>
                                </h3>

                                <div class="timeline-body" style="padding: 5px 15px 10px 15px; font-size: 13px; color: #444; border-bottom: none;">
                                    @if(str_contains($historico->observacoes, 'Alterou solicitante'))
                                        @php
                                            $id_sol = preg_replace('/[^0-9]/', '', $historico->observacoes);
                                            $solicitante = \App\Models\Solicitante::where('id',$id_sol)->value('nome');
                                            echo "Alterou solicitante para ".$solicitante;
                                        @endphp
                                    @elseif(str_contains($historico->observacoes, 'Alterou responsavel_id'))
                                        @php
                                            $id_resp = preg_replace('/[^0-9]/', '', $historico->observacoes);
                                            $solicitante = \App\User::where('id',$id_resp)->value('name');
                                            echo "Alterou responsável para ".$solicitante;
                                        @endphp
                                    @else
                                        {!! nl2br(e(strip_tags(str_replace(['<br>', '<br />', '<br/>', '</p>'], "\n", $historico->observacoes)))) !!}
                                    @endif
                                </div>

                                <div class="timeline-footer" style="padding: 5px 15px 10px 15px; background: transparent; border-top: none; display: flex; align-items: center; gap: 8px;">
                                    <span class="pendencia-badge-container">
                                        @if($historico->pendencia)
                                            <div class="pendencia-badge" style="display: inline-flex; border: 1px solid #3c8dbc; border-radius: 4px; overflow: hidden; font-size: 11px; vertical-align: middle;">
                                                <span style="background: #3c8dbc; color: #fff; padding: 3px 8px; font-weight: normal;"><i class="fa fa-link"></i> Vinculado à Pendência:</span>
                                                <span class="pendencia-nome-span" style="background: #f4f4f4; color: #444; padding: 3px 8px; font-weight: bold;">{{$historico->pendencia->pendencia}}</span>
                                            </div>
                                        @endif
                                    </span>

                                    @if($historico->visibilidade === 'interno')
                                        <span class="label label-default" style="padding: 4px 8px; font-size: 11px; background: #b5bbc8; color: #fff; font-weight: normal; border-radius: 4px; vertical-align: middle;"><i class="fa fa-lock"></i> Interno</span>
                                    @endif

                                    @if(strtolower(auth()->user()->privileges) == 'admin' || auth()->user()->id == 1 || auth()->id() == 1 || (strtolower(auth()->user()->privileges) == 'user' && $historico->user_id == auth()->id()))
                                        <a href="#" class="btn-edit-historico text-muted" data-toggle="modal" data-target="#modal-edit-historico" data-id="{{$historico->id}}" data-observacoes="{{ $historico->observacoes }}" data-pendencia_id="{{$historico->pendencia_id}}" data-pendencia_nome="{{ $historico->pendencia ? $historico->pendencia->pendencia : '' }}" style="margin-left: auto; font-size: 14px; display: inline-block; vertical-align: middle; cursor: pointer;" title="Editar"><span class="glyphicon glyphicon-pencil"></span></a>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center text-muted" style="padding: 20px;">
                    <p>Nenhuma alteração do sistema registrada para esta O.S.</p>
                </div>
            @endif
        </div>
    </div>


@endsection

@section('js')
<script>
$(document).ready(function() {
    // Dicionário de autocorreção em tempo real para PT-BR
    const autocorrectMap = {
        "serviso": "serviço",
        "servisos": "serviços",
        "corresao": "correção",
        "correcoes": "correções",
        "pagemanto": "pagamento",
        "pagemantos": "pagamentos",
        "historico": "histórico",
        "historicos": "históricos",
        "pendencia": "pendência",
        "pendencias": "pendências",
        "atencao": "atenção",
        "nao": "não",
        "tbm": "também",
        "tambem": "também",
        "voce": "você",
        "voces": "vocês",
        "ja": "já",
        "ate": "até",
        "email": "e-mail",
        "usuario": "usuário",
        "usuarios": "usuários",
        "situacao": "situação",
        "situacoes": "situações",
        "comunicacao": "comunicação",
        "relatorio": "relatório",
        "relatorios": "relatórios",
        "codigo": "código",
        "codigos": "códigos",
        "duvida": "dúvida",
        "duvidas": "dúvidas",
        "notificacao": "notificação",
        "notificacoes": "notificações",
        "concluido": "concluído",
        "alvara": "alvará",
        "alvaras": "alvarás",
        "licenca": "licença",
        "licencas": "licenças",
        "boletos": "boletos",
        "boleto": "boleto"
    };

    function applyAutocorrect(input) {
        const text = input.value;
        const selectionStart = input.selectionStart;
        const textBeforeCursor = text.substring(0, selectionStart);
        const words = textBeforeCursor.split(/([\s,.\!?;\(\)\[\]\{\}])/);
        
        if (words.length > 0) {
            const lastWordIndex = words.length - 1;
            const lastWord = words[lastWordIndex];
            const lowerWord = lastWord.toLowerCase();
            
            if (autocorrectMap.hasOwnProperty(lowerWord)) {
                let corrected = autocorrectMap[lowerWord];
                if (lastWord[0] === lastWord[0].toUpperCase() && lastWord[0] !== lastWord[0].toLowerCase()) {
                    corrected = corrected.charAt(0).toUpperCase() + corrected.slice(1);
                }
                
                words[lastWordIndex] = corrected;
                const newTextBeforeCursor = words.join('');
                const textAfterCursor = text.substring(selectionStart);
                
                input.value = newTextBeforeCursor + textAfterCursor;
                const newCursorPos = newTextBeforeCursor.length;
                input.setSelectionRange(newCursorPos, newCursorPos);
            }
        }
    }

    // Gatilho para autocorreção ao pressionar espaço ou pontuação
    $('#edit-historico-observacoes').on('keydown', function(e) {
        if (e.key === ' ' || e.key === ',' || e.key === '.' || e.key === '!' || e.key === '?' || e.key === ';') {
            applyAutocorrect(this);
        }
    });

    function escapeHtml(string) {
        if (!string) return '';
        return String(string)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


});
</script>
@stop