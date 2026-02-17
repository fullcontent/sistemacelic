<html>

<head>
    <style>
        @page {
            margin: 0cm 0cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            margin-top: 3cm;
            margin-left: 1.5cm;
            margin-right: 1.5cm;
            margin-bottom: 2cm;
            color: #333;
        }

        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
        }

        .invoice-header {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-top: 30px;
        }

        .invoice-header h2 {
            margin: 0;
            color: #000;
        }

        .invoice-info {
            margin-bottom: 20px;
            width: 100%;
        }

        .invoice-info td {
            padding: 2px 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.items th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }

        table.items td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .text-right {
            text-align: right !important;
        }

        .total-row {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }

        .signature-section {
            margin-top: 50px;
            width: 100%;
        }

        .signature-section td {
            text-align: left;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <header>
        @if($headerBase64)
            <img src="{{ $headerBase64 }}" width="100%" />
        @endif
    </header>

    <footer>
        @if($footerBase64)
            <img src="{{ $footerBase64 }}" width="100%" />
        @endif
    </footer>

    <main>
        <div class="invoice-header">
            <h2 class="text-center">RELATÓRIO DE FATURAMENTO</h2>
            <p class="text-right"><b>Data:</b> {{ \Carbon\Carbon::parse($faturamento->created_at)->format('d/m/Y') }}
            </p>
        </div>

        <table class="invoice-info">
            <tr>
                <td width="15%"><b>Descrição:</b></td>
                <td>{{ $faturamento->nome }}</td>
            </tr>
            <tr>
                <td><b>Referência:</b></td>
                <td>{{ $faturamento->obs }}</td>
            </tr>
            <tr>
                <td><b>Empresa:</b></td>
                <td>{{ $faturamento->empresa->nomeFantasia }}</td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Cód.</th>
                    <th>Loja</th>
                    <th>Cidade</th>
                    <th>CNPJ</th>
                    <th>Serviço</th>
                    <th>Valor</th>
                    <th>NF</th>
                </tr>
            </thead>
            <tbody>
                @foreach($faturamentoItens as $i)
                    <tr>
                        <td>{{ $i->detalhes->unidade->codigo }}</td>
                        <td>{{ $i->detalhes->unidade->nomeFantasia }}</td>
                        <td>{{ $i->detalhes->unidade->cidade }}/{{ $i->detalhes->unidade->uf }}</td>
                        <td>@php echo App\Http\Controllers\FaturamentoController::formatCnpjCpf($i->detalhes->unidade->cnpj); @endphp
                        </td>
                        <td>{{ $i->detalhes->nome }}</td>
                        <td>R$ {{ number_format($i->valorFaturado, 2, ',', '.') }}</td>
                        <td>{{ $i->detalhes->nf }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-row text-right">
            <b>Total:</b> R$ {{ number_format($faturamento->valorTotal, 2, ',', '.') }}
        </div>

        <div class="signature-section no-break">
            <p>__________________________________________________________________</p>
            <p><b>{{ $faturamento->dadosCastro->razaoSocial }}</b></p>
            <p>CNPJ: {{ $faturamento->dadosCastro->cnpj }}</p>
        </div>
    </main>
</body>

</html>