<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Relatório 4: Produtos Mais Vendidos') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.produtos') }}" class="flex items-end gap-3">
                <div>
                    <label class="block text-sm text-gray-600">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border border-gray-300 rounded-lg p-2 focus:ring-pink-500 focus:border-pink-500">
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border border-gray-300 rounded-lg p-2 focus:ring-pink-500 focus:border-pink-500">
                </div>
                <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-pink-700 transition">
                    Filtrar Vendas
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            @php
                $produtoCampeao = $produtosVendidos->first();
            @endphp
            
            <div class="bg-gradient-to-br from-green-400 to-green-600 p-6 rounded-lg shadow text-white flex flex-col justify-center items-center text-center">
                <div class="text-5xl mb-2">🛍️</div>
                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Produto Mais Vendido</h3>
                @if($produtoCampeao)
                    <p class="text-2xl font-black mb-1">{{ $produtoCampeao->nome }}</p>
                    <p class="text-3xl font-bold text-green-100">{{ $produtoCampeao->total_vendido }} Unidades</p>
                    <p class="text-sm mt-2 opacity-90">Receita: R$ {{ number_format($produtoCampeao->receita_gerada, 2, ',', '.') }}</p>
                @else
                    <p class="text-lg font-bold">Nenhuma venda no período.</p>
                @endif
            </div>

            <div class="bg-white p-6 rounded-lg shadow lg:col-span-2 flex flex-col items-center">
                <h3 class="font-bold mb-4 text-gray-700 w-full text-left">Distribuição de Vendas (Top 5)</h3>
                <div class="h-64 w-full flex justify-center">
                    <canvas id="chartProdutos"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
            <h3 class="font-bold mb-4 text-gray-700">Detalhamento de Produtos</h3>
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <th class="p-3">Posição</th>
                        <th class="p-3">Produto</th>
                        <th class="p-3 text-center">Unidades Vendidas</th>
                        <th class="p-3 text-right">Receita Total Gerada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produtosVendidos as $index => $produto)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="p-3 text-gray-500 font-bold">#{{ $index + 1 }}</td>
                        <td class="p-3 font-bold text-gray-800">{{ $produto->nome }}</td>
                        <td class="p-3 text-center text-blue-600 font-bold">{{ $produto->total_vendido }}</td>
                        <td class="p-3 text-right text-green-600 font-medium">R$ {{ number_format($produto->receita_gerada, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-500">Nenhum produto vendido neste período.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Pega apenas os Top 5 para o gráfico não ficar muito poluído
            const dados = @json($produtosVendidos->take(5)); 
            
            const labels = dados.map(item => item.nome);
            const quantidades = dados.map(item => item.total_vendido);

            // Paleta de cores para o gráfico
            const cores = [
                'rgba(236, 72, 153, 0.8)', // Pink
                'rgba(59, 130, 246, 0.8)', // Blue
                'rgba(16, 185, 129, 0.8)', // Green
                'rgba(245, 158, 11, 0.8)', // Yellow
                'rgba(139, 92, 246, 0.8)'  // Purple
            ];

            new Chart(document.getElementById('chartProdutos'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Unidades Vendidas',
                        data: quantidades,
                        backgroundColor: cores,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>