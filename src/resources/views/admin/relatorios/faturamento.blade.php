<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Relatório 1: Faturamento por Período') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') }}" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                ⬅ Voltar para a Central
            </a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.faturamento') }}" class="flex items-end gap-3">
                <div>
                    <label class="block text-sm text-gray-600">Data Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border rounded p-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Data Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border rounded p-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filtrar Gráfico</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                <h3 class="text-gray-500 text-sm font-bold">Total Geral (Faturamento)</h3>
                <p class="text-3xl font-black text-green-600">R$ {{ number_format($faturamentoTotal, 2, ',', '.') }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                <h3 class="text-gray-500 text-sm font-bold">Faturamento de Serviços</h3>
                <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($totalServicos, 2, ',', '.') }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500">
                <h3 class="text-gray-500 text-sm font-bold">Faturamento de Produtos</h3>
                <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($totalProdutos, 2, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="font-bold mb-4 text-gray-700">Evolução Diária do Faturamento</h3>
            <div class="relative h-96 w-full">
                <canvas id="graficoFaturamento"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dados = @json($dadosGrafico);
            
            // Extraindo as listas para os eixos do gráfico
            const labels = dados.map(item => item.data_br);
            const dadosServicos = dados.map(item => item.servicos);
            const dadosProdutos = dados.map(item => item.produtos);

            const ctx = document.getElementById('graficoFaturamento').getContext('2d');
            new Chart(ctx, {
                type: 'line', // Gráfico de Linha para mostrar evolução de tempo
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Serviços (R$)',
                            data: dadosServicos,
                            borderColor: 'rgba(59, 130, 246, 1)', // Azul
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3 // Deixa a linha suave
                        },
                        {
                            label: 'Produtos (R$)',
                            data: dadosProdutos,
                            borderColor: 'rgba(168, 85, 247, 1)', // Roxo
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>