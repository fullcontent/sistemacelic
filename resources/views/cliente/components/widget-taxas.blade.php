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

<div class="box box-success" style="border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 25px;">
    <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 18px 22px;">
        <h3 class="box-title" style="font-weight: 700; color: #1e293b; font-size: 16px; margin: 0;">
            <i class="fa fa-dollar text-success" style="margin-right: 8px;"></i> Controle de Taxas
        </h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="padding: 20px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle" style="width: 100%; margin-bottom: 0;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="font-weight: 600; color: #475569; padding: 10px;">Taxa / Descrição</th>
                        <th style="font-weight: 600; color: #475569; padding: 10px;">Valor</th>
                        <th style="font-weight: 600; color: #475569; padding: 10px;">Vencimento</th>
                        <th style="font-weight: 600; color: #475569; padding: 10px;">Situação</th>
                        <th style="font-weight: 600; color: #475569; padding: 10px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxas ?? [] as $taxa)
                        @php
                            $isVencida = !empty($taxa->vencimento) && $taxa->vencimento < date('Y-m-d');
                            $isPago = !empty($taxa->comprovante);
                            $taxaObsLimpa = cleanObsText($taxa->observacoes ?? '');
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 10px; font-weight: 600; color: #1e293b;">
                                <a href="javascript:void(0)" class="abrir-taxa-modal" 
                                   data-nome="{{ $taxa->nome }}"
                                   data-valor="R$ {{ number_format((float)str_replace(['.', ','], ['', '.'], $taxa->valor), 2, ',', '.') }}"
                                   data-vencimento="{{ $taxa->vencimento ? \Carbon\Carbon::parse($taxa->vencimento)->format('d/m/Y') : '-' }}"
                                   data-pagamento="{{ $taxa->pagamento ? \Carbon\Carbon::parse($taxa->pagamento)->format('d/m/Y') : '-' }}"
                                   data-status="{{ $isPago ? 'Pago' : ($isVencida ? 'Vencida' : 'Aberto') }}"
                                   data-statusclass="{{ $isPago ? 'label-success' : ($isVencida ? 'label-danger' : 'label-warning') }}"
                                   data-boleto="{{ !empty($taxa->boleto) ? url("uploads/$taxa->boleto") : '' }}"
                                   data-comprovante="{{ !empty($taxa->comprovante) ? url("public/uploads/$taxa->comprovante") : '' }}"
                                   data-obs="{{ $taxaObsLimpa }}"
                                   style="color: #2563eb; text-decoration: none; font-weight: 700;">
                                    <i class="fa fa-search-plus" style="margin-right: 4px; font-size: 12px;"></i> {{ $taxa->nome }}
                                </a>
                            </td>
                            <td style="padding: 10px; font-weight: 700; color: #0f172a;">
                                R$ {{ number_format((float)str_replace(['.', ','], ['', '.'], $taxa->valor), 2, ',', '.') }}
                            </td>
                            <td style="padding: 10px; color: #64748b;">
                                {{ $taxa->vencimento ? \Carbon\Carbon::parse($taxa->vencimento)->format('d/m/Y') : '-' }}
                            </td>
                            <td style="padding: 10px;">
                                @if($isPago)
                                    <span class="label label-success" style="border-radius: 4px; padding: 4px 8px;">Pago</span>
                                @elseif($isVencida)
                                    <span class="label label-danger" style="border-radius: 4px; padding: 4px 8px;">Vencida</span>
                                @else
                                    <span class="label label-warning" style="border-radius: 4px; padding: 4px 8px;">Aberto</span>
                                @endif
                            </td>
                            <td style="padding: 10px; text-align: center;">
                                <button type="button" class="btn btn-default btn-xs btn-pill abrir-taxa-modal"
                                   data-nome="{{ $taxa->nome }}"
                                   data-valor="R$ {{ number_format((float)str_replace(['.', ','], ['', '.'], $taxa->valor), 2, ',', '.') }}"
                                   data-vencimento="{{ $taxa->vencimento ? \Carbon\Carbon::parse($taxa->vencimento)->format('d/m/Y') : '-' }}"
                                   data-pagamento="{{ $taxa->pagamento ? \Carbon\Carbon::parse($taxa->pagamento)->format('d/m/Y') : '-' }}"
                                   data-status="{{ $isPago ? 'Pago' : ($isVencida ? 'Vencida' : 'Aberto') }}"
                                   data-statusclass="{{ $isPago ? 'label-success' : ($isVencida ? 'label-danger' : 'label-warning') }}"
                                   data-boleto="{{ !empty($taxa->boleto) ? url("uploads/$taxa->boleto") : '' }}"
                                   data-comprovante="{{ !empty($taxa->comprovante) ? url("public/uploads/$taxa->comprovante") : '' }}"
                                   data-obs="{{ $taxaObsLimpa }}"
                                   style="border-radius: 50px; padding: 4px 12px; font-weight: 600;">
                                    <i class="fa fa-eye"></i> Detalhes
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted" style="padding: 20px;">
                                Nenhuma taxa registrada para este serviço.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Detalhes da Taxa -->
