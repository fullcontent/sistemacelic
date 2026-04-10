@extends('adminlte::page')

@section('title', 'Área de Emissão de Notas Fiscais')

@section('content_header')
    <h1><i class="fa fa-file-invoice"></i> Área de Emissão de Notas Fiscais</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({ title: 'Sucesso!', text: "{{ session('success') }}", icon: 'success' });
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({ title: 'Erro na Emissão', text: "{{ session('error') }}", icon: 'error' });
                });
            </script>
        @endif

        <div class="box box-solid" style="border-top: 3px solid #354256;">
            <div class="box-header with-border" style="background-color: #eaeaec;">
                <h3 class="box-title" style="color: #354256;">Faturamento #{{ $faturamento->id }} - {{ $faturamento->empresa->nomeFantasia }}</h3>
            </div>
            
            <form action="{{ route('nfse.processar', $faturamento->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="box-body">
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-12">
                            <label>Empresa Emitente (Prestador):</label>
                            <select name="dados_castro_id" class="form-control" style="border-color: #7aa2c9;">
                                @foreach($empresasDisponiveis as $emp)
                                    <option value="{{ $emp->id }}" {{ $dadosCastro && $dadosCastro->id == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->cnpj }} - {{ $emp->razaoSocial }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs nav-justified">
                            <li class="active"><a href="#modo1" data-toggle="tab" aria-expanded="true"><i class="fa fa-magic"></i> MODO 1: Automático</a></li>
                            <li class=""><a href="#modo2" data-toggle="tab" aria-expanded="false"><i class="fa fa-upload"></i> MODO 2: Manual (Anexar)</a></li>
                            <li class=""><a href="#modo3" data-toggle="tab" aria-expanded="false"><i class="fa fa-times"></i> MODO 3: Não emitir</a></li>
                        </ul>
                        
                        <div class="tab-content" style="padding-top: 20px;">
                            <!-- MODO 1: AUTOMÁTICO -->
                            <div class="tab-pane active" id="modo1">
                                <input type="hidden" name="tipo_emissao" value="automatica" id="input_tipo_emissao">
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="well" style="background-color: #f4f6f9; border-left: 5px solid #7aa2c9;">
                                            <h4>Escolha a forma de preenchimento dos itens:</h4>
                                            
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="sub_opcao_modo1" value="1" checked onclick="toggleAutoDesc(1)">
                                                    <b>OPÇÃO 1:</b> Apenas o valor total com discriminação padrão do sistema.
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="sub_opcao_modo1" value="2" onclick="toggleAutoDesc(2)">
                                                    <b>OPÇÃO 2:</b> Preencher descrição da nota manualmente (Campo único).
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="sub_opcao_modo1" value="3" onclick="toggleAutoDesc(3)">
                                                    <b>OPÇÃO 3:</b> Agrupamento Automático (O sistema concatenará os serviços faturados).
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Discriminação dos Serviços na Nota:</label>
                                            <textarea name="descricao_agregada" id="descricao_agregada" class="form-control" rows="6" required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Valor Total da Nota (R$):</label>
                                            <input type="text" name="valor_total" id="valor_total" class="form-control" value="{{ number_format($faturamento->valorTotal, 2, ',', '.') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Número Pedido / OS (B2B):</label>
                                            <input type="text" name="numero_pedido_os" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Responsabilidade Técnica:</label>
                                            <input type="text" name="responsabilidade_tecnica" class="form-control" placeholder="Número do documento">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Documento de Referência:</label>
                                            <input type="text" name="documento_referencia" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MODO 2: MANUAL -->
                            <div class="tab-pane" id="modo2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Número da Nota Fiscal:</label>
                                            <input type="text" name="numero_nf" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Anexar PDF da Nota:</label>
                                            <input type="file" name="pdf_nota" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Observações:</label>
                                            <textarea name="obs" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MODO 3: NÃO EMITIR -->
                            <div class="tab-pane" id="modo3">
                                <div class="alert alert-warning" style="background-color: #fcf8e3 !important; color: #8a6d3b !important; border-color: #faebcc !important;">
                                    <i class="icon fa fa-warning"></i> 
                                    Atenção! Ao escolher esta opção, o sistema marcará este faturamento como "Concluído" sem a necessidade de emissão ou anexo de nota fiscal.
                                </div>
                                <div class="form-group">
                                    <label>Motivo da não emissão:</label>
                                    <textarea name="motivo_nao_emitir" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="box-footer" style="background-color: #eaeaec;">
                    <button type="submit" class="btn btn-lg btn-flat pull-right" style="background-color: #354256; color: white;">
                        <i class="fa fa-check"></i> Finalizar Emissão
                    </button>
                    <a href="{{ route('faturamentos.index') }}" class="btn btn-lg btn-flat btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Dados brutos para o JS -->
<div id="dados_servicos" style="display:none;">
    @foreach($servicos as $s)
        <div class="servico-item" data-id="{{ $s->id }}" data-nome="{{ $s->nome }}" data-unidade="{{ $s->unidade_nome }}" data-valor="{{ $s->valorFaturar }}" data-os="{{ $s->os }}"></div>
    @endforeach
</div>
@stop

@section('css')
<style>
    .nav-tabs-custom > .nav-tabs > li.active {
        border-top-color: #354256;
    }
    .nav-tabs-custom > .nav-tabs > li.active > a {
        background-color: #354256;
        color: white;
    }
    .box-header {
        border-bottom: 3px solid #7aa2c9;
    }
</style>
@stop

@section('js')
<script>
$(function() {
    // Escuta troca de tabs para atualizar o tipo_emissao
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        if(target == '#modo1') $('#input_tipo_emissao').val('automatica');
        if(target == '#modo2') $('#input_tipo_emissao').val('manual');
        if(target == '#modo3') $('#input_tipo_emissao').val('nao_emitir');
    });

    // Descrição padrão inicial
    toggleAutoDesc(1);
});

function toggleAutoDesc(opcao) {
    var descArea = $('#descricao_agregada');
    
    if(opcao == 1) {
        descArea.val("Serviços de consultoria técnica e licenciamento ambiental conforme faturamento #{{ $faturamento->id }}");
        descArea.prop('readonly', true);
    }
    else if(opcao == 2) {
        descArea.val("");
        descArea.prop('readonly', false);
        descArea.focus();
    }
    else if(opcao == 3) {
        var items = [];
        $('.servico-item').each(function() {
            var item = $(this);
            var valor = parseFloat(item.data('valor')).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
            items.push(item.data('os') + " - " + item.data('unidade') + " - " + item.data('nome') + " - " + valor);
        });
        descArea.val(items.join('\n'));
        descArea.prop('readonly', true);
    }
}
</script>
@stop
