@extends('adminlte::page')

@section('title', 'Gerenciamento de NFS-e')

@section('content_header')
<h1><i class="fa fa-file-invoice"></i> Gerenciamento de Notas Fiscais (NFS-e)</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid filter-box">
            <div class="box-body">
                <form action="{{ route('nfse.index') }}" method="GET" class="row">
                    <div class="col-md-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Todos</option>
                            <option value="PROCESSANDO" {{ request('status') == 'PROCESSANDO' ? 'selected' : '' }}>
                                Processando</option>
                            <option value="CONCLUIDA" {{ request('status') == 'CONCLUIDA' ? 'selected' : '' }}>Concluída
                            </option>
                            <option value="ERRO" {{ request('status') == 'ERRO' ? 'selected' : '' }}>Erro</option>
                            <option value="CANCELADA" {{ request('status') == 'CANCELADA' ? 'selected' : '' }}>Cancelada
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Data Início</label>
                        <input type="text" name="data_inicio" class="form-control datepicker"
                            value="{{ request('data_inicio') }}" autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label>Data Fim</label>
                        <input type="text" name="data_fim" class="form-control datepicker"
                            value="{{ request('data_fim') }}" autocomplete="off">
                    </div>
                    <div class="col-md-3" style="padding-top: 25px;">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                        <a href="{{ route('nfse.index') }}" class="btn btn-default">Limpar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-body no-padding">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr style="background-color: #f4f6f9;">
                            <th># ID</th>
                            <th>Faturamento</th>
                            <th>Cliente</th>
                            <th>Status PlugNotas</th>
                            <th>Valor Total</th>
                            <th>Data</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emissions as $e)
                            <tr>
                                <td><b>{{ $e->id }}</b></td>
                                <td>
                                    @if($e->faturamento)
                                        <a href="{{ route('faturamento.show', $e->faturamento->id) }}">
                                            #{{ $e->faturamento->id }} - {{ $e->faturamento->nome }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $e->faturamento->empresa->nomeFantasia ?? '-' }}</td>
                                <td>
                                    @php
                                        $status = strtolower($e->status);
                                    @endphp
                                    @if($status == 'concluida' || $status == 'emitida' || $status == 'concluido')
                                        <span class="status-label status-concluida"><i class="fa fa-check-circle"></i>
                                            CONCLUÍDA</span>
                                    @elseif($status == 'processando')
                                        <span class="status-label status-processando"><i class="fa fa-sync fa-spin"></i>
                                            PROCESSANDO</span>
                                    @elseif($status == 'erro')
                                        <span class="status-label status-erro" title="{{ $e->mensagem_erro }}"><i
                                                class="fa fa-times-circle"></i> ERRO</span>
                                    @elseif($status == 'cancelada')
                                        <span class="status-label status-cancelada"><i class="fa fa-ban"></i> CANCELADA</span>
                                    @else
                                        <span class="status-label status-pendente"><i class="fa fa-clock"></i>
                                            {{ strtoupper($status) }}</span>
                                    @endif
                                </td>
                                <td>R$ {{ number_format($e->valor_total, 2, ',', '.') }}</td>
                                <td>{{ $e->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        @if($e->status == 'CONCLUIDA' || $e->status == 'concluido' || $e->status == 'emitida')
                                            <a href="{{ route('nfse.download.pdf', $e->id) }}" class="btn btn-default btn-xs"
                                                target="_blank" title="Ver PDF" {{ empty($e->pdf_url) ? 'disabled style=opacity:0.5' : '' }}><i class="fa fa-file-pdf"></i></a>
                                            <a href="{{ route('nfse.download.xml', $e->id) }}" class="btn btn-default btn-xs"
                                                target="_blank" title="Ver XML" {{ empty($e->xml_url) ? 'disabled style=opacity:0.5' : '' }}><i class="fa fa-code"></i></a>
                                            <button type="button" class="btn btn-danger btn-xs btn-cancelar"
                                                data-id="{{ $e->id }}" title="Cancelar Nota"><i
                                                    class="fa fa-times"></i></button>
                                        @elseif($e->status == 'PROCESSANDO' || $e->status == 'processando' || $e->status == 'pendente')
                                            <button type="button" class="btn btn-info btn-xs btn-sync" data-id="{{ $e->id }}"
                                                title="Sincronizar Status"><i class="fa fa-refresh"></i> Sincronizar</button>
                                        @elseif($e->status == 'ERRO' || $e->status == 'erro')
                                            <button type="button" class="btn btn-warning btn-xs"
                                                onclick="Swal.fire('Detalhes do Erro', '{{ addslashes($e->mensagem_erro) }}', 'error')"
                                                title="Ver Erro"><i class="fa fa-exclamation-circle"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Nenhuma nota encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($emissions->hasPages())
                <div class="box-footer clearfix">
                    {{ $emissions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .filter-box {
        border-top: 3px solid #7aa2c9;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .table>tbody>tr>td {
        vertical-align: middle;
        padding: 12px 8px;
    }

    .table>therapeutic>tr>th {
        border-bottom: 2px solid #eee;
    }

    .status-label {
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        text-transform: uppercase;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.5px;
    }

    .status-concluida {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-processando {
        background-color: #cce5ff;
        color: #004085;
        border: 1px solid #b8daff;
    }

    .status-erro {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .status-cancelada {
        background-color: #e2e3e5;
        color: #383d41;
        border: 1px solid #d6d8db;
    }

    .status-pendente {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .btn-group .btn {
        margin-right: 2px;
        border-radius: 4px !important;
    }

    .box {
        border-radius: 8px;
        overflow: hidden;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            language: 'pt-BR'
        });

        $('.btn-cancelar').on('click', function () {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Cancelar Nota Fiscal?',
                text: "Esta ação enviará um pedido de cancelamento para a PlugNotas. Informe o motivo:",
                input: 'textarea',
                inputPlaceholder: 'Digite o motivo do cancelamento...',
                inputValue: 'Cancelamento solicitado pelo usuário.',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, cancelar!',
                cancelButtonText: 'Manter nota',
                showLoaderOnConfirm: true,
                preConfirm: (motivo) => {
                    return $.ajax({
                        url: "{{ url('admin/nfse/cancelar') }}/" + id,
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            motivo: motivo
                        }
                    }).catch(error => {
                        Swal.showValidationMessage(`Erro: ${error.responseJSON.error || 'Erro interno'}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Cancelado!', 'O pedido de cancelamento foi enviado.', 'success').then(() => {
                        location.reload();
                    });
                }
            });
        });

        $('.btn-sync').on('click', function () {
            const id = $(this).data('id');
            const btn = $(this);

            btn.html('<i class="fa fa-sync fa-spin"></i>').prop('disabled', true);

            $.ajax({
                url: "{{ url('admin/nfse/sync') }}/" + id,
                method: 'POST',
                data: { _token: "{{ csrf_token() }}" },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Status atualizado: ' + response.status,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function (error) {
                    Swal.fire('Erro', 'Falha ao sincronizar: ' + (error.responseJSON.error || 'Erro interno'), 'error');
                },
                complete: function () {
                    btn.html('<i class="fa fa-refresh"></i> Sincronizar').prop('disabled', false);
                }
            });
        });

        $('#btn-sync-batch').on('click', function () {
            const btn = $(this);
            const data_inicio = $('input[name="data_inicio"]').val();
            const data_fim = $('input[name="data_fim"]').val();

            btn.html('<i class="fa fa-refresh fa-spin"></i> Sincronizando...').prop('disabled', true);

            $.ajax({
                url: "{{ route('nfse.sync_batch') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    data_inicio: data_inicio,
                    data_fim: data_fim
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (error) {
                    Swal.fire('Erro', 'Falha ao sincronizar em lote: ' + (error.responseJSON.error || 'Erro interno'), 'error');
                },
                complete: function () {
                    btn.html('<i class="fa fa-refresh"></i> Sincronizar Tudo').prop('disabled', false);
                }
            });
        });
    });
</script>
@stop