<div class="modal fade" id="modal-detalhes-taxa" tabindex="-1" role="dialog" aria-labelledby="modalDetalhesTaxaLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; padding: 18px 25px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.9;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalDetalhesTaxaLabel" style="font-weight: 700;"><i class="fa fa-dollar" style="margin-right: 8px;"></i> Detalhes da Taxa</h4>
            </div>
            <div class="modal-body" style="padding: 25px; font-size: 14px; line-height: 1.6;">
                <div style="margin-bottom: 15px;">
                    <label class="text-muted" style="display:block; margin-bottom: 2px; font-size: 12px; text-transform: uppercase;">Nome da Taxa</label>
                    <div id="modal-taxa-nome" style="font-weight: 700; font-size: 16px; color: #1e293b;"></div>
                </div>

                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-6">
                        <label class="text-muted" style="display:block; margin-bottom: 2px; font-size: 12px; text-transform: uppercase;">Valor</label>
                        <div id="modal-taxa-valor" style="font-weight: 800; font-size: 18px; color: #059669;"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted" style="display:block; margin-bottom: 2px; font-size: 12px; text-transform: uppercase;">Situação</label>
                        <div id="modal-taxa-status"></div>
                    </div>
                </div>

                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-6">
                        <label class="text-muted" style="display:block; margin-bottom: 2px; font-size: 12px; text-transform: uppercase;">Vencimento</label>
                        <div id="modal-taxa-vencimento" style="font-weight: 600; color: #334155;"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted" style="display:block; margin-bottom: 2px; font-size: 12px; text-transform: uppercase;">Pagamento</label>
                        <div id="modal-taxa-pagamento" style="font-weight: 600; color: #334155;"></div>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label class="text-muted" style="display:block; margin-bottom: 4px; font-size: 12px; text-transform: uppercase;">Observações</label>
                    <div id="modal-taxa-obs" style="background: #f8fafc; border-radius: 6px; padding: 12px 15px; border: 1px solid #e2e8f0; font-size: 13px; color: #334155; white-space: pre-wrap; min-height: 50px;"></div>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;" id="modal-taxa-acoes">
                    <a id="modal-taxa-btn-boleto" href="#" class="btn btn-pill" style="background: #f59e0b; color: #fff; border-radius: 50px; padding: 6px 18px; font-weight: 600; text-decoration: none; display: none;" target="_blank">
                        <i class="fa fa-barcode"></i> Visualizar Boleto
                    </a>
                    <a id="modal-taxa-btn-comprovante" href="#" class="btn btn-pill" style="background: #10b981; color: #fff; border-radius: 50px; padding: 6px 18px; font-weight: 600; text-decoration: none; display: none;" target="_blank">
                        <i class="fa fa-check-circle"></i> Visualizar Comprovante
                    </a>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 15px 25px;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 50px; font-weight: 600;">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var $ = window.jQuery || window.$;
    if (!$) return;

    $(document).on('click', '.abrir-taxa-modal', function(e) {
        e.preventDefault();
        var nome = $(this).data('nome');
        var valor = $(this).data('valor');
        var vencimento = $(this).data('vencimento');
        var pagamento = $(this).data('pagamento');
        var status = $(this).data('status');
        var statusClass = $(this).data('statusclass');
        var boleto = $(this).data('boleto');
        var comprovante = $(this).data('comprovante');
        var obs = $(this).data('obs') || 'Nenhuma observação informada.';

        $('#modal-taxa-nome').text(nome);
        $('#modal-taxa-valor').text(valor);
        $('#modal-taxa-vencimento').text(vencimento);
        $('#modal-taxa-pagamento').text(pagamento);
        $('#modal-taxa-status').html('<span class="label ' + statusClass + '" style="font-size: 13px; padding: 4px 10px; border-radius: 4px;">' + status + '</span>');
        $('#modal-taxa-obs').text(obs);

        if (boleto) {
            $('#modal-taxa-btn-boleto').attr('href', boleto).show();
        } else {
            $('#modal-taxa-btn-boleto').hide();
        }

        if (comprovante) {
            $('#modal-taxa-btn-comprovante').attr('href', comprovante).show();
        } else {
            $('#modal-taxa-btn-comprovante').hide();
        }

        $('#modal-detalhes-taxa').modal('show');
    });
});
</script>