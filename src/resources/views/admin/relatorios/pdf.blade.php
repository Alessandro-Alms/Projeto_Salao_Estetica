<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório do Salão - Cheias de Charme</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Playfair+Display:wght@700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Syne', 'Space Grotesk', sans-serif;
            color: #1A002B;
            background: linear-gradient(135deg, #f5f0ff 0%, #fff5f8 100%);
            padding: 40px;
            margin: 0;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1);
            border: 1px solid rgba(255, 214, 244, 0.3);
            overflow: hidden;
            padding: 32px;
        }
        
        h2 {
            font-family: 'Playfair Display', serif;
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
            background: rgba(123, 25, 229, 0.1);
            display: inline-block;
            width: auto;
            margin-left: auto;
            margin-right: auto;
            padding: 6px 16px;
            border-radius: 50px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 16px;
            overflow: hidden;
        }
        
        th {
            background: linear-gradient(135deg, #7B19E5, #FF2EB6);
            color: white;
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #FFD6F4;
            color: #1A002B;
        }
        
        .text-right {
            text-align: right;
        }
        
        tbody tr:hover {
            background: rgba(123, 25, 229, 0.05);
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
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <span>✧</span>
        </div>
        <h2>Relatório de Desempenho</h2>
        <div style="text-align: center;">
            <div class="periodo">
                Período: {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Profissional</th>
                    <th class="text-right">Total de Atendimentos</th>
                    <th class="text-right">Total Gerado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($desempenhoProfissionais as $prof)
                <tr>
                    <td style="font-weight: 500;">{{ $prof->name }}</td>
                    <td class="text-right">{{ $prof->total_atendimentos }}</td>
                    <td class="text-right" style="color: #7B19E5; font-weight: 600;">R$ {{ number_format($prof->total_gerado, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="footer">
            ✧ Relatório gerado por Cheias de Charme ✧
        </div>
    </div>
</body>
</html>