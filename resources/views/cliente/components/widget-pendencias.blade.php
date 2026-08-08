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
@endphp

<div class="box box-warning" style="border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 25px;">
    <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 18px 22px; display: flex; align-items: center; justify-content: space-between;">
        <h3 class="box-title" style="font-weight: 700; color: #1e293b; font-size: 16px; margin: 0;">
            <i class="fa fa-tasks text-warning" style="margin-right: 8px;"></i> Pendências em Aberto
        </h3>
        <span class="badge bg-orange" style="font-size: 12px; border-radius: 50px; padding: 4px 10px;">
            {{ count($pendencias->where('status', 'pendente')) }} pendência(s)
        </span>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="padding: 20px;">
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @forelse($pendencias->where('status', 'pendente') as $pendencia)
                @php
                    $isVencida = !empty($pendencia->vencimento) && $pendencia->vencimento < date('Y-m-d');
                    $isClienteResp = $pendencia->responsavel_tipo == 'cliente';
                @endphp
                <div style="background: #f8fafc; border-radius: 8px; padding: 14px 18px; border: 1px solid #e2e8f0; border-left: 4px solid {{ $isClienteResp ? '#0ea5e9' : '#f59e0b' }}; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span class="label label-default" style="border-radius: 4px; font-weight: 600;">Etapa {{ $pendencia->etapa }}</span>
                            <span class="label {{ $isClienteResp ? 'label-info' : 'label-warning' }}" style="border-radius: 4px; font-weight: 500;">
                                Resp: {{ $isClienteResp ? 'Cliente' : ($pendencia->responsavel_tipo == 'usuario' ? 'Castro' : 'Órgão Público') }}
                            </span>
                            @if(!empty($pendencia->respondida_em))
                                <span class="label label-primary" style="border-radius: 4px; font-weight: 500;">Resposta enviada</span>
                            @endif
                        </div>
                        <div style="font-weight: 700; color: #1e293b; font-size: 14px;">
                            {{ $pendencia->pendencia }}
                        </div>
                        @if(!empty($pendencia->vencimento))
                            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                Data limite: <strong class="{{ $isVencida ? 'text-danger' : 'text-success' }}">{{ \Carbon\Carbon::parse($pendencia->vencimento)->format('d/m/Y') }}</strong>
                            </div>
                        @endif
                    </div>

                    <div>
                        <button type="button" class="btn btn-pill abrir-responder-modal" data-id="{{ $pendencia->id }}" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #fff; border-radius: 50px; padding: 6px 18px; font-weight: 700; border: none; box-shadow: 0 2px 6px rgba(14, 165, 233, 0.3);">
                            <i class="fa fa-reply" style="margin-right: 6px;"></i> Responder Pendência
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted" style="padding: 20px;">
                    <i class="fa fa-check-circle text-success" style="font-size: 28px; display: block; margin-bottom: 8px;"></i>
                    Nenhuma pendência em aberto para este serviço!
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal de Resposta em 2 Colunas da Pendência -->
<div class="modal fade" id="modal-responder-pendencia" tabindex="-1" role="dialog" aria-labelledby="modalResponderPendenciaLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
      <div class="modal-header" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #fff; padding: 16px 20px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.9;"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalResponderPendenciaLabel" style="font-weight: 700; font-size: 16px;"><i class="fa fa-reply" style="margin-right: 8px;"></i> Responder Pendência</h4>
      </div>

      <form id="form-responder-pendencia-modal" action="{{ route('cliente.pendencia.responder', 0) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body" style="padding: 20px; font-size: 13px; line-height: 1.5;">
            <div class="row">
                <!-- Coluna Esquerda: Informações da Pendência -->
                <div class="col-md-6" style="border-right: 1px solid #e2e8f0;">
                    <div style="background: #f8fafc; border-radius: 8px; padding: 14px; border: 1px solid #e2e8f0; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <strong style="color: #0284c7; font-size: 13px;" id="modal-resp-etapa-os"></strong>
                            <span id="modal-resp-venc" style="font-weight: 700; color: #059669; font-size: 12px;"></span>
                        </div>
                        <div id="modal-resp-descr" style="font-weight: 700; color: #1e293b; font-size: 14px; margin-bottom: 8px;"></div>
                        <div style="font-weight: 600; color: #64748b; font-size: 11px; text-transform: uppercase; margin-bottom: 4px;">Instruções / Observações:</div>
                        <div id="modal-resp-obs" style="font-size: 12px; color: #475569; white-space: pre-wrap; max-height: 140px; overflow-y: auto; background: #fff; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; line-height: 1.4;"></div>
                    </div>

                    <div>
                        <label class="text-muted" style="display:block; margin-bottom: 6px; font-size: 11px; text-transform: uppercase; font-weight: 700;">Arquivos Anexados</label>
                        <div id="modal-resp-arquivos-list" style="max-height: 120px; overflow-y: auto;"></div>
                    </div>
                </div>

                <!-- Coluna Direita: Formulário de Resposta -->
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="font-weight: 700; color: #1e293b; font-size: 13px;">Sua Resposta / Esclarecimento <span class="text-danger">*</span></label>
                        <textarea class="form-control plain-textarea-field" name="observacoes" id="modal_resposta_text" rows="6" spellcheck="true" autocomplete="off" placeholder="Escreva aqui sua resposta em texto simples..." required style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px; font-size: 13px; line-height: 1.5; resize: vertical; background: #ffffff !important; font-family: inherit;"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 5px;">
                        <label style="font-weight: 700; color: #1e293b; font-size: 13px;">Anexar Arquivos / Comprovantes</label>
                        <input type="file" name="arquivos[]" multiple class="form-control" style="border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px; font-size: 12px;">
                        <small class="text-muted" style="display: block; margin-top: 4px;">Selecione arquivos (PDF, imagens, zip).</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 20px;">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="border-radius: 50px; font-weight: 600; padding: 6px 18px;">Cancelar</button>
            <button type="submit" class="btn btn-primary" style="border-radius: 50px; font-weight: 700; background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border: none; padding: 6px 24px;">
                <i class="fa fa-send" style="margin-right: 6px;"></i> Enviar Resposta
            </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var $ = window.jQuery || window.$;
    if (!$) return;

    function cleanJsObs(str) {
        if (!str) return 'Nenhuma instrução adicional.';
        try {
            var parser = new DOMParser();
            var doc = parser.parseFromString(str, 'text/html');
            var text = doc.body.textContent || "";
            text = text.replace(/\xA0/g, ' ');
            text = text.replace(/[\r\n]{3,}/g, '\n\n');
            return text.trim();
        } catch(e) {
            return str.replace(/<[^>]*>?/gm, '').trim();
        }
    }

    $(document).on('click', '.abrir-responder-modal', function(e) {
        e.preventDefault();
        var pendenciaId = $(this).data('id');
        var urlShow = '{{ route("cliente.pendencia.show", ":id") }}'.replace(':id', pendenciaId);
        var urlResponder = '{{ route("cliente.pendencia.responder", ":id") }}'.replace(':id', pendenciaId);
        
        $('#form-responder-pendencia-modal').attr('action', urlResponder);

        // Destruir / desativar qualquer plugin de editor rico ou menções no textarea de resposta
        if (window.CKEDITOR && CKEDITOR.instances['modal_resposta_text']) {
            try { CKEDITOR.instances['modal_resposta_text'].destroy(true); } catch(err) {}
        }
        if ($.fn.summernote) {
            try { $('#modal_resposta_text').summernote('destroy'); } catch(err) {}
        }

        $('#modal_resposta_text').val('');

        $.ajax({
            url: urlShow,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                $('#modal-resp-etapa-os').text('Etapa ' + data.etapa + ' | OS ' + data.os);
                $('#modal-resp-descr').text(data.pendencia);
                $('#modal-resp-venc').text(data.vencimento ? 'Data limite: ' + data.vencimento : 'Sem data limite');
                
                var obsLimpa = cleanJsObs(data.observacoes);
                $('#modal-resp-obs').text(obsLimpa);
                
                var arquivosHtml = '';
                if (data.arquivos && data.arquivos.length > 0) {
                    arquivosHtml = '<ul class="list-group" style="margin-bottom:0;">';
                    $.each(data.arquivos, function(i, arq) {
                        arquivosHtml += '<li class="list-group-item" style="display:flex; justify-content:space-between; align-items:center; border-radius:6px; margin-bottom:4px; padding:6px 10px; font-size:12px;">' +
                            '<span><i class="fa fa-file-o text-info" style="margin-right:6px;"></i> <strong>' + arq.nome + '</strong></span>' +
                            '<a href="' + arq.download_url + '" class="btn btn-xs btn-success btn-pill" style="border-radius:50px; padding:2px 8px;" target="_blank"><i class="fa fa-download"></i> Baixar</a>' +
                            '</li>';
                    });
                    arquivosHtml += '</ul>';
                } else {
                    arquivosHtml = '<p class="text-muted" style="font-size:12px; margin:0;">Nenhum arquivo anexado nesta pendência.</p>';
                }
                $('#modal-resp-arquivos-list').html(arquivosHtml);
                
                $('#modal-responder-pendencia').modal('show');
            },
            error: function() {
                alert('Erro ao carregar detalhes da pendência.');
            }
        });
    });
});
</script>