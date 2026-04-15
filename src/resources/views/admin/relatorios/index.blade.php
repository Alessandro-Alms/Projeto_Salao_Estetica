<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Dashboard e Relatórios</h1>

        <div class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap justify-between items-center gap-4">
            <form method="GET" action="{{ route('admin.relatorios.index') }}" class="flex items-end gap-3">
                <div>
                    <label class="block text-sm text-gray-600">Data Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border rounded p-2">
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Data Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border rounded p-2">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filtrar</button>
            </form>

            <div class="flex gap-2">
                <a href="{{ route('admin.relatorios.exportarPdf', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    📄 Exportar PDF
                </a>
                <a href="{{ route('admin.relatorios.exportarExcel', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    📊 Exportar Excel
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                <h3 class="text-gray-500 text-sm">Faturamento (Executados)</h3>
                <p class="text-3xl font-bold text-gray-800">R$ {{ number_format($faturamentoTotal, 2, ',', '.') }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                <h3 class="text-gray-500 text-sm">Agendamentos Executados</h3>
                <p class="text-3xl font-bold text-gray-800">{{ $totalExecutados }} <span class="text-sm font-normal text-gray-400">/ {{ $totalAgendamentos }} totais</span></p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500">
                <h3 class="text-gray-500 text-sm">Taxa de Ocupação</h3>
                @php
                    $taxa = $totalAgendamentos > 0 ? ($totalExecutados / $totalAgendamentos) * 100 : 0;
                @endphp
                <p class="text-3xl font-bold text-gray-800">{{ number_format($taxa, 1, ',', '.') }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="font-bold mb-4">Atendimentos por Profissional</h3>
                <canvas id="graficoProfissionais"></canvas>
            </div>

            <div class="bg-white p-4 rounded-lg shadow overflow-auto">
                <h3 class="font-bold mb-4">Ranking de Desempenho</h3>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border-b">Profissional</th>
                            <th class="p-2 border-b text-center">Qtd. Atendimentos</th>
                            <th class="p-2 border-b text-right">Valor Gerado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($desempenhoProfissionais as $prof)
                        <tr class="hover:bg-gray-50">
                            <td class="p-2 border-b">{{ $prof->name }}</td>
                            <td class="p-2 border-b text-center">{{ $prof->total_atendimentos }}</td>
                            <td class="p-2 border-b text-right text-green-600 font-bold">R$ {{ number_format($prof->total_gerado, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        
                        @if($desempenhoProfissionais->isEmpty())
                        <tr>
                            <td colspan="3" class="p-4 text-center text-gray-500">Nenhum dado encontrado neste período.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Pega os dados que vieram do Controller e converte para o formato do Javascript
            const dadosProfissionais = @json($desempenhoProfissionais);
            
            // Separa os nomes (para a legenda) e as quantidades (para as barras)
            const labels = dadosProfissionais.map(item => item.name);
            const data = dadosProfissionais.map(item => item.total_atendimentos);

            const ctx = document.getElementById('graficoProfissionais').getContext('2d');
            new Chart(ctx, {
                type: 'bar', // Tipo do gráfico (bar, pie, line)
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantidade de Atendimentos',
                        data: data,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)', // Azul tailwind
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 } // Mostra números inteiros
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>