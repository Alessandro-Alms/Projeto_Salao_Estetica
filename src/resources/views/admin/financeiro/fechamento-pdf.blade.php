<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fechamento de Caixa - {{ $dataSelecionada }}</title>
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
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #7B19E5;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #7B19E5;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
            font-size: 12px;
        }
        
        .data-hora {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            background: #7B19E5;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        
        .row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }
        
        .row:last-child {
            border-bottom: none;
        }
        
        .row.total {
            background: #f5f5f5;
            font-weight: bold;
            border-top: 2px solid #7B19E5;
            border-bottom: 2px solid #7B19E5;
        }
        
        .row.highlight {
            background: #f9f9f9;
            font-weight: bold;
        }
        
        .label {
            flex: 1;
        }
        
        .value {
            flex: 1;
            text-align: right;
            font-weight: 500;
            color: #7B19E5;
        }
        
        .row.positive .value {
            color: #00B050;
        }
        
        .row.negative .value {
            color: #E7423F;
        }
        
        .summary {
            background: #f0f0f0;
            padding: 20px;
            border-radius: 4px;
            margin-top: 25px;
            border-left: 4px solid #7B19E5;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 14px;
        }
        
        .summary-item.total {
            border-top: 2px solid #7B19E5;
            padding-top: 15px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 16px;
        }
        
        .summary-item .label {
            font-weight: 500;
        }
        
        .summary-item .value {
            font-weight: bold;
        }
        
        .positive {
            color: #00B050;
        }
        
        .negative {
            color: #E7423F;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #7B19E5;
            text-align: center;
            font-size: 11px;
            color: #999;
        }
        
        .agendamentos-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
        }
        
        .agendamentos-table th {
            background: #7B19E5;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .agendamentos-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        
        .agendamentos-table tr:nth-child(even) {
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>✧ FECHAMENTO DE CAIXA</h1>
            <p>Cheias de Charme - Salão de Beleza</p>
        </div>
        
        <!-- Data e Hora -->
        <div class="data-hora">
            <strong>Data:</strong> {{ \Carbon\Carbon::parse($dataSelecionada)->format('d/m/Y') }}
            <br>
            <strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i:s') }}
        </div>
        
        <!-- Detalhamento de Serviços -->
        @if($agendamentos->isNotEmpty())
        <div class="section">
            <div class="section-title">📋 Agendamentos Executados</div>
            <table class="agendamentos-table">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <th>Profissional</th>
                        <th>Serviço</th>
                        <th style="text-align: right;">Valor</th>
                        <th style="text-align: right;">Comissão</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agendamentos as $agendamento)
                    <tr>
                        <td>{{ $agendamento->data_hora_inicio->format('H:i') }}</td>
                        <td>{{ $agendamento->cliente->name }}</td>
                        <td>{{ $agendamento->profissional->name }}</td>
                        <td>{{ $agendamento->servico->nome }}</td>
                        <td style="text-align: right;">R$ {{ number_format($agendamento->valor_total, 2, ',', '.') }}</td>
                        <td style="text-align: right;">R$ {{ number_format($agendamento->valor_comissao, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Resumo Financeiro -->
        <div class="section">
            <div class="section-title">💰 RESUMO FINANCEIRO DO DIA</div>
            
            <div class="row highlight positive">
                <span class="label">✂️ Serviços Executados (Total Bruto)</span>
                <span class="value">R$ {{ number_format($totalServicos, 2, ',', '.') }}</span>
            </div>
            
            <div class="row highlight positive">
                <span class="label">🛍️ Produtos Vendidos (Total Bruto)</span>
                <span class="value">R$ {{ number_format($totalProdutos, 2, ',', '.') }}</span>
            </div>
            
            <div class="row highlight positive">
                <span class="label">🎁 Pacotes Vendidos (Total Bruto)</span>
                <span class="value">R$ {{ number_format($totalPacotes, 2, ',', '.') }}</span>
            </div>

            <div class="row highlight positive">
                <span class="label">⚠️ Multas de Cancelamento</span>
                <span class="value">R$ {{ number_format($totalMultas, 2, ',', '.') }}</span>
            </div>
            
            <div class="row total">
                <span class="label">📊 FATURAMENTO TOTAL</span>
                <span class="value positive">R$ {{ number_format($totalServicos + $totalProdutos + $totalPacotes + $totalMultas, 2, ',', '.') }}</span>
            </div>
            
            <div style="margin-top: 15px;"></div>
            
            <div class="row highlight negative">
                <span class="label">💸 Comissões a Pagar (Saída)</span>
                <span class="value">- R$ {{ number_format($totalComissoes, 2, ',', '.') }}</span>
            </div>
            
            <div class="row total positive">
                <span class="label">✓ LUCRO LÍQUIDO DO SALÃO</span>
                <span class="value">R$ {{ number_format($lucroLiquido, 2, ',', '.') }}</span>
            </div>
        </div>
        
        <!-- Resumo Executivo -->
        <div class="summary">
            <div class="summary-item">
                <span class="label">Total de Agendamentos:</span>
                <span class="value">{{ $agendamentos->count() }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Comissão Média por Serviço:</span>
                <span class="value">{{ $agendamentos->count() > 0 ? 'R$ ' . number_format($agendamentos->sum('valor_comissao') / $agendamentos->count(), 2, ',', '.') : 'R$ 0,00' }}</span>
            </div>
            <div class="summary-item total">
                <span class="label">Resultado do Dia:</span>
                <span class="value positive">R$ {{ number_format($lucroLiquido, 2, ',', '.') }}</span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Este documento foi gerado automaticamente pelo sistema Cheias de Charme</p>
            <p>Relatório de Fechamento de Caixa - {{ \Carbon\Carbon::parse($dataSelecionada)->format('d \\d\\e M \\d\\e Y') }}</p>
        </div>
    </div>
</body>
</html>
