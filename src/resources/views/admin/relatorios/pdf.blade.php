<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório do Salão</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        h2 { text-align: center; margin-bottom: 5px; }
        .periodo { text-align: center; color: #666; margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <h2>Relatório de Desempenho - Salão</h2>
    <div class="periodo">
        Período: {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
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
                <td>{{ $prof->name }}</td>
                <td class="text-right">{{ $prof->total_atendimentos }}</td>
                <td class="text-right">R$ {{ number_format($prof->total_gerado, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>