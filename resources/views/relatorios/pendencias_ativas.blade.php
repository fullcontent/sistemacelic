@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Filtros</h3>
            </div>
            <div class="box-body">
                <form action="{{ route('relatorio.pendencias_ativas') }}" method="GET" class="form-inline">
                    <div class="form-group">
                        <label for="status">Status: </label>
                        <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                            <option value="pendente" {{ $status == 'pendente' ? 'selected' : '' }}>Ativa (Pendente)</option>
                            <option value="concluido" {{ $status == 'concluido' ? 'selected' : '' }}>Concluída</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">Listagem de Pendências</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="relatorio-pendencias" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Unidade</th>
                                <th>Serviço</th>
                                <th>Pendência</th>
                                <th>Responsável</th>
                                <th>Vencimento</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendencias as $p)
                            <tr>
                                <td>{{ $p->servico->unidade->empresa->nomeFantasia ?? 'N/A' }}</td>
                                <td>{{ $p->servico->unidade->nomeFantasia ?? 'N/A' }} ({{ $p->servico->unidade->codigo ?? '' }})</td>
                                <td><a href="{{ route('servicos.show', $p->servico_id) }}">{{ $p->servico->nome ?? 'N/A' }}</a></td>
                                <td>{{ $p->pendencia }}</td>
                                <td>{{ $p->responsavel->name ?? 'N/A' }}</td>
                                <td>
                                    @if($p->vencimento)
                                        @php
                                            $hoje = date('Y-m-d');
                                            $classe = 'label-info';
                                            if ($p->status == 'pendente') {
                                                if ($p->vencimento < $hoje) $classe = 'label-danger';
                                                elseif ($p->vencimento == $hoje) $classe = 'label-warning';
                                                else $classe = 'label-success';
                                            }
                                        @endphp
                                        <span class="label {{ $classe }}">{{ \Carbon\Carbon::parse($p->vencimento)->format('d/m/Y') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($p->status == 'pendente')
                                        <span class="label label-warning">Ativa</span>
                                    @else
                                        <span class="label label-success">Concluída</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    {{ $pendencias->appends(['status' => $status])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(function () {
        $('#relatorio-pendencias').DataTable({
            "paging": false, // Handled by Laravel Pagination
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }
        });
    });
</script>
@stop
