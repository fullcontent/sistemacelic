@extends('adminlte::page')

@section('content_header')
    <h1 style="font-weight: 700; color: #2c3e50; font-size: 24px; margin-bottom: 5px;">{{ $title ?? 'Listagem de Serviços' }}</h1>
@stop

@section('content')

<div class="table-container" style="background: #fff; border-radius: 8px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; margin-bottom: 25px;">
    <div class="table-responsive">
        <table id="lista-servicos" class="table table-hover align-middle" style="width:100%;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="font-weight: 600; color: #475569; padding: 12px;">OS</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Tipo</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Nome</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Empresa/Unidade</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Situação</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Responsável</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px; text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($servicos as $servico)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; font-weight: 600; color: #334155;">{{ $servico->os ?? '-' }}</td>
                        <td style="padding: 12px; color: #64748b;">
                            @switch($servico->tipo)
                                @case('nRenovaveis')
                                    Licenças/Projetos não renováveis
                                    @break
                                @case('licencaOperacao')
                                    Licença de Operação
                                    @break
                                @case('controleCertidoes')
                                    Certidões
                                    @break
                                @case('controleTaxas')
                                    Taxas
                                    @break
                                @case('facilitiesRealEstate')
                                    Facilities/Real Estate
                                    @break
                                @default
                                    {{ $servico->tipo }}
                            @endswitch
                        </td>
                        <td style="padding: 12px;">
                            <a href="{{ route('cliente.servico.show', $servico->id) }}" style="color: #2563eb; font-weight: 600; text-decoration: none;">
                                {{ $servico->nome }}
                            </a>
                        </td>
                        @php
                            $empresaNome = $servico->unidade ? $servico->unidade->nomeFantasia : ($servico->empresa ? $servico->empresa->nomeFantasia : '-');
                        @endphp
                        <td style="padding: 12px; color: #475569;">{{ $empresaNome }}</td>
                        <td style="padding: 12px;">
                            @switch($servico->situacao)
                                @case('andamento')
                                    @if(($servico->licenca_validade >= date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))
                                        <span class="label label-success" style="border-radius: 4px; padding: 5px 10px; font-weight: 500;">Andamento</span>
                                    @elseif(($servico->licenca_validade < date('Y-m-d')) && ($servico->tipo == 'licencaOperacao'))
                                        <span class="label label-danger" style="border-radius: 4px; padding: 5px 10px; font-weight: 500;">Vencido</span>
                                    @else
                                        <span class="label label-primary" style="border-radius: 4px; padding: 5px 10px; font-weight: 500; background-color: #0ea5e9;">Andamento</span>
                                    @endif
                                    @break
                                @case('finalizado')
                                    <span class="label label-success" style="border-radius: 4px; padding: 5px 10px; font-weight: 500; background-color: #10b981;">Finalizado</span>
                                    @break
                                @case('standBy')
                                    <span class="label label-warning" style="border-radius: 4px; padding: 5px 10px; font-weight: 500; background-color: #f59e0b;">Stand By</span>
                                    @break
                                @case('cancelado')
                                    <span class="label label-danger" style="border-radius: 4px; padding: 5px 10px; font-weight: 500; background-color: #ef4444;">Cancelado</span>
                                    @break
                                @case('arquivado')
                                    <span class="label label-default" style="border-radius: 4px; padding: 5px 10px; font-weight: 500;">Arquivado</span>
                                    @break
                                @default
                                    <span class="label label-info" style="border-radius: 4px; padding: 5px 10px; font-weight: 500;">{{ ucfirst($servico->situacao) }}</span>
                            @endswitch
                        </td>
                        <td style="padding: 12px; color: #475569;">{{ $servico->responsavel->name ?? '-' }}</td>
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