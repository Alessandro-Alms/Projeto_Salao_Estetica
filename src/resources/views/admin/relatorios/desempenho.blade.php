<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Relatório 3: Desempenho por Profissional') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.desempenho') }}" class="flex items-end gap-3">
                <div>
                    <label class="block text-sm text-gray-600">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border rounded p-2">
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border rounded p-2">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Analisar Equipa</button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow lg:col-span-2">
                <h3 class="font-bold mb-4 text-gray-700">Volume de Atendimentos</h3>
                <div class="h-72">
                    <canvas id="chartDesempenho"></canvas>
                </div>
            </div>

            @php
                $melhorAvaliador = $desempenhoProfissionais->sortByDesc('media_estrelas')->first();
            @endphp
            
            <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 p-6 rounded-lg shadow text-white flex flex-col justify-center items-center text-center">
                <div class="text-5xl mb-2">🏆</div>
                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Profissional Destaque</h3>
                @if($melhorAvaliador && $melhorAvaliador->media_estrelas > 0)
                    <p class="text-2xl font-black mb-1">{{ $melhorAvaliador->name }}</p>
                    <p class="text-3xl font-bold text-yellow-100">{{ number_format($melhorAvaliador->media_estrelas, 1) }} ⭐</p>
                    <p class="text-sm mt-2 opacity-90">Baseado em {{ $melhorAvaliador->qtd_avaliacoes }} avaliações</p>
                @else
                    <p class="text-lg font-bold">Aguardando avaliações...</p>
                @endif
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
            <h3 class="font-bold mb-4 text-gray-700">Detalhamento Financeiro e Satisfação</h3>
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <th class="p-3">Profissional</th>
                        <th class="p-3 text-center">Atendimentos</th>
                        <th class="p-3 text-right">Valor Total (Salão)</th>
                        <th class="p-3 text-right">Comissão (Profissional)</th>
                        <th class="p-3 text-center">Avaliação Média</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($desempenhoProfissionais as $prof)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="p-3 font-bold text-gray-800">{{ $prof->name }}</td>
                        <td class="p-3 text-center text-blue-600 font-bold">{{ $prof->total_atendimentos }}</td>
                        <td class="p-3 text-right text-green-600 font-medium">R$ {{ number_format($prof->valor_total_gerado, 2, ',', '.') }}</td>
                        <td class="p-3 text-right text-purple-600 font-medium">R$ {{ number_format($prof->comissao_gerada, 2, ',', '.') }}</td>
                        <td class="p-3 text-center">
                            @if($prof->qtd_avaliacoes > 0)
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded font-bold text-sm">
                                    {{ number_format($prof->media_estrelas, 1) }} ⭐
                                </span>
                                <span class="text-xs text-gray-400 ml-1">({{ $prof->qtd_avaliacoes }})</span>
                            @else
                                <span class="text-gray-400 text-sm">Sem notas</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">Nenhum atendimento executado neste período.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dados = @json($desempenhoProfissionais);
            
            const labels = dados.map(item => item.name);
            const totalAtendimentos = dados.map(item => item.total_atendimentos);

            new Chart(document.getElementById('chartDesempenho'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantidade de Atendimentos',
                        data: totalAtendimentos,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)', // Azul Tailwind
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>