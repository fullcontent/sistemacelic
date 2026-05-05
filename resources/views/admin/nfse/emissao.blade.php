@extends('adminlte::page')

@section('title', 'Emissão de NFS-e')

@section('content_header')
    <h1><i class="fa fa-file-invoice"></i> Emissão de Notas Fiscais (NFS-e)</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-check"></i> Sucesso!</h5>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-ban"></i> Erro na Emissão!</h5>
                <div style="word-wrap: break-word; white-space: pre-wrap;">
                    @if(strpos(session('error'), 'Resposta:') !== false)
                        <?php 
                            $parts = explode(' | Resposta: ', session('error'));
                            $mainMsg = $parts[0];
                            $jsonMsg = $parts[1] ?? '';
                            // Tenta formatar se for JSON
                            $decoded = json_decode($jsonMsg, true);
                            if ($decoded) {
                                $formattedJson = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            } else {
                                $formattedJson = $jsonMsg;
                            }
                        ?>
                        <p>{{ $mainMsg }}</p>
                        <hr style="border-top: 1px solid rgba(255,255,255,0.2)">
                        <strong>Detalhes da API:</strong>
                        <pre style="background: rgba(0,0,0,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); margin-top: 10px; max-height: 300px; overflow-y: auto;">{{ $formattedJson }}</pre>
                    @else
                        {{ session('error') }}
                    @endif
                </div>
            </div>
        @endif

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Faturamento #{{ $faturamento->id }} - {{ $faturamento->empresa->nomeFantasia }}</h3>
            </div>
            
            <form id="formEmissao" action="{{ route('nfse.processar', $faturamento->id) }}" method="POST">
                @csrf
                <div class="box-body">
                    <!-- PASSO 1: SELEÇÃO DA OPÇÃO -->
                    <div class="row">
                        <div class="col-md-12">
                            <h4 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                                <span class="label label-primary">1</span> Escolha o Modo de Emissão
                            </h4>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box bg-aqua option-card" data-option="1" style="cursor: pointer; opacity: 0.7;">
                                <span class="info-box-icon"><i class="fa fa-copy"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>OPÇÃO 1: INDIVIDUAL (PADRÃO)</b></span>
                                    <span class="info-box-number">Uma nota por serviço</span>
                                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                                    <span class="progress-description">Tomador = CNPJ da Unidade do Serviço</span>
                                </div>
                                <input type="radio" name="opcao_automatica" value="1" style="display:none;" id="opt1">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box bg-green option-card" data-option="2" style="cursor: pointer; opacity: 0.7;">
                                <span class="info-box-icon"><i class="fa fa-user-edit"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>OPÇÃO 2: INDIVIDUAL (MANUAL)</b></span>
                                    <span class="info-box-number">Uma nota por serviço</span>
                                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                                    <span class="progress-description">Tomador = CNPJ digitado manualmente</span>
                                </div>
                                <input type="radio" name="opcao_automatica" value="2" style="display:none;" id="opt2">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box bg-purple option-card" data-option="3" style="cursor: pointer; opacity: 0.7;">
                                <span class="info-box-icon"><i class="fa fa-layer-group"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>OPÇÃO 3: AGRUPADA (PADRÃO)</b></span>
                                    <span class="info-box-number">Uma nota única</span>
                                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                                    <span class="progress-description">Tomador = Empresa do faturamento</span>
                                </div>
                                <input type="radio" name="opcao_automatica" value="3" style="display:none;" id="opt3">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box bg-orange option-card" data-option="4" style="cursor: pointer; opacity: 0.7;">
                                <span class="info-box-icon"><i class="fa fa-search"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><b>OPÇÃO 4: AGRUPADA (MANUAL)</b></span>
                                    <span class="info-box-number">Uma nota única</span>
                                    <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                                    <span class="progress-description">Tomador = CNPJ digitado manualmente</span>
                                </div>
                                <input type="radio" name="opcao_automatica" value="4" style="display:none;" id="opt4">
                            </div>
                        </div>
                    </div>

                    <!-- PASSO 2: SELEÇÃO DE SERVIÇOS -->
                    <div class="row" style="margin-top: 30px;">
                        <div class="col-md-12">
                            <h4 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                                <span class="label label-primary">2</span> Serviços Vinculados
                            </h4>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr style="background-color: #f9f9f9;">
                                        <th width="30"><input type="checkbox" id="checkAll"></th>
                                        <th>OS</th>
                                        <th>Unidade</th>
                                        <th>CNPJ Tomador</th>
                                        <th>Serviço</th>
                                        <th>Valor Faturado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($faturamento->servicosFaturados as $item)
                                        @if($item->detalhes)
                                        <tr>
                                            <td><input type="checkbox" name="servico_ids[]" value="{{ $item->servico_id }}" checked class="checkItem"></td>
                                            <td>{{ $item->detalhes->os }}</td>
                                            <td>{{ $item->detalhes->unidade->nomeFantasia ?? '-' }}</td>
                                            <td>
                                                <span class="cnpj-display" data-unit-cnpj="{{ $item->detalhes->unidade->cnpj ?? '-' }}">
                                                    {{ $item->detalhes->unidade->cnpj ?? '-' }}
                                                </span>
                                            </td>
                                            <td>{{ $item->detalhes->nome }}</td>
                                            <td>R$ {{ number_format($item->valorFaturado, 2, ',', '.') }}</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- PASSO 3: DADOS DO TOMADOR -->
                    <div id="sectionTomador" class="row" style="margin-top: 30px; display: none;">
                        <div class="col-md-12">
                            <h4 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                                <span class="label label-primary">3</span> Dados do Tomador (Destinatário)
                            </h4>
                            
                            <div id="tomadorManual" class="box box-warning box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Informar Dados do Tomador</h3>
                                </div>
                                <div class="box-body">
                                    <input type="hidden" name="nova_empresa" id="input_nova_empresa" value="1">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>CNPJ do Tomador:</label>
                                                <div class="input-group">
                                                    <input type="text" name="override_cnpj" id="override_cnpj" class="form-control" placeholder="00.000.000/0000-00">
                                                    <span class="input-group-btn">
                                                        <button type="button" class="btn btn-info btn-flat" id="btnConsutarCnpj"><i class="fa fa-search"></i> Consultar</button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label>Razão Social:</label>
                                                <input type="text" name="override_razaoSocial" id="override_razaoSocial" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>E-mail:</label>
                                                <input type="text" name="override_email" id="override_email" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Logradouro:</label>
                                                <input type="text" name="override_logradouro" id="override_logradouro" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Número:</label>
                                                <input type="text" name="override_numero" id="override_numero" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Bairro:</label>
                                                <input type="text" name="override_bairro" id="override_bairro" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>CEP:</label>
                                                <input type="text" name="override_cep" id="override_cep" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Cidade:</label>
                                                <input type="text" name="override_municipio" id="override_municipio" class="form-control">
                                                <input type="hidden" name="override_codigoCidade" id="override_codigoCidade">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>UF:</label>
                                                <input type="text" name="override_uf" id="override_uf" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="box-footer">
                    <a href="{{ route('faturamento.show', $faturamento->id) }}" class="btn btn-default btn-lg">Cancelar</a>
                    <button type="submit" class="btn btn-primary btn-lg pull-right" id="btnFinalizar" disabled>
                        <i class="fa fa-check"></i> Processar Emissão
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .option-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .option-selected {
        opacity: 1 !important;
        border: 4px solid #3c8dbc !important;
        transform: translateY(-2px);
    }
    .label-primary { font-size: 110%; padding: 0.3em 0.8em; }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    const empresaCnpj = "{{ $faturamento->empresa->cnpj }}";

    function updateCnpjDisplays() {
        const option = $('input[name="opcao_automatica"]:checked').val();
        const manualCnpj = $('#override_cnpj').val();

        $('.cnpj-display').each(function() {
            const unitCnpj = $(this).data('unit-cnpj');
            
            if (option == '1') {
                $(this).text(unitCnpj).removeClass('text-orange text-bold');
            } else if (option == '2' || option == '4') {
                $(this).text(manualCnpj || 'AGUARDANDO CNPJ...').addClass('text-orange text-bold');
            } else if (option == '3') {
                $(this).text(empresaCnpj).removeClass('text-orange text-bold');
            }
        });
    }

    // Seleção de card
    $('.option-card').on('click', function() {
        $('.option-card').removeClass('option-selected');
        $(this).addClass('option-selected');
        
        const option = $(this).data('option');
        $(`#opt${option}`).prop('checked', true);
        
        $('#btnFinalizar').prop('disabled', false);
        
        // Se for opção 2 ou 4 (Manual), mostrar seção do tomador
        if(option == 2 || option == 4) {
            $('#sectionTomador').fadeIn();
            $('#input_nova_empresa').val('1');
        } else {
            $('#sectionTomador').fadeOut();
            $('#input_nova_empresa').val('0');
        }

        updateCnpjDisplays();
    });

    // CheckAll logic
    $('#checkAll').on('change', function() {
        $('.checkItem').prop('checked', $(this).is(':checked'));
    });



    // Atualizar displays ao digitar CNPJ manual
    $('#override_cnpj').on('input', function() {
        updateCnpjDisplays();
    });

    // Consulta CNPJ
    $('#btnConsutarCnpj').on('click', function() {
        const cnpj = $('#override_cnpj').val();
        if(!cnpj) return;

        $(this).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ url('admin/nfse/buscar-cnpj') }}/" + cnpj,
            success: function(response) {
                $('#override_razaoSocial').val(response.razaoSocial);
                $('#override_email').val(response.email);
                $('#override_logradouro').val(response.logradouro);
                $('#override_numero').val(response.numero);
                $('#override_bairro').val(response.bairro);
                $('#override_cep').val(response.cep);
                $('#override_municipio').val(response.municipio);
                $('#override_uf').val(response.uf);
                $('#override_codigoCidade').val(response.ibge); // Se disponível
                
                updateCnpjDisplays();
                Swal.fire('Sucesso', 'Dados recuperados da Receita Federal!', 'success');
            },
            error: function() {
                Swal.fire('Erro', 'Não foi possível localizar este CNPJ.', 'error');
            },
            complete: function() {
                $('#btnConsutarCnpj').html('<i class="fa fa-search"></i> Consultar');
            }
        });
    });

    // Validar form antes de enviar
    $('#formEmissao').on('submit', function(e) {
        if($('.checkItem:checked').length == 0) {
            e.preventDefault();
            Swal.fire('Erro', 'Selecione pelo menos um serviço para emitir a nota.', 'error');
            return;
        }

        // Mostrar Barra de Progresso / Overlay de Carregamento
        Swal.fire({
            title: 'Processando Emissão...',
            html: `
                <p>Por favor, aguarde enquanto comunicamos com a prefeitura.</p>
                <div class="progress progress-striped active" style="margin-bottom:0;">
                    <div class="progress-bar progress-bar-primary" style="width: 100%"></div>
                </div>
                <p style="margin-top:10px;"><small>Este processo pode levar até 3 minutos.</small></p>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
});
</script>
@stop
