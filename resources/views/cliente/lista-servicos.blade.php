@extends('adminlte::page')

@section('content_header')
    <h1 style="font-weight: 700; color: #1e293b; font-size: 24px; margin-bottom: 5px;">{{ $title ?? 'Listagem de Serviços' }}</h1>
@stop

@section('content')

@php
    $isMeuAtivo = request()->boolean('meu');
    $paramsTodos = collect(request()->except('meu'))->toArray();
    $paramsMeus = array_merge($paramsTodos, ['meu' => 1]);
@endphp

<div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
    <div class="btn-group" role="group" style="border-radius: 50px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
        <a href="{{ route(Route::currentRouteName(), $paramsTodos) }}"
           class="btn {{ $isMeuAtivo ? 'btn-default' : 'btn-primary' }}"
           style="border-radius: 50px 0 0 50px; font-weight: 600; padding: 6px 18px; margin: 0;">
            Todos
        </a>
        <a href="{{ route(Route::currentRouteName(), $paramsMeus) }}"
           class="btn {{ $isMeuAtivo ? 'btn-primary' : 'btn-default' }}"
           style="border-radius: 0 50px 50px 0; font-weight: 600; padding: 6px 18px; margin: 0;">
            Meus serviços
        </a>
    </div>
</div>

<div class="table-container" style="background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; margin-bottom: 25px;">
    <div class="table-responsive">
        <table id="lista-servicos" class="table table-hover align-middle" style="width:100%;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Nome do Serviço</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Solicitante</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Empresa / Unidade</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Cód. Unidade</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Cidade</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">UF</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px; text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicos as $servico)
                    @php
                        $solicitanteNome = $servico->solicitanteServico ? $servico->solicitanteServico->nome : ($servico->solicitante ?? '-');
                        $empresaUnidade = $servico->unidade ? $servico->unidade->nomeFantasia : ($servico->empresa ? $servico->empresa->nomeFantasia : '-');
                        $codUnidade = $servico->unidade ? $servico->unidade->codigo : '-';
                        $cidade = $servico->unidade ? $servico->unidade->cidade : '-';
                        $uf = $servico->unidade ? $servico->unidade->uf : '-';
                    @endphp
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; font-weight: 700; color: #1e293b;">
                            <a href="{{ route('cliente.servico.show', $servico->id) }}" style="color: #2563eb; text-decoration: none;">
                                {{ $servico->nome }}
                            </a>
                        </td>
                        <td style="padding: 12px; color: #334155; font-weight: 500;">{{ $solicitanteNome }}</td>
                        <td style="padding: 12px; color: #475569;">{{ $empresaUnidade }}</td>
                        <td style="padding: 12px; color: #64748b; font-weight: 600;">{{ $codUnidade }}</td>
                        <td style="padding: 12px; color: #475569;">{{ $cidade }}</td>
                        <td style="padding: 12px; color: #64748b; font-weight: 600;">{{ $uf }}</td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="{{ route('cliente.servico.show', $servico->id) }}" class="btn btn-pill" style="background: #2563eb; color: #fff; border-radius: 50px; padding: 5px 16px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-block;">
                                Detalhes
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>   
    </div>
</div>

@endsection

@section('css')
<style>
    .content-header .breadcrumb { display: none !important; }
    .btn-pill {
        transition: all 0.2s ease;
    }
    .btn-pill:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.25);
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('#lista-servicos').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }
        });
    });
</script>
@endsection