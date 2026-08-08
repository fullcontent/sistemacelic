@extends('adminlte::page')

@section('content_header')
    <h1 style="font-weight: 700; color: #2c3e50; font-size: 24px; margin-bottom: 5px;">Listagem de Empresas</h1>
@stop

@section('content')

<div class="table-container" style="background: #fff; border-radius: 8px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; margin-bottom: 25px;">
    <div class="table-responsive">
        <table id="lista-empresas" class="table table-hover align-middle" style="width:100%;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Nome Fantasia</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">CNPJ</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Cidade/UF</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px;">Telefone</th>
                    <th style="font-weight: 600; color: #475569; padding: 12px; text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empresas as $empresa)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px;">
                            <a href="{{ route('cliente.empresa.unidades', $empresa->id) }}" style="color: #2563eb; font-weight: 600; text-decoration: none;">
                                {{ $empresa->nomeFantasia }}
                            </a>
                        </td>
                        <td style="padding: 12px; color: #64748b;">{{ $empresa->cnpj ?? '-' }}</td>
                        <td style="padding: 12px; color: #475569;">{{ $empresa->cidade }}/{{ $empresa->uf }}</td>
                        <td style="padding: 12px; color: #64748b;">{{ $empresa->telefone ?? '-' }}</td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="{{ route('cliente.empresa.unidades', $empresa->id) }}" class="btn btn-pill" style="background: #0ea5e9; color: #fff; border-radius: 50px; padding: 5px 14px; font-weight: 600; font-size: 12px; text-decoration: none; margin-right: 4px; display: inline-block;">
                                Unidades
                            </a>
                            <a href="{{ route('cliente.empresa.show', $empresa->id) }}" class="btn btn-pill" style="background: #64748b; color: #fff; border-radius: 50px; padding: 5px 14px; font-weight: 600; font-size: 12px; text-decoration: none; display: inline-block;">
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
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('#lista-empresas').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            }
        });
    });
</script>
@endsection