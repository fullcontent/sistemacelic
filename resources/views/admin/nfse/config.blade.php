@extends('adminlte::page')

@section('title', 'Configurações NFS-e')

@section('content_header')
    <h1><i class="fa fa-cogs"></i> Configurações de Emissão NFS-e</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12" style="margin-bottom: 20px;">
        <button class="btn btn-success" data-toggle="modal" data-target="#modalNovoEmitente">
            <i class="fa fa-plus"></i> Nova Empresa Emitente
        </button>
    </div>

    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                @foreach($dadosCastros as $index => $dc)
                    <li class="{{ $index == 0 ? 'active' : '' }}">
                        <a href="#tab_{{ $dc->id }}" data-toggle="tab"><b>{{ $dc->razaoSocial }}</b></a>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content" style="background-color: #f4f6f9; padding: 20px;">
                @foreach($dadosCastros as $index => $dc)
                <div class="tab-pane {{ $index == 0 ? 'active' : '' }}" id="tab_{{ $dc->id }}">
                    <form class="nfse-config-form" data-id="{{ $dc->id }}">
                        @csrf
                        <input type="hidden" name="dados_castro_id" value="{{ $dc->id }}">
                        
                        <!-- SEÇÃO 1: DADOS CADASTRAIS -->
                        <div class="box box-solid section-box">
                            <div class="box-header with-border">
                                <h3 class="box-title text-primary"><i class="fa fa-building"></i> 1. Dados Cadastrais e Bancários</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Razão Social</label>
                                            <input type="text" name="dados_castro[razaoSocial]" class="form-control" value="{{ $dc->razaoSocial }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>CNPJ</label>
                                            <div class="input-group">
                                                <input type="text" name="dados_castro[cnpj]" class="form-control input-cnpj" value="{{ $dc->cnpj }}" required>
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-warning btn-flat btn-pesquisar-cnpj"><i class="fa fa-search"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Inscrição Municipal</label>
                                            <input type="text" name="inscricao_municipal" class="form-control" value="{{ $dc->nfseConfiguration->inscricao_municipal ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Chave PIX</label>
                                            <input type="text" name="dados_castro[chavePix]" class="form-control" value="{{ $dc->chavePix }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Email Emitente</label>
                                            <input type="email" name="email_emitente" class="form-control" value="{{ $dc->nfseConfiguration->email_emitente ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Telefone</label>
                                            <input type="text" name="telefone_emitente" class="form-control" value="{{ $dc->nfseConfiguration->telefone_emitente ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="ativo" {{ ($dc->nfseConfiguration->ativo ?? true) ? 'checked' : '' }}> Emitente Ativo
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO 2: ENDEREÇO FISCAL -->
                        <div class="box box-solid section-box">
                            <div class="box-header with-border">
                                <h3 class="box-title text-primary"><i class="fa fa-map-marker"></i> 2. Endereço Fiscal (IBGE/Município)</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Logradouro / Rua</label>
                                            <input type="text" name="logradouro" class="form-control" value="{{ $dc->nfseConfiguration->logradouro ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Número</label>
                                            <input type="text" name="numero" class="form-control" value="{{ $dc->nfseConfiguration->numero ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Bairro</label>
                                            <input type="text" name="bairro" class="form-control" value="{{ $dc->nfseConfiguration->bairro ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>CEP</label>
                                            <input type="text" name="cep" class="form-control" value="{{ $dc->nfseConfiguration->cep ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Município (Nome)</label>
                                            <input type="text" name="municipio_nome" class="form-control" value="{{ $dc->nfseConfiguration->municipio_nome ?? 'Balneário Camboriú' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>UF</label>
                                            <input type="text" name="uf" class="form-control text-uppercase" value="{{ $dc->nfseConfiguration->uf ?? 'SC' }}" maxlength="2">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cód. Município IBGE <i class="fa fa-info-circle text-info" title="Consulte o código IBGE do seu município para garantir a emissão correta."></i></label>
                                            <input type="text" name="codigo_cidade" class="form-control" value="{{ $dc->nfseConfiguration->codigo_cidade ?? '4202008' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO 3: PARÂMETROS TRIBUTÁRIOS -->
                        <div class="box box-solid section-box">
                            <div class="box-header with-border">
                                <h3 class="box-title text-primary"><i class="fa fa-percent"></i> 3. Parâmetros Tributários e Fiscais</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Regime de Apuração</label>
                                            <select name="simples_regime" class="form-control select2">
                                                <option value="Simples Nacional" {{ ($dc->nfseConfiguration->simples_regime ?? '') == 'Simples Nacional' ? 'selected' : '' }}>Simples Nacional</option>
                                                <option value="Microempreendedor Individual (MEI)" {{ ($dc->nfseConfiguration->simples_regime ?? '') == 'Microempreendedor Individual (MEI)' ? 'selected' : '' }}>Microempreendedor Individual (MEI)</option>
                                                <option value="Lucro Presumido" {{ ($dc->nfseConfiguration->simples_regime ?? '') == 'Lucro Presumido' ? 'selected' : '' }}>Lucro Presumido</option>
                                                <option value="Lucro Real" {{ ($dc->nfseConfiguration->simples_regime ?? '') == 'Lucro Real' ? 'selected' : '' }}>Lucro Real</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Alíquota Simples Nacional (%)</label>
                                            <input type="number" step="0.01" name="aliquota_simples" class="form-control" value="{{ $dc->nfseConfiguration->aliquota_simples ?? '9.90' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Situação PIS/COFINS (CST)</label>
                                            <input type="text" name="pis_cofins_situacao" class="form-control" value="{{ $dc->nfseConfiguration->pis_cofins_situacao ?? '00' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Cód. Tributação Nacional (LC 116)</label>
                                            <input type="text" name="codigo_tributacao_nacional" class="form-control" value="{{ $dc->nfseConfiguration->codigo_tributacao_nacional ?? '17.02.02' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Item NBS</label>
                                            <input type="text" name="item_nbs" class="form-control" value="{{ $dc->nfseConfiguration->item_nbs ?? '118064000' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tipo Tributação ISS</label>
                                            <select name="tipo_tributacao_iss" class="form-control">
                                                <option value="" {{ empty($dc->nfseConfiguration->tipo_tributacao_iss) ? 'selected' : '' }}>Padrão (Automático)</option>
                                                <option value="1" {{ ($dc->nfseConfiguration->tipo_tributacao_iss ?? '') == '1' ? 'selected' : '' }}>1 - Tributável no Município</option>
                                                <option value="2" {{ ($dc->nfseConfiguration->tipo_tributacao_iss ?? '') == '2' ? 'selected' : '' }}>2 - Tributável fora do Município</option>
                                                <option value="3" {{ ($dc->nfseConfiguration->tipo_tributacao_iss ?? '') == '3' ? 'selected' : '' }}>3 - Isenção / Substituição</option>
                                                <option value="4" {{ ($dc->nfseConfiguration->tipo_tributacao_iss ?? '') == '4' ? 'selected' : '' }}>4 - Imunidade / Não Incidência</option>
                                                <option value="5" {{ ($dc->nfseConfiguration->tipo_tributacao_iss ?? '') == '5' ? 'selected' : '' }}>5 - Suspensa (Decisão Jud.)</option>
                                                <option value="6" {{ ($dc->nfseConfiguration->tipo_tributacao_iss ?? '') == '6' ? 'selected' : '' }}>6 - Suspensa (Proc. Adm.)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Exigibilidade ISS</label>
                                            <select name="exigibilidade_iss" class="form-control">
                                                <option value="" {{ empty($dc->nfseConfiguration->exigibilidade_iss) ? 'selected' : '' }}>Padrão (Automático)</option>
                                                <option value="1" {{ ($dc->nfseConfiguration->exigibilidade_iss ?? '') == '1' ? 'selected' : '' }}>1 - Exigível</option>
                                                <option value="2" {{ ($dc->nfseConfiguration->exigibilidade_iss ?? '') == '2' ? 'selected' : '' }}>2 - Imunidade / Não Incidência</option>
                                                <option value="3" {{ ($dc->nfseConfiguration->exigibilidade_iss ?? '') == '3' ? 'selected' : '' }}>3 - Isenção</option>
                                                <option value="4" {{ ($dc->nfseConfiguration->exigibilidade_iss ?? '') == '4' ? 'selected' : '' }}>4 - Não Incidência / Exportação</option>
                                                <option value="5" {{ ($dc->nfseConfiguration->exigibilidade_iss ?? '') == '5' ? 'selected' : '' }}>5 - Suspensa (Decisão Jud.)</option>
                                                <option value="6" {{ ($dc->nfseConfiguration->exigibilidade_iss ?? '') == '6' ? 'selected' : '' }}>6 - Suspensa (Proc. Adm.)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="issqn_retido" {{ ($dc->nfseConfiguration->issqn_retido ?? false) ? 'checked' : '' }}> ISSQN Retido
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="beneficio_municipal" {{ ($dc->nfseConfiguration->beneficio_municipal ?? false) ? 'checked' : '' }}> Benefício Municipal
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="suspensao_exigibilidade_issqn" {{ ($dc->nfseConfiguration->suspensao_exigibilidade_issqn ?? false) ? 'checked' : '' }}> Suspensão Exigibilidade
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="issqn_exigibilidade_suspensa" {{ ($dc->nfseConfiguration->issqn_exigibilidade_suspensa ?? false) ? 'checked' : '' }}> Exigibilidade Suspensa
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO 4: CONFIGURAÇÕES TÉCNICAS -->
                        <div class="box box-solid section-box" style="border-bottom: 2px solid #3c8dbc;">
                            <div class="box-header with-border">
                                <h3 class="box-title text-primary"><i class="fa fa-key"></i> 4. Integração e Certificado (PlugNotas)</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ID Certificado (PlugNotas)</label>
                                            <input type="text" name="certificado" class="form-control" value="{{ $dc->nfseConfiguration->certificado ?? '' }}" placeholder="Cole o ID do certificado da PlugNotas">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Login Prefeitura</label>
                                            <input type="text" name="login_prefeitura" class="form-control" value="{{ $dc->nfseConfiguration->login_prefeitura ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Senha Prefeitura</label>
                                            <input type="password" name="senha_prefeitura" class="form-control" value="{{ $dc->nfseConfiguration->senha_prefeitura ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Ambiente</label>
                                            <select name="producao" class="form-control">
                                                <option value="0" {{ !($dc->nfseConfiguration->producao ?? false) ? 'selected' : '' }}>Sandbox (Testes)</option>
                                                <option value="1" {{ ($dc->nfseConfiguration->producao ?? false) ? 'selected' : '' }}>Produção (Real)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tipo Tomador</label>
                                            <select name="tomador_tipo" class="form-control">
                                                <option value="Brasil" {{ ($dc->nfseConfiguration->tomador_tipo ?? '') == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                                                <option value="Exterior" {{ ($dc->nfseConfiguration->tomador_tipo ?? '') == 'Exterior' ? 'selected' : '' }}>Exterior</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Regime Tributário (PlugNotas)</label>
                                            <select name="regime_tributario" class="form-control">
                                                <option value="1" {{ ($dc->nfseConfiguration->regime_tributario ?? '') == '1' ? 'selected' : '' }}>Simples Nacional</option>
                                                <option value="2" {{ ($dc->nfseConfiguration->regime_tributario ?? '') == '2' ? 'selected' : '' }}>Simples Nacional - MEI</option>
                                                <option value="3" {{ ($dc->nfseConfiguration->regime_tributario ?? '') == '3' ? 'selected' : '' }}>Lucro Presumido</option>
                                                <option value="4" {{ ($dc->nfseConfiguration->regime_tributario ?? '') == '4' ? 'selected' : '' }}>Lucro Real</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                         <div class="form-group">
                                            <label>Mapear como:</label>
                                            <select name="emit_as" class="form-control">
                                                <option value="Prestador" {{ ($dc->nfseConfiguration->emit_as ?? '') == 'Prestador' ? 'selected' : '' }}>Prestador</option>
                                                <option value="Tomador" {{ ($dc->nfseConfiguration->emit_as ?? '') == 'Tomador' ? 'selected' : '' }}>Tomador</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer" style="padding: 20px;">
                                <button type="button" class="btn btn-default btn-flat btn-delete-emitente" data-id="{{ $dc->id }}">
                                    <i class="fa fa-trash"></i> Desativar
                                </button>
                                <button type="button" class="btn btn-info btn-flat btn-sync-plugnotas" data-id="{{ $dc->id }}">
                                    <i class="fa fa-refresh"></i> Sincronizar PlugNotas
                                </button>
                                <button type="submit" class="btn btn-primary btn-flat pull-right btn-lg">
                                    <i class="fa fa-save"></i> SALVAR CONFIGURAÇÕES
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Nova Empresa -->
<div class="modal fade" id="modalNovoEmitente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Nova Empresa Emitente</h4>
            </div>
            <form action="{{ route('nfse.emitente.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>CNPJ</label>
                        <div class="input-group">
                            <input type="text" name="cnpj" class="form-control input-cnpj" required>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-warning btn-flat btn-pesquisar-cnpj"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Razão Social</label>
                        <input type="text" name="razaoSocial" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .section-box { border-top: 3px solid #d2d6de; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .section-box .box-header { background: #fff; }
    .nav-tabs-custom > .nav-tabs > li.active { border-top-color: #3c8dbc; }
</style>
@stop

@section('js')
<script>
$(function() {
    $('.input-cnpj').mask('00.000.000/0000-00');

    $('.nfse-config-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        btn.html('<i class="fa fa-spinner fa-spin"></i> SALVANDO...').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('nfse.config.save') }}",
            type: "POST",
            data: form.serialize(),
            success: function(response) {
                if(response.success) {
                    Swal.fire('Sucesso!', 'Configurações salvas com sucesso!', 'success');
                }
            },
            error: function(err) {
                Swal.fire('Erro', 'Falha ao salvar. Verifique os dados.', 'error');
            },
            complete: function() {
                btn.html('<i class="fa fa-save"></i> SALVAR CONFIGURAÇÕES').prop('disabled', false);
            }
        });
    });

    $('.btn-pesquisar-cnpj').on('click', function() {
        var btn = $(this);
        var container = btn.closest('.form-group').parent();
        var cnpj = container.find('.input-cnpj').val().replace(/\D/g, '');
        
        if (cnpj.length !== 14) return;

        btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
        $.ajax({
            url: 'https://www.receitaws.com.br/v1/cnpj/' + cnpj,
            method: 'GET',
            dataType: 'jsonp',
            success: function (response) {
                if (response.status == 'OK') {
                    if (container.closest('.modal-body').length) {
                         container.closest('.modal-body').find('input[name="razaoSocial"]').val(response.nome);
                    } else {
                         container.closest('.box-body').find('input[name="dados_castro[razaoSocial]"]').val(response.nome);
                         // Preencher endereço se estiver na aba
                         container.closest('.nfse-config-form').find('input[name="logradouro"]').val(response.logradouro);
                         container.closest('.nfse-config-form').find('input[name="numero"]').val(response.numero);
                         container.closest('.nfse-config-form').find('input[name="bairro"]').val(response.bairro);
                         container.closest('.nfse-config-form').find('input[name="cep"]').val(response.cep);
                         container.closest('.nfse-config-form').find('input[name="uf"]').val(response.uf);
                         container.closest('.nfse-config-form').find('input[name="municipio_nome"]').val(response.municipio);
                    }
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Dados recuperados!' });
                }
            },
            complete: function() {
                btn.html('<i class="fa fa-search"></i>').prop('disabled', false);
            }
        });
    });
});
</script>
@stop
