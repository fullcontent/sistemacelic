@php
    $ciclos = $servico->analiseProtocoloLaudos;
    $cicloAtivo = $servico->cicloAtivo();
@endphp

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-refresh"></i> Controle de Análises (Ciclo de Vida)</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        @if($ciclos->isEmpty())
            <div class="alert alert-info alert-dismissible" style="margin-bottom: 15px;">
                <h4><i class="icon fa fa-info"></i> Sem ciclos ativos!</h4>
                Este serviço ainda não possui um controle de análises ativo. Clique abaixo para iniciar o primeiro ciclo.
            </div>
            
            {!! Form::open(['route' => 'servico.ciclo.iniciar']) !!}
                {!! Form::hidden('servico_id', $servico->id) !!}
                {!! Form::hidden('descricao', 'Primeira Análise') !!}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Número do Protocolo</label>
                            <input type="text" name="protocolo_numero" class="form-control" placeholder="Opcional">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Data do Protocolo</label>
                            <input type="text" name="protocolo_emissao" class="form-control" placeholder="dd/mm/aaaa" data-date-format="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Anexo Protocolo</label>
                            <input type="file" name="protocolo_anexo">
                        </div>
                    </div>
                    <div class="col-md-3" style="padding-top: 25px;">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-play"></i> Iniciar Primeira Análise</button>
                    </div>
                </div>
            {!! Form::close() !!}
        @else
            @if($cicloAtivo)
                <div class="panel panel-warning" style="border-color: #f39c12; margin-bottom: 25px;">
                    <div class="panel-heading" style="background-color: #f39c12; color: #fff;">
                        <h3 class="panel-title" style="display: flex; justify-content: space-between; align-items: center;">
                            <span><strong>Ciclo Ativo:</strong> {{ $cicloAtivo->descricao }}</span>
                            <span class="label label-default" style="background-color: rgba(255,255,255,0.2);">Em Andamento</span>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <!-- Protocolo Column -->
                            <div class="col-md-6" style="border-right: 1px solid #f4f4f4;">
                                <h4><i class="fa fa-file-text-o"></i> Protocolo da Solicitação</h4>
                                @if($cicloAtivo->protocolo)
                                    <table class="table table-condensed table-striped" style="margin-top: 15px;">
                                        <tr>
                                            <th style="width: 150px;">Número:</th>
                                            <td>{{ $cicloAtivo->protocolo->numero ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Data Protocolo:</th>
                                            <td>{{ $cicloAtivo->protocolo->data_protocolo ? \Carbon\Carbon::parse($cicloAtivo->protocolo->data_protocolo)->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                        @if($cicloAtivo->protocolo->anexo)
                                            <tr>
                                                <th>Anexo:</th>
                                                <td>
                                                    <a href="{{ route('servico.ciclo.download', ['tipo' => 'protocolo', 'id' => $cicloAtivo->protocolo->id]) }}" class="btn btn-xs btn-warning" target="_blank">
                                                        <i class="fa fa-download"></i> Baixar Protocolo
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                @else
                                    <p class="text-muted">Nenhum protocolo inicial anexado ao ciclo.</p>
                                    <button class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-anexar-protocolo-ciclo">
                                        <i class="fa fa-plus"></i> Adicionar Protocolo
                                    </button>
                                @endif
                            </div>

                            <!-- Documentos Juntados Column -->
                            <div class="col-md-6">
                                <h4><i class="fa fa-folder-open-o"></i> Documentos Juntados</h4>
                                <div style="margin-top: 15px;">
                                    @if($cicloAtivo->documentos->isEmpty())
                                        <p class="text-muted">Nenhum documento juntado para este ciclo.</p>
                                    @else
                                        <ul class="list-unstyled">
                                            @foreach($cicloAtivo->documentos as $doc)
                                                <li style="margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; padding: 5px; background: #fafafa; border-radius: 4px;">
                                                    <span style="font-size: 0.95em;"><i class="fa fa-file-pdf-o text-red"></i> {{ $doc->nome }}</span>
                                                    <div>
                                                        <a href="{{ route('servico.ciclo.download', ['tipo' => 'documento', 'id' => $doc->id]) }}" class="btn btn-xs btn-default" target="_blank" title="Baixar">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        <a href="{{ route('servico.ciclo.excluirDocumento', ['id' => $doc->id]) }}" class="btn btn-xs btn-danger" onclick="return confirm('Tem certeza que deseja remover este documento?');" title="Excluir">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <button class="btn btn-default btn-xs" data-toggle="modal" data-target="#modal-anexar-documentos-ciclo" style="margin-top: 5px;">
                                        <i class="fa fa-plus"></i> Adicionar Documento
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr style="margin: 20px 0;">

                        <!-- Actions Panel -->
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-registrar-exigencia" style="margin-right: 5px;">
                                    <i class="fa fa-exclamation-triangle"></i> Registrar Exigência (Laudo)
                                </button>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-aprovar-ciclo">
                                    <i class="fa fa-check"></i> Aprovar Análise e Emitir Licença
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Historico de Ciclos anteriores -->
            @if($ciclos->count() > ($cicloAtivo ? 1 : 0))
                <h4 style="margin-top: 25px; margin-bottom: 15px;"><i class="fa fa-history"></i> Histórico de Ciclos Anteriores</h4>
                <div class="box-group" id="accordion-ciclos">
                    @foreach($ciclos as $c)
                        @if($c->status !== 'em_andamento')
                            <div class="panel panel-default" style="margin-bottom: 10px; border-radius: 4px; overflow: hidden; border-left: 4px solid {{ $c->status === 'aprovada' ? '#00a65a' : '#dd4b39' }};">
                                <div class="panel-heading" style="background-color: #fcfcfc;">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion-ciclos" href="#collapse-ciclo-{{ $c->id }}" style="display: flex; justify-content: space-between; align-items: center; text-decoration: none; color: #444;">
                                            <span>
                                                <strong>{{ $c->descricao }}</strong> 
                                                <small class="text-muted" style="margin-left: 10px;">iniciado em {{ $c->created_at->format('d/m/Y') }}</small>
                                            </span>
                                            <div>
                                                @if($c->status === 'aprovada')
                                                    <span class="label label-success">Aprovada</span>
                                                @else
                                                    <span class="label label-danger">Com Exigência</span>
                                                @endif
                                                <i class="fa fa-chevron-down" style="margin-left: 10px; font-size: 0.85em;"></i>
                                            </div>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapse-ciclo-{{ $c->id }}" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <div class="row">
                                            <!-- Protocolo Column -->
                                            <div class="col-md-4" style="border-right: 1px solid #f4f4f4;">
                                                <h5><strong>Protocolo da Solicitação</strong></h5>
                                                @if($c->protocolo)
                                                    <p style="margin-bottom: 5px;"><strong>Número:</strong> {{ $c->protocolo->numero ?: 'N/A' }}</p>
                                                    <p style="margin-bottom: 5px;"><strong>Data:</strong> {{ $c->protocolo->data_protocolo ? \Carbon\Carbon::parse($c->protocolo->data_protocolo)->format('d/m/Y') : 'N/A' }}</p>
                                                    @if($c->protocolo->anexo)
                                                        <a href="{{ route('servico.ciclo.download', ['tipo' => 'protocolo', 'id' => $c->protocolo->id]) }}" class="btn btn-xs btn-default" target="_blank" style="margin-top: 5px;">
                                                            <i class="fa fa-download"></i> Baixar Protocolo
                                                        </a>
                                                    @endif
                                                @else
                                                    <p class="text-muted">Nenhum protocolo inicial.</p>
                                                @endif
                                            </div>

                                            <!-- Documentos Juntados Column -->
                                            <div class="col-md-4" style="border-right: 1px solid #f4f4f4;">
                                                <h5><strong>Documentos Juntados</strong></h5>
                                                @if($c->documentos->isEmpty())
                                                    <p class="text-muted">Nenhum documento.</p>
                                                @else
                                                    <ul class="list-unstyled" style="padding-left: 0;">
                                                        @foreach($c->documentos as $d)
                                                            <li style="margin-bottom: 4px;">
                                                                <a href="{{ route('servico.ciclo.download', ['tipo' => 'documento', 'id' => $d->id]) }}" target="_blank" style="font-size: 0.9em;">
                                                                    <i class="fa fa-file-pdf-o text-red"></i> {{ $d->nome }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>

                                            <!-- Laudo/Desfecho Column -->
                                            <div class="col-md-4">
                                                <h5><strong>Resultado do Ciclo</strong></h5>
                                                @if($c->status === 'aprovada')
                                                    <div class="alert alert-success" style="padding: 10px; margin-bottom: 0;">
                                                        <h5><i class="fa fa-check-circle"></i> Aprovado!</h5>
                                                        Licença final emitida e anexada ao serviço.
                                                    </div>
                                                @elseif($c->laudo)
                                                    <p style="margin-bottom: 5px;"><strong>Laudo de Exigências:</strong></p>
                                                    <p style="margin-bottom: 5px;"><strong>Número:</strong> {{ $c->laudo->numero ?: 'N/A' }}</p>
                                                    <p style="margin-bottom: 5px;"><strong>Data:</strong> {{ $c->laudo->data_emissao ? \Carbon\Carbon::parse($c->laudo->data_emissao)->format('d/m/Y') : 'N/A' }}</p>
                                                    @if($c->laudo->anexo)
                                                        <a href="{{ route('servico.ciclo.download', ['tipo' => 'laudo', 'id' => $c->laudo->id]) }}" class="btn btn-xs btn-danger" target="_blank" style="margin-top: 5px;">
                                                            <i class="fa fa-download"></i> Baixar Laudo de Exigências
                                                        </a>
                                                    @endif
                                                @else
                                                    <p class="text-muted">Nenhum laudo registrado.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Modal 1: Anexar Protocolo ao Ciclo Ativo -->
@if($cicloAtivo)
    <div class="modal fade" id="modal-anexar-protocolo-ciclo" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Adicionar Protocolo</h4>
                </div>
                {!! Form::open(['route' => 'servico.anexarProtocolo', 'enctype' => 'multipart/form-data']) !!}
                    <div class="modal-body">
                        {!! Form::hidden('servico_id', $servico->id) !!}
                        <div class="form-group">
                            <label for="protocolo_numero">N. Protocolo</label>
                            <input type="text" name="protocolo_numero" class="form-control" required id="protocolo_numero">
                        </div>
                        <div class="form-group">
                            <label for="protocolo_emissao">Data do Protocolo</label>
                            <input type="text" name="protocolo_emissao" class="form-control" required id="protocolo_emissao" data-date-format="dd/mm/yyyy" placeholder="dd/mm/aaaa">
                        </div>
                        <div class="form-group">
                            <label for="protocolo_anexo">Anexo do Protocolo</label>
                            <input type="file" name="protocolo_anexo" class="form-control" required id="protocolo_anexo">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Modal 2: Anexar Documentos Juntados ao Ciclo Ativo -->
    <div class="modal fade" id="modal-anexar-documentos-ciclo" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Adicionar Documentos Juntados</h4>
                </div>
                {!! Form::open(['route' => 'servico.ciclo.anexarDocumento', 'enctype' => 'multipart/form-data']) !!}
                    <div class="modal-body">
                        {!! Form::hidden('servico_id', $servico->id) !!}
                        <div class="form-group">
                            <label for="documentos">Arquivos (Você pode selecionar múltiplos)</label>
                            <input type="file" name="documentos[]" class="form-control" required id="documentos" multiple>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Modal 3: Registrar Exigência (Finaliza Ciclo Ativo, Inicia Próximo) -->
    <div class="modal fade" id="modal-registrar-exigencia" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Registrar Exigência (Laudo)</h4>
                </div>
                {!! Form::open(['route' => 'servico.ciclo.registrarExigencia', 'enctype' => 'multipart/form-data']) !!}
                    <div class="modal-body">
                        {!! Form::hidden('servico_id', $servico->id) !!}
                        
                        <!-- Dados do Laudo de Exigências -->
                        <h4 style="border-bottom: 1px solid #f4f4f4; padding-bottom: 8px; margin-bottom: 15px;">Laudo de Exigências Recusado</h4>
                        <div class="form-group">
                            <label for="laudo_numero">N. Laudo de Exigências</label>
                            <input type="text" name="laudo_numero" class="form-control" required id="laudo_numero">
                        </div>
                        <div class="form-group">
                            <label for="laudo_emissao">Data de Emissão do Laudo</label>
                            <input type="text" name="laudo_emissao" class="form-control" required id="laudo_emissao" data-date-format="dd/mm/yyyy" placeholder="dd/mm/aaaa">
                        </div>
                        <div class="form-group">
                            <label for="laudo_anexo">Anexo do Laudo</label>
                            <input type="file" name="laudo_anexo" class="form-control" required id="laudo_anexo">
                        </div>

                        <!-- Dados do Próximo Protocolo/Reapresentação -->
                        <h4 style="border-bottom: 1px solid #f4f4f4; padding-bottom: 8px; margin-top: 25px; margin-bottom: 15px;">Novo Protocolo / Reapresentação (Inicia Próximo Ciclo)</h4>
                        <div class="form-group">
                            <label for="novo_protocolo_numero">N. Novo Protocolo</label>
                            <input type="text" name="novo_protocolo_numero" class="form-control" placeholder="Opcional">
                        </div>
                        <div class="form-group">
                            <label for="novo_protocolo_emissao">Data do Novo Protocolo</label>
                            <input type="text" name="novo_protocolo_emissao" class="form-control" placeholder="dd/mm/aaaa" data-date-format="dd/mm/yyyy">
                        </div>
                        <div class="form-group">
                            <label for="novo_protocolo_anexo">Anexo do Novo Protocolo</label>
                            <input type="file" name="novo_protocolo_anexo" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="novos_documentos_juntados">Documentos Juntados da Reapresentação</label>
                            <input type="file" name="novos_documentos_juntados[]" class="form-control" multiple>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-danger">Confirmar e Iniciar Novo Ciclo</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Modal 4: Aprovar Ciclo e Emitir Licença (Finaliza Ciclo como Aprovado e finaliza Serviço) -->
    <div class="modal fade" id="modal-aprovar-ciclo" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title"><i class="fa fa-check-circle text-green"></i> Aprovar Análise e Emitir Licença Final</h4>
                </div>
                {!! Form::open(['route' => 'servico.ciclo.finalizarCicloSucesso', 'enctype' => 'multipart/form-data']) !!}
                    <div class="modal-body">
                        {!! Form::hidden('servico_id', $servico->id) !!}
                        
                        <div class="form-group">
                            <label for="tipoLicenca">Tipo da Licença</label>
                            {!! Form::select('tipoLicenca', array(
                                'renovavel' => 'Renovável',
                                'n/a' => 'Não aplicada',
                                'definitiva' => 'Definitiva',
                            ), $servico->tipoLicenca, ['class'=>'form-control','id'=>'tipoLicenca_ciclo']) !!}
                        </div>

                        <div class="form-group">
                            <label for="licenca_emissao">Emissão da Licença</label>
                            <input type="text" name="licenca_emissao" class="form-control" required id="licenca_emissao_ciclo" data-date-format="dd/mm/yyyy" placeholder="dd/mm/aaaa">
                        </div>

                        <div class="form-group" id="validade-container-ciclo">
                            <label for="licenca_validade">Validade da Licença</label>
                            <input type="text" name="licenca_validade" class="form-control" required id="licenca_validade_ciclo" data-date-format="dd/mm/yyyy" placeholder="dd/mm/aaaa">
                        </div>

                        <div class="form-group">
                            <label for="licenca_anexo">Documento da Licença / Licença Digital (PDF)</label>
                            <input type="file" name="licenca_anexo" class="form-control" required id="licenca_anexo_ciclo">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-success">Aprovar e Finalizar Serviço</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Toggle validade input script -->
    <script>
        $(document).ready(function() {
            function toggleValidade() {
                var tipo = $('#tipoLicenca_ciclo').val();
                if(tipo === 'n/a' || tipo === 'definitiva') {
                    $('#validade-container-ciclo').hide();
                    $('#licenca_validade_ciclo').removeAttr('required');
                } else {
                    $('#validade-container-ciclo').show();
                    $('#licenca_validade_ciclo').attr('required', 'required');
                }
            }
            $('#tipoLicenca_ciclo').change(toggleValidade);
            toggleValidade();

            // Datepicker initialization fallback if not handled globally
            if (typeof $.fn.datepicker !== 'undefined') {
                $('#protocolo_emissao, #novo_protocolo_emissao, #laudo_emissao, #licenca_emissao_ciclo, #licenca_validade_ciclo').datepicker({
                    autoclose: true,
                    format: 'dd/mm/yyyy',
                    language: 'pt-BR'
                });
            }
        });
    </script>
@endif
