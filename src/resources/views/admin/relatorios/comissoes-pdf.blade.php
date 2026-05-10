<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Comissões</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 100%;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1);
            border: 1px solid rgba(255, 214, 244, 0.3);
            overflow: hidden;
            padding: 32px;
        }

        h2 {
            font-family: 'Arial', sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
            text-align: center;
            margin-bottom: 8px;
            color: #4A00B9;
            font-size: 28px;
        }

        .logo {
            text-align: center;
            margin-bottom: 16px;
        }

        .logo span {
            font-size: 32px;
            color: #7B19E5;
        }

        .periodo {
            text-align: center;
            color: #7B19E5;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .summary-card {
            background: linear-gradient(135deg, rgba(123, 25, 229, 0.1) 0%, rgba(255, 46, 182, 0.1) 100%);
            border: 1px solid rgba(255, 214, 244, 0.3);
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }

        .summary-card h3 {
            font-size: 12px;
            font-weight: bold;
            color: #4A00B9;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .summary-card p {
            font-size: 20px;
            font-weight: bold;
            color: #FF2EB6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
        }

        thead {
            background: linear-gradient(135deg, #7B19E5 0%, #A955F7 100%);
            color: white;
        }

        th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #FFD6F4;
        }

        tbody tr:nth-child(even) {
            background: rgba(123, 25, 229, 0.02);
        }

        tbody tr:hover {
            background: rgba(123, 25, 229, 0.05);
        }

        td {
            padding: 12px;
            font-size: 10px;
        }

        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #FFD6F4;
            padding-top: 16px;
        }

        .total-row td {
            font-weight: bold;
            background: rgba(255, 46, 182, 0.1);
            border-top: 1px solid #FFD6F4;
        }

        .money {
            text-align: right;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <span>✧</span>
        </div>
        <h2>Relatório de Comissões a Pagar</h2>
        <div style="text-align: center;">
            <div class="periodo">
                Período: {{ $dataInicio }} até {{ $dataFim }}
            </div>
        </div>

        <!-- Cartões de Resumo -->
        <div class="summary">
            <div class="summary-card">
                <h3>Total de Serviços</h3>
                <p>{{ $totalServicosRealizados }}</p>
            </div>
            <div class="summary-card">
                <h3>Total de Comissões</h3>
                <p>R$ {{ number_format($totalGeralComissoes, 2, ',', '.') }}</p>
            </div>
            <div class="summary-card">
                <h3>Profissionais</h3>
                <p>{{ $comissoes->count() }}</p>
            </div>
        </div>

        <!-- Tabela de Comissões -->
        <table>
            <thead>
                <tr>
                    <th>Profissional</th>
                    <th class="center">Serviços</th>
                    <th class="money">Receita Gerada</th>
                    <th class="money">Comissão a Pagar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($comissoes as $comissao)
                    <tr>
                        <td>{{ $comissao->name }}</td>
                        <td class="center">{{ $comissao->total_servicos }}</td>
                        <td class="money">R$ {{ number_format($comissao->receita_gerada, 2, ',', '.') }}</td>
                        <td class="money"><strong>R$ {{ number_format($comissao->comissao_a_pagar, 2, ',', '.') }}</strong></td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td class="center"><strong>{{ $comissoes->sum('total_servicos') }}</strong></td>
                    <td class="money"><strong>R$ {{ number_format($comissoes->sum('receita_gerada'), 2, ',', '.') }}</strong></td>
                    <td class="money"><strong>R$ {{ number_format($comissoes->sum('comissao_a_pagar'), 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Relatório gerado automaticamente em {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
            <p>Este documento é de caráter confidencial</p>
        </div>
    </div>
</body>
</html>
