@extends('adminlte::page')

@section('title', 'Configurações NFS-e')

@section('content_header')
    <h1>Configurações de Emissão NFS-e</h1>
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
            <div class="tab-content" style="background-color: #f4f6f9;">
                @foreach($dadosCastros as $index => $dc)
                <div class="tab-pane {{ $index == 0 ? 'active' : '' }}" id="tab_{{ $dc->id }}">
                    <form class="nfse-config-form" data-id="{{ $dc->id }}">
                        @csrf
                        <input type="hidden" name="dados_castro_id" value="{{ $dc->id }}">
                        
                        <div class="box box-solid" style="border-top: 3px solid #7aa2c9; margin-top: 15px;">
                            <div class="box-header with-border" style="background-color: #f4f6f9;">
                                <h3 class="box-title" style="color: #354256;">Dados Cadastrais da Empresa</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Razão Social</label>
                                            <input type="text" name="dados_castro[razaoSocial]" class="form-control" value="{{ $dc->razaoSocial }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>CNPJ</label>
                                            <div class="input-group">
                                                <input type="text" name="dados_castro[cnpj]" class="form-control input-cnpj" value="{{ $dc->cnpj }}" required>
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-warning btn-flat btn-pesquisar-cnpj"><i class="fa fa-search"></i> Buscar</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Banco</label>
                                            <input type="text" name="dados_castro[banco]" class="form-control" value="{{ $dc->banco }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Agência</label>
                                            <input type="text" name="dados_castro[agencia]" class="form-control" value="{{ $dc->agencia }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Conta</label>
                                            <input type="text" name="dados_castro[conta]" class="form-control" value="{{ $dc->conta }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Chave PIX</label>
                                            <input type="text" name="dados_castro[chavePix]" class="form-control" value="{{ $dc->chavePix }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box box-solid" style="border-top: 3px solid #354256;">
                            <div class="box-header with-border" style="background-color: #eaeaec;">
                                <h3 class="box-title" style="color: #354256;">Parâmetros de Emissão (PlugNotas)</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Emitir nota como:</label>
                                            <select name="emitir_como" class="form-control">
                                                <option value="Prestador" {{ ($dc->nfseConfiguration->emitir_como ?? '') == 'Prestador' ? 'selected' : '' }}>Prestador (Padrão)</option>
                                                <option value="Tomador" {{ ($dc->nfseConfiguration->emitir_como ?? '') == 'Tomador' ? 'selected' : '' }}>Tomador</option>
                                                <option value="Intermediário" {{ ($dc->nfseConfiguration->emitir_como ?? '') == 'Intermediário' ? 'selected' : '' }}>Intermediário</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Regime de Apuração:</label>
                                            <select name="regime_apuracao" class="form-control">
                                                <option value="Simples Nacional" {{ ($dc->nfseConfiguration->regime_apuracao ?? '') == 'Simples Nacional' ? 'selected' : '' }}>Simples Nacional</option>
                                                <option value="Microempreendedor Individual (MEI)" {{ ($dc->nfseConfiguration->regime_apuracao ?? '') == 'Microempreendedor Individual (MEI)' ? 'selected' : '' }}>Microempreendedor Individual (MEI)</option>
                                                <option value="Lucro Presumido" {{ ($dc->nfseConfiguration->regime_apuracao ?? '') == 'Lucro Presumido' ? 'selected' : '' }}>Lucro Presumido</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tomador do Serviço:</label>
                                            <select name="tomador_servico" class="form-control">
                                                <option value="Brasil" {{ ($dc->nfseConfiguration->tomador_servico ?? '') == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                                                <option value="Exterior" {{ ($dc->nfseConfiguration->tomador_servico ?? '') == 'Exterior' ? 'selected' : '' }}>Exterior</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Código de Tributação Nacional:</label>
                                            <input type="text" name="codigo_tributacao_nacional" class="form-control" value="{{ $dc->nfseConfiguration->codigo_tributacao_nacional ?? '17.02.02' }}" placeholder="Ex: 17.02.02">
                                            <small class="text-muted">Serviço de Licenciamento (Padrão)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Item NBS:</label>
                                            <input type="text" name="item_nbs" class="form-control" value="{{ $dc->nfseConfiguration->item_nbs ?? '118064000' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Alíquota Simples Nacional (%):</label>
                                            <input type="number" step="0.01" name="aliquota_simples_nacional" class="form-control" value="{{ $dc->nfseConfiguration->aliquota_simples_nacional ?? '9.90' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>ISSQN Retido?</label>
                                            <select name="issqn_retido" class="form-control">
                                                <option value="0" {{ !($dc->nfseConfiguration->issqn_retido ?? false) ? 'selected' : '' }}>Não</option>
                                                <option value="1" {{ ($dc->nfseConfiguration->issqn_retido ?? false) ? 'selected' : '' }}>Sim</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Benefício Municipal?</label>
                                            <select name="beneficio_municipal" class="form-control">
                                                <option value="0" {{ !($dc->nfseConfiguration->beneficio_municipal ?? false) ? 'selected' : '' }}>Não</option>
                                                <option value="1" {{ ($dc->nfseConfiguration->beneficio_municipal ?? false) ? 'selected' : '' }}>Sim</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Situação PIS/COFINS:</label>
                                            <input type="text" name="pis_cofins_situacao" class="form-control" value="{{ $dc->nfseConfiguration->pis_cofins_situacao ?? '00' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Inscrição Municipal:</label>
                                            <input type="text" name="inscricao_municipal" class="form-control" value="{{ $dc->nfseConfiguration->inscricao_municipal ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Email Emitente:</label>
                                            <input type="email" name="email_emitente" class="form-control" value="{{ $dc->nfseConfiguration->email_emitente ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Telefone:</label>
                                            <input type="text" name="telefone_emitente" class="form-control" value="{{ $dc->nfseConfiguration->telefone_emitente ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                <h4 style="border-bottom: 2px solid #7aa2c9; padding-bottom: 5px; margin-top: 20px;">Endereço do Emitente (Obrigatório para registro)</h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Logradouro:</label>
                                            <input type="text" name="logradouro" class="form-control" value="{{ $dc->nfseConfiguration->logradouro ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Número:</label>
                                            <input type="text" name="numero" class="form-control" value="{{ $dc->nfseConfiguration->numero ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Bairro:</label>
                                            <input type="text" name="bairro" class="form-control" value="{{ $dc->nfseConfiguration->bairro ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>CEP:</label>
                                            <input type="text" name="cep" class="form-control" value="{{ $dc->nfseConfiguration->cep ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Código Cidade (IBGE):</label>
                                            <input type="text" name="codigo_cidade" class="form-control" value="{{ $dc->nfseConfiguration->codigo_cidade ?? '4202008' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>UF:</label>
                                            <input type="text" name="uf" class="form-control" value="{{ $dc->nfseConfiguration->uf ?? 'SC' }}" maxlength="2">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Regime Tributário (PlugNotas):</label>
                                            <select name="regime_tributario" class="form-control">
                                                <option value="1" {{ ($dc->nfseConfiguration->regime_tributario ?? '') == '1' ? 'selected' : '' }}>Simples Nacional</option>
                                                <option value="2" {{ ($dc->nfseConfiguration->regime_tributario ?? '') == '2' ? 'selected' : '' }}>Simples Nacional - MEI</option>
                                                <option value="3" {{ ($dc->nfseConfiguration->regime_tributario ?? '') == '3' ? 'selected' : '' }}>Lucro Presumido</option>
                                                <option value="4" {{ ($dc->nfseConfiguration->regime_tributario ?? '') == '4' ? 'selected' : '' }}>Lucro Real</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <h4 style="border-bottom: 2px solid #7aa2c9; padding-bottom: 5px; margin-top: 20px;">Credenciais e Certificado (Opcional)</h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>ID Certificado (PlugNotas):</label>
                                            <input type="text" name="certificado" class="form-control" value="{{ $dc->nfseConfiguration->certificado ?? '' }}" placeholder="Ex: 5af59d271f6... ">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Login Prefeitura:</label>
                                            <input type="text" name="login_prefeitura" class="form-control" value="{{ $dc->nfseConfiguration->login_prefeitura ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Senha Prefeitura:</label>
                                            <input type="password" name="senha_prefeitura" class="form-control" value="{{ $dc->nfseConfiguration->senha_prefeitura ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Ambiente:</label>
                                            <select name="producao" class="form-control">
                                                <option value="0" {{ !($dc->nfseConfiguration->producao ?? false) ? 'selected' : '' }}>Sandbox</option>
                                                <option value="1" {{ ($dc->nfseConfiguration->producao ?? false) ? 'selected' : '' }}>Produção</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer" style="background-color: #eaeaec;">
                                <button type="button" class="btn btn-flat btn-danger btn-delete-emitente" data-id="{{ $dc->id }}" style="margin-right: 15px;">
                                    <i class="fa fa-trash"></i> Desativar Empresa
                                </button>
                                <button type="button" class="btn btn-flat btn-info btn-sync-plugnotas" data-id="{{ $dc->id }}">
                                    <i class="fa fa-refresh"></i> Sincronizar com PlugNotas
                                </button>
                                <button type="submit" class="btn btn-flat pull-right" style="background-color: #354256; color: white;">
                                    <i class="fa fa-save"></i> Salvar Configurações
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
<div class="modal fade" id="modalNovoEmitente" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #354256; color: white;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-building"></i> Nova Empresa Emitente</h4>
            </div>
            <form action="{{ route('nfse.emitente.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Insira o CNPJ e clique em buscar. Traremos todos os dados disponíveis.</p>
                    <div class="form-group">
                        <label>CNPJ</label>
                        <div class="input-group">
                            <input type="text" name="cnpj" class="form-control input-cnpj" placeholder="00.000.000/0000-00" required>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-warning btn-flat btn-pesquisar-cnpj"><i class="fa fa-search"></i> Buscar</button>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Razão Social</label>
                        <input type="text" name="razaoSocial" class="form-control" required>
                    </div>

                    <!-- Campos Ocultos para Autopreenchimento da ReceitaWS -->
                    <input type="hidden" name="logradouro" value="">
                    <input type="hidden" name="numero" value="">
                    <input type="hidden" name="bairro" value="">
                    <input type="hidden" name="cep" value="">
                    <input type="hidden" name="uf" value="">
                    <input type="hidden" name="telefone_emitente" value="">
                    <input type="hidden" name="email_emitente" value="">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #354256;">Cadastrar e Configurar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
    .nav-tabs-custom > .nav-tabs > li.active {
        border-top-color: #354256;
    }
    .form-control:focus {
        border-color: #7aa2c9;
        box-shadow: none;
    }
</style>
@stop

@section('js')
<script>
$(function() {
    $('.nfse-config-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serialize();
        
        $.ajax({
            url: "{{ route('nfse.config.save') }}",
            type: "POST",
            data: data,
            success: function(response) {
                if(response.success) {
                    Swal.fire('Sucesso!', response.message, 'success');
                }
            },
            error: function() {
                Swal.fire('Erro', 'Erro ao salvar configurações.', 'error');
            }
        });
    });

    // Clique no botão de Sincronizar
    $('.btn-sync-plugnotas').on('click', function() {
        var btn = $(this);
        var id = btn.data('id');
        var form = btn.closest('form');
        
        // Primeiro salva as configurações locais para garantir que estão atualizadas
        $.ajax({
            url: "{{ route('nfse.config.save') }}",
            type: "POST",
            data: form.serialize(),
            success: function(saveRes) {
                if(saveRes.success) {
                    // Após salvar, dispara a sincronização
                    btn.html('<i class="fa fa-spinner fa-spin"></i> Sincronizando...').prop('disabled', true);
                    
                    $.ajax({
                        url: "{{ route('nfse.sync') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            dados_castro_id: id
                        },
                        success: function(syncRes) {
                            if(syncRes.success) {
                                Swal.fire('Sucesso!', 'Empresa sincronizada com PlugNotas.', 'success');
                            } else {
                                Swal.fire('Erro na API', syncRes.message || 'Erro ao sincronizar.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Erro', 'Erro na comunicação com o servidor Celic.', 'error');
                        },
                        complete: function() {
                            btn.html('<i class="fa fa-refresh"></i> Sincronizar com PlugNotas').prop('disabled', false);
                        }
                    });
                }
            }
        });
    });

    // Desativar Empresa
    $('.btn-delete-emitente').on('click', function() {
        var btn = $(this);
        var id = btn.data('id');

        Swal.fire({
            title: 'Você tem certeza?',
            text: "Esta empresa será ocultada do painel de configurações e do formulário de emissão. Nenhum faturamento passado será afetado.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, desativar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true);
                $.ajax({
                    url: "/admin/nfse/configuracoes/emitente/" + id,
                    type: "POST",
                    data: {
                        _method: 'DELETE',
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire('Desativada!', response.message, 'success').then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false);
                        Swal.fire('Erro', 'Ocorreu um problema ao desativar.', 'error');
                    }
                });
            }
        });
    });

    // Função de formato
    function formatText(text) {
        if(!text) return '';
        var loweredText = text.toLowerCase();
        var words = loweredText.split(" ");
        for (var a = 0; a < words.length; a++) {
            var w = words[a];
            if(w.length > 0) {
                var firstLetter = w[0];
                w = firstLetter.toUpperCase() + w.slice(1);
                words[a] = w;
            }
        }
        return words.join(" ");
    }

    // Pesquisar CNPJ na ReceitaWS
    $('.btn-pesquisar-cnpj').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        
        // Pega o container do escopo atual (seja do modal ou da form tab).
        var container = btn.closest('.modal-body');
        if (container.length === 0) {
            container = btn.closest('.box-body');
        }

        var inputCnpj = container.find('.input-cnpj');
        var cnpj = inputCnpj.val().replace(/[^0-9]/g, '');

        if (cnpj.length == 14) {
            btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

            $.ajax({
                url: 'https://www.receitaws.com.br/v1/cnpj/' + cnpj,
                method: 'GET',
                dataType: 'jsonp',
                success: function (response) {
                    if (response && response.status == 'OK') {
                        // Preenche Razão Social genérica
                        var razaoSocial = container.find('input[name="razaoSocial"], input[name="dados_castro[razaoSocial]"]');
                        if(razaoSocial.length > 0) razaoSocial.val(formatText(response.nome));

                        // Preenche os outros dados fiscais se existirem no form do escopo
                        var endereco = container.find('input[name="logradouro"]');
                        if (endereco.length > 0) {
                            endereco.val(formatText(response.logradouro));
                            container.find('input[name="numero"]').val(response.numero);
                            container.find('input[name="cep"]').val(response.cep);
                            container.find('input[name="bairro"]').val(formatText(response.bairro));
                            // Não setamos o codigo_cidade do IBGE pois a API da Receita traz só o nome e UF.
                            container.find('input[name="uf"]').val(response.uf);
                            
                            var telefone = container.find('input[name="telefone_emitente"]');
                            if(telefone.length > 0) telefone.val(response.telefone);
                            var email = container.find('input[name="email_emitente"]');
                            if(email.length > 0) email.val(response.email);
                        } else {
                            // Estamos no modal, setar os hiddens
                            container.find('input[name="logradouro"]').val(formatText(response.logradouro));
                            container.find('input[name="numero"]').val(response.numero);
                            container.find('input[name="cep"]').val(response.cep);
                            container.find('input[name="bairro"]').val(formatText(response.bairro));
                            container.find('input[name="uf"]').val(response.uf);
                            container.find('input[name="telefone_emitente"]').val(response.telefone);
                            container.find('input[name="email_emitente"]').val(response.email);
                        }

                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Dados obtidos com sucesso. Verifique o preenchimento.', showConfirmButton: false, timer: 3000 });
                    } else {
                        Swal.fire('Erro', response ? response.message : 'Não foi possível buscar os dados (verifique o CNPJ)', 'warning');
                    }
                    
                    btn.html('<i class="fa fa-search"></i> Buscar').prop('disabled', false);
                },
                error: function() {
                    Swal.fire('Erro', 'Falha na comunicação com a Receita Federal.', 'error');
                    btn.html('<i class="fa fa-search"></i> Buscar').prop('disabled', false);
                }
            });
        } else {
            Swal.fire('Atenção', 'CNPJ deve conter 14 dígitos válidos.', 'error');
        }
    });
});
</script>
@stop
