@extends('adminlte::page')

@section('content_header')
    <h1 style="font-weight: 700; color: #2c3e50; font-size: 24px; margin-bottom: 5px;">{{ $title ?? 'Minhas Pendências em Aberto' }}</h1>
@stop

@section('content')
<div class="table-container" style="background: #fff; border-radius: 8px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; margin-bottom: 25px;">
    <div class="table-responsive">
        <table id="lista-pendencias" class="table table-hover align-middle" style="width:100%;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Etapa / Pendência</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Serviço</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Vencimento</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Status</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px; text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendencias as $pendencia)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px;">
                            <strong style="color: #1e293b;">{{ $pendencia->etapa }}</strong>
                            <div style="font-size: 13px; color: #64748b; margin-top: 3px;">{{ $pendencia->pendencia }}</div>
                        </td>
                        <td style="padding: 12px;">
                            @if($pendencia->servico)
                                <a href="{{ route('cliente.servico.show', $pendencia->servico->id) }}" style="color: #2563eb; font-weight: 500;">
                                    {{ $pendencia->servico->nome }}
                                </a>
                                @if($pendencia->servico->os)
                                    <span style="font-size: 12px; color: #94a3b8; display: block;">OS: {{ $pendencia->servico->os }}</span>
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            @if($pendencia->vencimento)
                                @php
                                    $venc = \Carbon\Carbon::parse($pendencia->vencimento);
                                    $today = \Carbon\Carbon::today();
                                    $badgeClass = $venc->lt($today) ? 'bg-red' : ($venc->isToday() ? 'bg-yellow' : 'bg-green');
                                @endphp
                                <span class="label {{ $badgeClass }}" style="border-radius: 4px; padding: 4px 8px; font-weight: 500;">
                                    {{ $venc->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            @if(!empty($pendencia->respondida_em))
                                <span class="label label-info" style="border-radius: 4px; padding: 4px 8px;">Resposta enviada</span>
                            @else
                                <span class="label label-warning" style="border-radius: 4px; padding: 4px 8px;">{{ ucfirst($pendencia->status) }}</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="{{ route('cliente.pendencia.show', $pendencia->id) }}" class="btn btn-pill" style="background: #2563eb; color: #fff; border-radius: 50px; padding: 5px 16px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-block;">
                                Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding: 20px;">Nenhuma pendência em aberto encontrada.</td>
                    </tr>
                @endforelse
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
        $('#lista-pendencias').DataTable({
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
