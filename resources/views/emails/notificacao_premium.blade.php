<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body {
            background-color: #f4f7f6;
            color: #51545e;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }
        .wrapper {
            background-color: #f4f7f6;
            margin: 0;
            padding: 40px 0;
            width: 100%;
        }
        .content {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin: 0 auto;
            max-width: 600px;
            overflow: hidden;
            width: 100%;
        }
        .header {
            background-color: #222d32;
            padding: 30px;
            text-align: center;
        }
        .header img {
            max-width: 250px;
        }
        .body {
            padding: 40px;
        }
        .body h1 {
            color: #333333;
            font-size: 20px;
            font-weight: bold;
            margin-top: 0;
            text-align: left;
        }
        .body p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .interaction-card {
            background-color: #f9f9f9;
            border-left: 4px solid #337ab7;
            padding: 20px;
            margin-bottom: 25px;
            font-style: italic;
            border-radius: 0 4px 4px 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px 0;
            font-size: 14px;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-table td.label {
            font-weight: bold;
            width: 120px;
            color: #777;
        }
        .button-wrapper {
            text-align: center;
            padding: 20px 0;
        }
        .button {
            background-color: #337ab7;
            border-radius: 4px;
            color: #ffffff !important;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            padding: 12px 30px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #286090;
        }
        .footer {
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        .footer a {
            color: #337ab7;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="content">
            <div class="header">
                <img src="{{ config('app.url') }}/public/img/logoCelicLogin.png" alt="Sistema Celic">
            </div>
            <div class="body">
                <h1>Olá, {{ $to_name }}!</h1>
                <p>Você foi mencionado em uma nova interação no sistema.</p>

                <table class="info-table">
                    <tr>
                        <td class="label">Código:</td>
                        <td>{{ $servico->os }}</td>
                    </tr>
                    <tr>
                        <td class="label">Unidade:</td>
                        <td>{{ $servico->unidade->nomeFantasia }}</td>
                    </tr>
                    <tr>
                        <td class="label">Serviço:</td>
                        <td>{{ $servico->nome }}</td>
                    </tr>
                    <tr>
                        <td class="label">Situação:</td>
                        <td>{{ ucfirst($servico->situacao) }}</td>
                    </tr>
                </table>

                @if($interaction->ai_summary)
                    <div class="interaction-card">
                        "{{ $interaction->ai_summary }}"
                    </div>
                @endif

                <div class="button-wrapper">
                    <a href="{{ $servico->link }}" class="button">Ver Detalhes do Serviço</a>
                </div>

                <p style="font-size: 13px; color: #888;">
                    Esta é uma notificação automática do Sistema Celic. Não responda a este e-mail.
                </p>
            </div>
            <div class="footer">
                <strong>Castro Licenciamentos</strong><br>
                Consultoria e Legalização Imobiliária<br>
                <a href="https://www.sistemacelic.com">www.sistemacelic.com</a> | <a href="https://www.castroli.com.br">www.castroli.com.br</a>
            </div>
        </div>
    </div>
</body>
</html>
