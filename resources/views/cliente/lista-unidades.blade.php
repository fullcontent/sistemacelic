@extends('adminlte::page')

@section('content_header')
    <h1 style="font-weight: 700; color: #2c3e50; font-size: 24px; margin-bottom: 5px;">Listagem de Unidades</h1>
@stop

@section('content')

<!-- Legend Container -->
<div class="legend-container" style="background: #fff; border-radius: 8px; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; margin-bottom: 20px;">
    <div style="font-weight: 600; color: #475569; font-size: 13px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Legenda de Licenças</div>
    <div style="display: flex; flex-wrap: wrap; gap: 12px; font-size: 13px; color: #334155;">
        <span><strong style="color: #0f172a;">CB:</strong> AVCB</span>
        <span><strong style="color: #0f172a;">AS:</strong> Alvará Sanitário</span>
        <span><strong style="color: #0f172a;">AF:</strong> Alvará de Funcionamento</span>
        <span><strong style="color: #0f172a;">AP:</strong> Alvará de Publicidade</span>
        <span><strong style="color: #0f172a;">PC:</strong> Alvará da Polícia Civil</span>
        <span><strong style="color: #0f172a;">LA:</strong> Licença Ambiental</span>
    </div>
</div>

<div class="table-container" style="background: #fff; border-radius: 8px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; margin-bottom: 25px;">
    <div class="table-responsive">
        <table id="lista-unidades" class="table table-hover align-middle" style="width:100%;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Cód.</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Nome Fantasia</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">CNPJ</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Cidade/UF</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Licenças</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px; text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unidades as $unidade)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; font-weight: 600; color: #334155;">{{ $unidade->codigo ?? '-' }}</td>
                        <td style="padding: 12px;">
                            <a href="{{ route('cliente.unidade.show', $unidade->id) }}" style="color: #2563eb; font-weight: 600; text-decoration: none;">
                                {{ $unidade->nomeFantasia }}
                            </a>
                        </td>
                        <td style="padding: 12px; color: #64748b;">{{ $unidade->cnpj ?? '-' }}</td>
                        <td style="padding: 12px; color: #475569;">{{ $unidade->cidade }}/{{ $unidade->uf }}</td>
                        <td style="padding: 12px;">
                            @php
                                $lic = $unidade->servicos->where('tipo','licencaOperacao')->sortByDesc('created_at')->unique('nome');
                                $siglasMap = [
                                    'Alvará de Publicidade' => 'AP',
                                    'Alvará Sanitário' => 'AS',
                                    'Alvará da Polícia Civil' => 'PC',
                                    'AVCB' => 'CB',
                                    'Alvará de Funcionamento' => 'AF',
                                    'Licença Ambiental' => 'LA',
                                ];
                            @endphp
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                @foreach($lic as $l)
                                    @php
                                        $badgeBg = ($l->licenca_validade > date('Y-m-d')) ? '#10b981' : '#ef4444';
                                        $name = $siglasMap[$l->nome] ?? 'N/A';
                                    @endphp
                                    <a href="{{ route('cliente.servico.show', $l->id) }}" class="licence-badge" style="background: {{ $badgeBg }}; color: #fff; border-radius: 4px; padding: 3px 8px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-block;">
                                        {{ $name }}
                                    </a>
                                @endforeach
                            </div>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="{{ route('cliente.unidade.show', $unidade->id) }}" class="btn btn-pill" style="background: #2563eb; color: #fff; border-radius: 50px; padding: 5px 16px; font-weight: 600; font-size: 12px; text-decoration: none; display: inline-block;">
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
    .licence-badge {
        transition: opacity 0.2s ease;
    }
    .licence-badge:hover {
        opacity: 0.85;
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('#lista-unidades').DataTable({
            "paging": true,
            "lengthChange": true,
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